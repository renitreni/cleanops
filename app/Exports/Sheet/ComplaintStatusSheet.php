<?php

namespace App\Exports\Sheet;

use App\Models\Observation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    private $tempFiles = []; // Track temporary files for cleanup
    
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
        
        // Try to decode as JSON array
        try {
            $decoded = json_decode($photoJson, true);
            if (is_array($decoded)) {
                // Clean up each URL/path and return
                return array_map('trim', $decoded);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to decode photo JSON: " . $e->getMessage());
        }
        
        // Fallback: if it's already a string, return as single item array
        if (is_string($photoJson)) {
            return [trim($photoJson)];
        }
        
        return [];
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
        // Remove any escape slashes and trim whitespace
        $photoPath = trim(str_replace('\/', '/', $photoPath));
        
        // If it's a URL, try to download it temporarily
        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            return $this->downloadImageTemporarily($photoPath);
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

    private function downloadImageTemporarily($url)
    {
        try {
            // Log the URL we're trying to download (for debugging)
            Log::debug("Attempting to download image: " . $url);
            
            // Create a temporary file
            $tempPath = tempnam(sys_get_temp_dir(), 'excel_image_');
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            $tempPathWithExt = $tempPath . '.' . ($extension ?: 'jpg');
            
            // Use cURL for better error handling and timeout control
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // In case of SSL issues
            
            $imageContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                Log::warning("cURL error for $url: " . $curlError);
                return null;
            }
            
            if ($httpCode !== 200) {
                Log::warning("HTTP error for $url: HTTP $httpCode");
                return null;
            }
            
            if ($imageContent === false || empty($imageContent)) {
                Log::warning("Empty response for $url");
                return null;
            }
            
            // Save the content
            if (file_put_contents($tempPathWithExt, $imageContent) === false) {
                Log::warning("Failed to save image content for $url");
                return null;
            }
            
            // Verify it's a valid image
            $imageInfo = getimagesize($tempPathWithExt);
            if (!$imageInfo) {
                Log::warning("Invalid image format for $url");
                unlink($tempPathWithExt);
                return null;
            }
            
            Log::debug("Successfully downloaded image: $url -> $tempPathWithExt");
            
            // Track temp file for cleanup
            $this->tempFiles[] = $tempPathWithExt;
            
            return $tempPathWithExt;
            
        } catch (\Exception $e) {
            Log::error("Exception downloading image $url: " . $e->getMessage());
            
            // Clean up on error
            if (isset($tempPathWithExt) && file_exists($tempPathWithExt)) {
                unlink($tempPathWithExt);
            }
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
        
        return null;
    }

    private function imageExists($originalPath, $localPath)
    {
        if (filter_var($originalPath, FILTER_VALIDATE_URL)) {
            // For URLs, the localPath will be null if download failed
            return $localPath !== null && file_exists($localPath);
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
            'before', // New heading for photo column
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
    
    public function __destruct()
    {
        // Clean up temporary files
        foreach ($this->tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}