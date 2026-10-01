<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Geocodes US street addresses with the US Census Bureau geocoder: free, no API key, and
 * built for US addresses. https://geocoding.geo.census.gov/geocoder/
 */
class CensusGeocoder
{
    private const URL = 'https://geocoding.geo.census.gov/geocoder/locations/address';

    /**
     * @return array{latitude: float, longitude: float}|null null when the address couldn't
     *                                                        be matched or the service failed
     */
    public function geocode(string $street, string $city, string $state, ?string $zip = null): ?array
    {
        try {
            $response = Http::acceptJson()->timeout(10)->get(self::URL, array_filter([
                'street' => $street,
                'city' => $city,
                'state' => $state,
                'zip' => $zip ? substr($zip, 0, 5) : null,
                'benchmark' => 'Public_AR_Current',
                'format' => 'json',
            ]));
        } catch (ConnectionException $e) {
            Log::warning('Geocoding failed', ['reason' => $e->getMessage()]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('Geocoding failed', ['status' => $response->status()]);

            return null;
        }

        // Coordinates come back as x = longitude, y = latitude.
        $coordinates = $response->json('result.addressMatches.0.coordinates');

        if (! isset($coordinates['x'], $coordinates['y'])) {
            return null;
        }

        return ['latitude' => (float) $coordinates['y'], 'longitude' => (float) $coordinates['x']];
    }
}
