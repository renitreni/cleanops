<?php

namespace App\Filament\Portal\Resources\TaskResource\Pages;

use App\Filament\Portal\Resources\TaskResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\EditRecord;
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
            Actions\DeleteAction::make()->hidden(fn ($record) => Auth::user()->role != 'admin'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
            ]);
    }
}
