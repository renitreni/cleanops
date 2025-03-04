<?php

namespace App\Filament\Portal\Resources\UserResource\Pages;

use App\Filament\Portal\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
