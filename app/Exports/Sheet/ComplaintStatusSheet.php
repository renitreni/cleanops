<?php

namespace App\Exports\Sheet;

use App\Models\Observation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ComplaintStatusSheet implements FromQuery, WithColumnFormatting, WithColumnWidths, WithHeadings, WithStyles, WithTitle, WithMapping, WithDrawings
{
    private $observations = [];
    
    public function __construct(private string $status, private array $dateRange) {}

    public function map($row): array
    {
        // Store the observation for later use in drawings
        $this->observations[] = $row;
        
        // Decode photo JSON and count images
        $photos = $this->getPhotosFromJson($row->photo);
        $photoText = empty($photos) ? 'No Photo' : count($photos) . ' Photo(s)';
        
        return [
            $row->serial,
            $row->name,
            $row->description,
            $row->contact_no,
            $row->email,
            Carbon::parse($row->created_at)->format('F j, Y'),
            $this->diffForHumansDuration($row->pending_at, $row->resolved_at),
            $photoText, // Show count of photos
        ];
    }

    public function getPhotosFromJson($photoJson)
    {
        if (empty($photoJson)) {
            return [];
        }
        
        try {
            $photos = json_decode($photoJson, true);
            return is_array($photos) ? $photos : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function diffForHumansDuration($dateParam1, $dateParam2)
    {
        $diff = '-';
        if ($dateParam2) {
            $date1 = \Carbon\Carbon::parse($dateParam1);
            $date2 = \Carbon\Carbon::parse($dateParam2);

            $diff = $date1->diff($date2);
        }

        return $diff;
    }

    public function drawings()
    {
        $drawings = [];
        
        foreach ($this->observations as $index => $observation) {
            $photos = $this->getPhotosFromJson($observation->photo);
            
            if (!empty($photos)) {
                $columnOffset = 0; // To position multiple images horizontally
                
                foreach ($photos as $photoIndex => $photoPath) {
                    // Handle both local storage paths and URLs
                    $imagePath = $this->getImagePath($photoPath);
                    
                    if ($imagePath && $this->imageExists($photoPath, $imagePath)) {
                        $drawing = new Drawing();
                        $drawing->setName('Photo ' . $observation->serial . '_' . ($photoIndex + 1));
                        $drawing->setDescription('Complaint Photo ' . ($photoIndex + 1));
                        $drawing->setPath($imagePath);
                        
                        // Set the height and width (adjust as needed)
                        $drawing->setHeight(60);
                        $drawing->setWidth(60);
                        
                        // Position the image in column H for each row
                        // Row 2 is the first data row (after headers)
                        $drawing->setCoordinates('H' . ($index + 2));
                        
                        // Offset multiple images horizontally
                        $drawing->setOffsetX(5 + ($columnOffset * 65)); // 65px spacing between images
                        $drawing->setOffsetY(5);
                        
                        $drawings[] = $drawing;
                        $columnOffset++;
                        
                        // Limit to 3 images per row to avoid overflow
                        if ($columnOffset >= 3) {
                            break;
                        }
                    }
                }
            }
        }
        
        return $drawings;
    }

    private function getImagePath($photoPath)
    {
        // Remove any escape slashes
        $photoPath = str_replace('\/', '/', $photoPath);
        
        // If it's a URL, we need to download it first or skip it
        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            // For URLs, you might want to download them temporarily
            // For now, we'll skip URLs and only handle local files
            return null;
        }
        
        // For local storage paths
        $localPath = ltrim($photoPath, '/'); // Remove leading slash
        
        if (Storage::exists($localPath)) {
            return Storage::path($localPath);
        }
        
        // Try public path
        $publicPath = public_path($photoPath);
        if (file_exists($publicPath)) {
            return $publicPath;
        }
        
        return null;
    }

    private function imageExists($originalPath, $localPath)
    {
        if (filter_var($originalPath, FILTER_VALIDATE_URL)) {
            // For URLs, you could implement a check here
            return false; // Skip URLs for now
        }
        
        return $localPath && file_exists($localPath);
    }

    public function styles(Worksheet $sheet)
    {
        // Make header row bold and adjust row heights for images
        $styles = [
            1 => ['font' => ['bold' => true]],
        ];
        
        // Set row height for data rows to accommodate images
        for ($i = 2; $i <= count($this->observations) + 1; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(70); // Increased for multiple images
        }
        
        return $styles;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 30,
            'G' => 40,
            'H' => 200, // Wider column for multiple photos
        ];
    }

    public function headings(): array
    {
        return [
            'serial',
            'name',
            'description',
            'contact_no',
            'email',
            'created_at',
            'duration (pending -> resolved)',
            'photo', // New heading for photo column
        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return Observation::query()
            ->select([
                'serial',
                'name',
                'description',
                'contact_no',
                'email',
                'created_at',
                'pending_at',
                'resolved_at',
                'in_progress_at',
                'photo',
                // duration is NULL if resolve_at IS NULL, else the diff in seconds
                DB::raw(<<<'SQL'
                    CASE
                      WHEN resolved_at IS NULL THEN NULL
                      ELSE TIMESTAMPDIFF(SECOND, pending_at, resolved_at)
                    END as duration
                SQL)
            ])
            ->when($this->dateRange['from'], function ($q) {
                $q->whereBetween('created_at', [$this->dateRange['from'], $this->dateRange['until']]);
            })
            ->where('status', $this->status)
            ->orderBy('created_at', 'desc');
    }

    public function title(): string
    {
        return $this->status;
    }
}