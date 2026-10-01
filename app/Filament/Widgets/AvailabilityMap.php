<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AvailabilityChecks\AvailabilityCheckResource;
use App\Models\AvailabilityCheck;
use Filament\Widgets\Widget;

/**
 * Dashboard map with a pin for every availability check whose address has been geocoded.
 * The map itself is drawn by resources/js/admin-map.js (Leaflet + OpenStreetMap tiles).
 */
class AvailabilityMap extends Widget
{
    protected string $view = 'filament.widgets.availability-map';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    // Render with the page so the map script (loaded in the panel's <head>) can start it.
    protected static bool $isLazy = false;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $checks = AvailabilityCheck::query()->latest()->get();
        $located = $checks->whereNotNull('latitude');

        return [
            'points' => $located->map(fn (AvailabilityCheck $check) => [
                'lat' => $check->latitude,
                'lng' => $check->longitude,
                'name' => $check->name,
                'address' => $check->fullAddress(),
                'url' => AvailabilityCheckResource::getUrl('view', ['record' => $check]),
            ])->values()->all(),
            'unlocatedCount' => $checks->count() - $located->count(),
            'center' => config('site.service_location'),
        ];
    }
}
