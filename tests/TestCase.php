<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    /**
     * What the faked Census geocoder answers. Defaults to "no match"; tests can replace it
     * (a later Http::fake call wouldn't override the one registered here).
     *
     * @var array<string, mixed>
     */
    protected array $geocoderResponse = ['result' => ['addressMatches' => []]];

    protected int $geocoderStatus = 200;

    protected function setUp(): void
    {
        parent::setUp();

        // Saving an availability check geocodes its address; never call real services in tests.
        Http::preventStrayRequests();
        Http::fake([
            'geocoding.geo.census.gov/*' => fn () => Http::response($this->geocoderResponse, $this->geocoderStatus),
        ]);
    }

    /**
     * Make the faked Census geocoder match an address at the given coordinates.
     */
    protected function geocodeTo(float $latitude, float $longitude): void
    {
        $this->geocoderResponse = ['result' => ['addressMatches' => [[
            'matchedAddress' => 'MATCHED ADDRESS',
            'coordinates' => ['x' => $longitude, 'y' => $latitude],
        ]]]];
    }
}
