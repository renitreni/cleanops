<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\TaskResource\Pages;
use App\Models\Contractor;
use App\Models\Observation;
use App\Models\Task;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('observation_id')
                    ->required()
                    ->label('Observation')
                    ->options(Observation::all()->pluck('serial', 'id'))
                    ->searchable(),
                Select::make('contractor_id')
                    ->required()
                    ->label('Contractor')
                    ->options(Contractor::all()->pluck('name', 'id'))
                    ->searchable(),
                Select::make('status')
                    ->label('Task Status')
                    ->required()
                    ->options([
                        'assigned' => 'Assigned',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                    ]),
                FileUpload::make('completion_photo'),
                Grid::make()
                    ->relationship('assignedBy')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('name')
                            ->label('Assigned By')
                            ->options(User::all()->pluck('name', 'id'))
                            ->default(Auth::user()->name)
                            ->disabled()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('observation.serial')->sortable(),
                TextColumn::make('contractor.name')->sortable(),
                TextColumn::make('status')->sortable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'success',
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
