<?php

namespace App\Filament\Portal\Resources\ContractorResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->unique()
                    ->email()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->copyable(),
                Tables\Columns\TextColumn::make('email')->copyable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Create User')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->unique()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->required()
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->required(fn($livewire) => $livewire instanceof CreateRecord)
                            ->maxLength(255)
                            ->hiddenOn('edit'),
                    ])
                    ->action(function (array $data, array $arguments): void {
                        // Create
                        $feedback = new User();
                        $feedback->entity_id = $this->ownerRecord->id;
                        $feedback->role = 'contractor';
                        $feedback->name = $data['name'];
                        $feedback->email = $data['email'];
                        $feedback->password = $data['password'];
                        $feedback->save();
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('change-password')
                    ->label('Change Password')
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->required()
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->hiddenOn('edit'),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->password = $data['password'];
                        $record->save();
                    }),
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
