<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\FeedbackResource\RelationManagers\FeedbacksRelationManager;
use App\Filament\Portal\Resources\TaskResource\Pages;
use App\Models\Contractor;
use App\Models\Observation;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canDeleteAny(): bool
    {
        return Auth::user()->role == 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('observation_id')
                    ->disabledOn('edit')
                    ->required()
                    ->label('Observation')
                    ->options(Observation::where('status', 'pending')->pluck('serial', 'id'))
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
                Section::make('Related Complaint')
                    ->relationship('observation')
                    ->hiddenOn('create')
                    ->schema([
                        Grid::make()->schema([
                            Placeholder::make('Fullname')
                                ->label('Fullname')
                                ->content(fn($record) => $record->name),
                            Placeholder::make('email')
                                ->label('E-mail')
                                ->content(fn($record) => $record->email),
                            Placeholder::make('phone')
                                ->label('Phone')
                                ->content(fn($record) => $record->contact_no),
                            Placeholder::make('status')
                                ->label('Status')
                                ->content(fn($record) => $record->status),

                        ]),
                        Placeholder::make('description')
                            ->label('Description')
                            ->content(fn($record) => $record->description),
                        Placeholder::make('photo')
                            ->label('Evidences')
                            ->content(function ($record) {
                                $jsonString = $record->photo;

                                // Decode JSON to an associative array
                                $evidences = json_decode($jsonString, true);
                                return view('filament.observation-evidence', compact('evidences'));
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('observation.serial')->searchable()->sortable()
                    ->description(function (Task $record) {
                        if (Carbon::parse(Carbon::now()->subDay())->gt($record->updated_at)) {
                            return new HtmlString('<span class="text-red-800">This is due</span>');
                        }

                        return '';
                    }),
                TextColumn::make('contractor.name')->searchable()->sortable(),
                TextColumn::make('assignedBy.name')->searchable()->sortable(),
                TextColumn::make('status')->sortable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
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
            ->modifyQueryUsing(function (Builder $query) {
                if (Auth::user()->role === 'contractor') {
                    $contractor = Contractor::find(Auth::user()->entity_id);

                    return $query->where('contractor_id', $contractor->id);
                }
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FeedbacksRelationManager::class,
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
