<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\ObservationResource\Pages;
use App\Filament\Portal\Resources\ObservationResource\Pages\ViewObservation;
use App\Filament\Portal\Resources\ObservationResource\RelationManagers;
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
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SelectColumn::make('status')->label('Status Update')
                    ->options([
                        'in_progress' => 'In Progress',
                        'pending' => 'Pending',
                        'resolved' => 'Resolved',
                    ]),
                TextColumn::make('location'),
                ImageColumn::make('photo'),
                TextColumn::make('reporter.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //  Tables\Actions\EditAction::make(),
                ViewAction::make()
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
                        TextEntry::make('reporter.name')
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
