<?php

namespace App\Filament\Portal\Resources\TaskResource\Pages;

use App\Filament\Portal\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    public static function canDeleteAny(): bool
    {
        return Auth::user()->role == 'admin';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->hidden(fn($record) => Auth::user()->role != 'admin'),
        ];
    }
}
