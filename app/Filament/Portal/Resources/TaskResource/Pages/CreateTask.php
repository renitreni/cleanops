<?php

namespace App\Filament\Portal\Resources\TaskResource\Pages;

use App\Filament\Portal\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
}
