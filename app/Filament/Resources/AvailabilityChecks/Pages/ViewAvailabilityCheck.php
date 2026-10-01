<?php

namespace App\Filament\Resources\AvailabilityChecks\Pages;

use App\Filament\Resources\AvailabilityChecks\AvailabilityCheckResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAvailabilityCheck extends ViewRecord
{
    protected static string $resource = AvailabilityCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
