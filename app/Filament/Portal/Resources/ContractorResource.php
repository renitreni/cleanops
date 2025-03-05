<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\ContractorResource\Pages;
use App\Filament\Portal\Resources\ContractorResource\RelationManagers\TasksRelationManager;
use App\Filament\Portal\Resources\ContractorResource\RelationManagers\UsersRelationManager;
use App\Models\Contractor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ContractorResource extends Resource
{
    protected static ?string $model = Contractor::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    
    public static function canAccess(): bool
    {
        return Auth::user()->role == 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Company Name'),
                TextInput::make('contact_person'),
                TextInput::make('phone'),
                TextInput::make('email'),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Company name'),
                TextColumn::make('contact_person'),
                TextColumn::make('phone'),
                TextColumn::make('email'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //   TasksRelationManager::class
            UsersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractors::route('/'),
            'create' => Pages\CreateContractor::route('/create'),
            'edit' => Pages\EditContractor::route('/{record}/edit'),
        ];
    }
}
