<?php

namespace App\Filament\Portal\Resources;

use App\Actions\FetchComplains;
use App\Filament\Portal\Resources\ObservationResource\Pages;
use App\Filament\Portal\Resources\ObservationResource\Pages\ViewObservation;
use App\Filament\Portal\Resources\ObservationResource\RelationManagers;
use App\Mail\ComplaintDueProcessMail;
use App\Mail\ComplaintProcessMail;
use App\Models\Observation;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

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
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable(),
                TextColumn::make('contact_no')->sortable(),
                TextColumn::make('status'),
                TextColumn::make('location')
                    ->sortable()
                    ->label('Location')
                    ->formatStateUsing(function ($state) {
                        $jsonString = $state;

                        // Decode JSON to an associative array
                        $data = json_decode($jsonString, true);
                        $googleMapsUrl = "https://www.google.com/maps?q={$data['lat']},{$data['lng']}";

                        return "<a href='{$googleMapsUrl}' target='_blank'>{$state}</a>";
                    })
                    ->html(),
                TextColumn::make('photo')
                    ->sortable()
                    ->label('Photo')
                    ->formatStateUsing(function ($state) {
                        $jsonString = $state;

                        // Decode JSON to an associative array
                        $data = json_decode($jsonString, true);
                        $images = '';
                        foreach ($data ?? [] as $item) {
                            if ($item) {
                                $jsondecode = json_decode($item, true)[0];
                                $images .= "<img src='{$jsondecode}' target='_blank'/>";
                            }
                        }

                        return $images;
                    })
                    ->html(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                //  Tables\Actions\EditAction::make(),
                ViewAction::make()
            ])
            ->headerActions([
                Action::make('sync2')
                    ->label('Sample Process Received Email')
                    ->action(function () {
                        Mail::to('renier.trenuela@gmail.com')->bcc(['ferdzsabado@gmail.com'])->send(new ComplaintProcessMail());
                    }),
                Action::make('sync3')
                    ->label('Sample Due Process Received Email')
                    ->action(function () {
                        Mail::to('renier.trenuela@gmail.com')->bcc(['ferdzsabado@gmail.com'])->send(new ComplaintDueProcessMail());
                    }),
                Action::make('sync')
                    ->label('Sync Complain')
                    ->action(function () {
                        FetchComplains::handle();
                        redirect()->route('filament.portal.resources.observations.index');
                    }),
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
            'view' => ViewObservation::route('/{record}')
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'in_progress' => 'info',
                                'pending' => 'warning',
                                'resolved' => 'success',
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
                        TextEntry::make('description')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 4,
                            ]),
                        ImageEntry::make('photo')
                            ->columnSpanFull()->size(500),
                    ])->columnSpanFull()
            ]);
    }
}
