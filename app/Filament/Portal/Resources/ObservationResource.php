<?php

namespace App\Filament\Portal\Resources;

use App\Actions\FetchComplains;
use App\Exports\ComplaintReport;
use App\Filament\Portal\Resources\ObservationResource\Pages;
use App\Filament\Portal\Resources\ObservationResource\Pages\ViewObservation;
use App\Mail\ComplaintDueProcessMail;
use App\Mail\RejectComplainMail;
use App\Mail\ResolveComplainMail;
use App\Models\ComplainResolve;
use App\Models\Observation;
use App\Services\TwilioService;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Complaints';

    protected static ?string $modelLabel = 'Complaint';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    protected function getActions(): array
    {
        return [
            Action::make(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'rejected' => 'gray',
                    }),
                TextColumn::make('serial')->searchable()->sortable()->copyable(),
                TextColumn::make('created_at')->sortable()->dateTime(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('contact_no')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('reject')
                    ->label('Reject')
                    ->hiddenLabel()
                    ->color('danger')
                    ->icon('heroicon-o-hand-thumb-down')
                    ->action(function ($record) {
                        $record->status = 'rejected';
                        $record->save();

                        ComplainResolve::create([
                            'serial' => $record->serial,
                            'evidences' => 'rejected',
                            'approved_by' => Auth::user()->name,
                        ]);

                        Mail::to($record->email)
                            ->cc([])
                            ->bcc(['renier.trenuela@gmail.com'])
                            ->send(new RejectComplainMail($record->toArray()));
                    })
                    ->requiresConfirmation()
                    ->hidden(function ($record) {
                        return ! in_array(($record->status), ['pending']);
                    })->tooltip('Reject'),
                Action::make('resolve')
                    ->label('Resolve Now')
                    ->hiddenLabel()
                    ->icon('heroicon-o-check')
                    ->action(function ($record) {
                        $record->status = 'resolved';
                        $record->save();

                        ComplainResolve::create([
                            'serial' => $record->serial,
                            'evidences' => 'resolved',
                            'approved_by' => Auth::user()->name,
                        ]);

                        Mail::to($record->email)
                            ->cc([])
                            ->bcc(['renier.trenuela@gmail.com'])
                            ->send(new ResolveComplainMail($record->toArray()));
                    })
                    ->requiresConfirmation()
                    ->hidden(function ($record) {
                        return (($record->task->status ?? '') !== 'completed') || in_array(($record->status), ['resolved']);
                    })->tooltip('Resolve Now'),
                Action::make('location')
                    ->label('')
                    ->url(function (Observation $record) {
                        $data = json_decode($record->location, true);

                        return "https://www.google.com/maps?q={$data['lat']},{$data['lng']}";
                    })
                    ->color('warning')
                    ->icon('heroicon-o-map-pin')
                    ->openUrlInNewTab()
                    ->tooltip('Show Location'),
                ViewAction::make()
                    ->label('')
                    ->tooltip('View Details'),
            ], position: ActionsPosition::BeforeColumns)
            ->headerActions([
                Action::make('download-report')
                    ->label('Download Report')
                    ->action(function () {
                        return (new ComplaintReport)->download('Complaint Report - ' . now() . '.xls');
                    }),
                // Action::make('sms')
                //     ->label('SMS Test')
                //     ->action(function () {
                //         $twilioService = app(TwilioService::class);
                //         $twilioService->sendComplaintProcessWA('+966508624264', ['serial' => 'test']);
                //     }),
                // Action::make('sms')
                //     ->label('Test SMS')
                //     ->action(function () { 
                //         // Create an instance of the service
                //         $itexmoService = new \App\Services\ItexmoService(
                //             email: 'renier.trenuela@gmail.com',
                //             password: 'reniertrenuela9',
                //             apiCode: 'APICODEABCD1234'
                //         );

                //         // Prepare your content
                //         // +966508624264 ferdz
                //         // +966553029991 nakailan
                //         $content = [
                //             // Add your broadcast parameters here
                //             'message' => 'Hello World!',
                //             'recipients' => ['+966508614264', '09064243594']
                //             // Other required parameters
                //         ];

                //         // Send the broadcast
                //         try {
                //             $result = $itexmoService->sendBroadcast($content);
                //             // Handle successful response
                //             print_r($result);
                //         } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                //             // Handle error
                //             echo "Error: " . $e->getMessage();
                //         }
                //     }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObservations::route('/'),
            // 'create' => Pages\CreateObservation::route('/create'),
            // 'edit' => Pages\EditObservation::route('/{record}/edit'),
            'view' => ViewObservation::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    Grid::make([
                        'sm' => 1,
                        'md' => 12,
                    ])->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'in_progress' => 'info',
                                'pending' => 'warning',
                                'resolved' => 'success',
                                'rejected' => 'gray',
                            })
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 4,
                            ]),
                        TextEntry::make('name')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 4,
                            ]),
                        TextEntry::make('contact_no')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 4,
                            ]),
                        TextEntry::make('email')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 4,
                            ]),
                        TextEntry::make('description')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 4,
                            ]),
                        TextEntry::make('photo')
                            ->formatStateUsing(function ($state) {
                                $jsonString = $state;

                                // Decode JSON to an associative array
                                $evidences = json_decode($jsonString, true);

                                return view('filament.observation-evidence', compact('evidences'));
                            })
                            ->html()
                            ->columnSpanFull()
                            ->size(500),
                    ])->columnSpanFull(),
                ]),
            ]);
    }
}
