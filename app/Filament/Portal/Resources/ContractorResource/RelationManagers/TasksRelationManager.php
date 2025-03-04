<?php

namespace App\Filament\Portal\Resources\ContractorResource\RelationManagers;

use App\Models\Observation;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->relationship('observation')->schema([
                    Select::make('description')
                        ->label('Observation')
                        ->options(function ($record, Forms\Get $get, Forms\Set $set) {
                            // $contractor_id = $this->ownerRecord->getKey();
                            $observationIds = Task::query()->when($record, function ($q) use ($record) {
                                $q->whereNot('observation_id', $record->observation_id);
                            })->pluck('observation_id')->toArray();

                            return Observation::query()->whereNotIn('id', $observationIds)->pluck('description', 'id');
                        })
                        ->searchable(),
                ]),
                Select::make('status')
                    ->options(['complete' => 'Completed', 'rejected' => 'Rejected'])->hiddenOn('create'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contractor_id')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
                Tables\Columns\TextColumn::make('observation.description'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'assigned' => 'info',
                        'rejected' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['assigned_by'] = Auth::id();
                        $data['status'] = 'assigned';

                        Observation::query()->where('id', $data['observation_id'])->update(['status' => 'in_progress']);

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
