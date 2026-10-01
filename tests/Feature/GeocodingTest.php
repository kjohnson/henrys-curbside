<?php

namespace Tests\Feature;

use App\Jobs\GeocodeAvailabilityCheck;
use App\Models\AvailabilityCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_job_stores_the_matched_coordinates(): void
    {
        $this->geocodeTo(35.160023, -84.874291);
        $check = AvailabilityCheck::factory()->create(['street' => '190 Church St NE', 'city' => 'Cleveland', 'state' => 'TN', 'postal_code' => '37311']);

        GeocodeAvailabilityCheck::dispatchSync($check);

        $check->refresh();
        $this->assertEqualsWithDelta(35.160023, $check->latitude, 0.000001);
        $this->assertEqualsWithDelta(-84.874291, $check->longitude, 0.000001);

        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'geocoding.geo.census.gov')
            && $request['street'] === '190 Church St NE'
            && $request['city'] === 'Cleveland'
            && $request['state'] === 'TN'
            && $request['zip'] === '37311');
    }

    public function test_an_unmatched_address_has_no_coordinates(): void
    {
        $check = AvailabilityCheck::factory()->create(['latitude' => 1, 'longitude' => 1]);

        GeocodeAvailabilityCheck::dispatchSync($check);

        $this->assertNull($check->refresh()->latitude);
    }

    public function test_a_geocoder_outage_leaves_no_coordinates_without_failing(): void
    {
        $this->geocoderStatus = 500;
        $check = AvailabilityCheck::factory()->create();

        GeocodeAvailabilityCheck::dispatchSync($check);

        $this->assertNull($check->refresh()->latitude);
    }

    public function test_new_checks_and_address_changes_are_geocoded_after_the_response(): void
    {
        Bus::fake();

        $check = AvailabilityCheck::factory()->create();
        Bus::assertDispatchedAfterResponse(GeocodeAvailabilityCheck::class, 1);

        $check->update(['street' => '1 New St']);
        Bus::assertDispatchedAfterResponse(GeocodeAvailabilityCheck::class, 2);

        $check->update(['name' => 'Someone Else', 'email' => 'new@example.com']);
        Bus::assertDispatchedAfterResponse(GeocodeAvailabilityCheck::class, 2);
    }

    public function test_the_command_geocodes_checks_without_coordinates(): void
    {
        $missing = AvailabilityCheck::factory()->create();
        $located = AvailabilityCheck::factory()->create();
        $located->forceFill(['latitude' => 10, 'longitude' => 20])->saveQuietly();

        $this->geocodeTo(35.16, -84.87);

        $this->artisan('availability-checks:geocode')
            ->expectsOutputToContain('Geocoded 1 check(s): 1 located.')
            ->assertSuccessful();

        $this->assertEqualsWithDelta(35.16, $missing->refresh()->latitude, 0.0001);
        $this->assertEqualsWithDelta(10, $located->refresh()->latitude, 0.0001);

        $this->artisan('availability-checks:geocode --all')
            ->expectsOutputToContain('Geocoded 2 check(s): 2 located.');
    }
}
