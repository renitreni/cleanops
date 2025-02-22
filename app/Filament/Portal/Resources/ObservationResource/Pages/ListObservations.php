<?php

namespace App\Filament\Portal\Resources\ObservationResource\Pages;

use App\Filament\Portal\Resources\ObservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListObservations extends ListRecords
{
    protected static string $resource = ObservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
