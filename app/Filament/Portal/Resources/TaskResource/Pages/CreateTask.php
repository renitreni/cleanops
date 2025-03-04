<?php

namespace App\Filament\Portal\Resources\TaskResource\Pages;

use App\Filament\Portal\Resources\TaskResource;
use App\Models\Observation;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['assigned_by'] = Auth::id();
        $observation = Observation::find($data['observation_id']);
        $observation->status = 'in_progress';
        $observation->save();

        return $data;
    }
}
