<?php

namespace App\Filament\Portal\Resources\FeedbackResource\RelationManagers;

use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class FeedbacksRelationManager extends RelationManager
{
    protected static string $relationship = 'feedbacks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('comments')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('comments')
            ->columns([
                Tables\Columns\TextColumn::make('comments')->grow(),
                Tables\Columns\TextColumn::make('reviewer.name')->label('Comment by'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Add Comment')
                    ->form([
                        Textarea::make('comments')->required(),
                    ])
                    ->action(function (array $data, array $arguments): void {
                        // Create
                        $feedback = new Feedback;
                        $feedback->task_id = $this->ownerRecord->id;
                        $feedback->comments = $data['comments'];
                        $feedback->reviewer_id = Auth::user()->id;
                        $feedback->save();
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
