<?php

namespace Tests\Feature;

use App\Filament\Resources\AvailabilityChecks\AvailabilityCheckResource;
use App\Filament\Resources\AvailabilityChecks\Pages\EditAvailabilityCheck;
use App\Filament\Resources\AvailabilityChecks\Pages\ListAvailabilityChecks;
use App\Filament\Widgets\AvailabilityMap;
use App\Jobs\GeocodeAvailabilityCheck;
use App\Models\AvailabilityCheck;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    private function signIn(): void
    {
        $this->actingAs(User::factory()->create());
    }

    /**
     * Pin a check to the map by hand. Its automatic geocode is faked out, since that would
     * otherwise run after the next request and replace these coordinates.
     */
    private function locate(AvailabilityCheck $check, float $latitude, float $longitude): void
    {
        $check->forceFill(['latitude' => $latitude, 'longitude' => $longitude])->saveQuietly();
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get(AvailabilityCheckResource::getUrl('index'))->assertRedirect('/admin/login');
    }

    public function test_the_dashboard_shows_the_map_with_a_pin_for_each_located_check(): void
    {
        Bus::fake([GeocodeAvailabilityCheck::class]);
        $this->signIn();
        $located = AvailabilityCheck::factory()->create(['name' => 'Pinned Person']);
        $this->locate($located, 35.16, -84.87);
        AvailabilityCheck::factory()->create(['name' => 'Unmatched Person']);

        $this->get('/admin')
            ->assertOk()
            ->assertSee('admin-map', false) // the Leaflet bundle is loaded
            ->assertSee('1 on the map, 1 not located');

        Livewire::test(AvailabilityMap::class)
            ->assertViewHas('points', fn (array $points) => count($points) === 1
                && $points[0]['name'] === 'Pinned Person'
                && $points[0]['lat'] === 35.16
                && $points[0]['url'] === AvailabilityCheckResource::getUrl('view', ['record' => $located]))
            ->assertViewHas('unlocatedCount', 1);
    }

    public function test_the_list_shows_checks_with_formatted_phone_numbers(): void
    {
        $this->signIn();
        $checks = AvailabilityCheck::factory()->count(3)->create();
        $checks->first()->update(['phone' => '4235550123']);

        Livewire::test(ListAvailabilityChecks::class)
            ->assertCanSeeTableRecords($checks)
            ->assertSee('(423) 555-0123');
    }

    public function test_the_list_can_be_searched_by_address_and_filtered_by_map_status(): void
    {
        Bus::fake([GeocodeAvailabilityCheck::class]);
        $this->signIn();
        $hillview = AvailabilityCheck::factory()->create(['street' => '240 Hillview Dr NW']);
        $church = AvailabilityCheck::factory()->create(['street' => '190 Church St NE']);
        $this->locate($church, 35.16, -84.87);

        Livewire::test(ListAvailabilityChecks::class)
            ->searchTable('Hillview')
            ->assertCanSeeTableRecords([$hillview])
            ->assertCanNotSeeTableRecords([$church]);

        Livewire::test(ListAvailabilityChecks::class)
            ->filterTable('located', true)
            ->assertCanSeeTableRecords([$church])
            ->assertCanNotSeeTableRecords([$hillview]);
    }

    public function test_the_view_page_shows_the_contact_details_and_address(): void
    {
        $this->signIn();
        $check = AvailabilityCheck::factory()->create(['street' => '190 Church St NE', 'unit' => 'Apt 2', 'phone' => '4235550123', 'postal_code' => '37311']);

        $this->get(AvailabilityCheckResource::getUrl('view', ['record' => $check]))
            ->assertOk()
            ->assertSee('190 Church St NE, Apt 2, Cleveland TN 37311')
            ->assertSee('(423) 555-0123')
            ->assertSee('Not located');
    }

    public function test_editing_stores_the_phone_as_digits_and_re_geocodes_a_changed_address(): void
    {
        $this->signIn();
        $check = AvailabilityCheck::factory()->create(['phone' => '4235550123']);
        $this->geocodeTo(35.2286, -84.8483);

        Livewire::test(EditAvailabilityCheck::class, ['record' => $check->getRouteKey()])
            ->assertSchemaStateSet(['phone' => '(423) 555-0123'])
            ->fillForm(['phone' => '(423) 555-0199', 'street' => '240 Hillview Dr NW'])
            ->call('save')
            ->assertHasNoFormErrors();

        $check->refresh();
        $this->assertSame('4235550199', $check->phone);
        $this->assertSame('240 Hillview Dr NW', $check->street);

        // The geocode runs after the response; run the app's terminating callbacks to finish it.
        $this->app->terminate();
        $this->assertEqualsWithDelta(35.2286, $check->refresh()->latitude, 0.0001);
    }
}
