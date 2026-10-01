<?php

namespace App\Jobs;

use App\Models\AvailabilityCheck;
use App\Services\CensusGeocoder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Looks up the coordinates of an availability check's address for the admin map. Leaves
 * them empty when the address can't be matched.
 */
class GeocodeAvailabilityCheck implements ShouldQueue
{
    use Queueable;

    public function __construct(public AvailabilityCheck $availabilityCheck)
    {
        //
    }

    public function handle(CensusGeocoder $geocoder): void
    {
        $check = $this->availabilityCheck;
        $coordinates = $geocoder->geocode($check->street, $check->city, $check->state, $check->postal_code);

        // saveQuietly: updating the coordinates mustn't trigger another geocode.
        $check->forceFill($coordinates ?? ['latitude' => null, 'longitude' => null])->saveQuietly();
    }
}
