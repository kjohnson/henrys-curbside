<?php

namespace Tests\Feature;

use App\Models\AvailabilityCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['site.service_location' => ['city' => 'Cleveland', 'state' => 'TN']]);
    }

    /**
     * @return array<string, string>
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pat Neighbor',
            'street' => '123 Maple Street',
            'unit' => '',
            'city' => 'Cleveland',
            'state' => 'TN',
            'postal_code' => '',
        ], $overrides);
    }

    public function test_landing_page_shows_the_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Register your interest')
            ->assertSee('name="name"', false)
            ->assertSee('name="street"', false)
            ->assertDontSee('name="email"', false)
            ->assertDontSee('name="phone"', false);
    }

    public function test_footer_truck_only_appears_on_the_final_thank_you_page(): void
    {
        $this->get('/')->assertDontSee('data-drive-when-visible', false);

        foreach ([false, true] as $contactSaved) {
            $this->withSession(['registered' => true, 'contact_saved' => $contactSaved])
                ->get('/')
                ->assertSee('data-drive-when-visible', false)
                ->assertDontSee('is-driving', false);
        }
    }

    public function test_thank_you_page_says_we_will_let_you_know_when_contact_details_were_left(): void
    {
        $this->withSession(['registered' => true, 'contact_saved' => true])
            ->get('/')
            ->assertSee("We'll let you know!", false)
            ->assertDontSee("You're on the list!", false);
    }

    public function test_logo_entrance_is_skipped_after_registering(): void
    {
        $this->get('/')->assertDontSee('data-logo-intro', false);

        $this->withSession(['registered' => true])
            ->get('/')
            ->assertSee('data-logo-intro="skip"', false);
    }

    public function test_logo_can_be_replayed_on_click(): void
    {
        $this->get('/')
            ->assertSee('data-logo-replay', false)
            ->assertSee('class="logo-roll-in"', false);
    }

    public function test_landing_page_links_the_favicons(): void
    {
        $this->get('/')
            ->assertSee('href="'.asset('favicon.ico').'"', false)
            ->assertSee('href="'.asset('favicon.svg').'"', false)
            ->assertSee('href="'.asset('apple-touch-icon.png').'"', false);

        foreach (['favicon.ico', 'favicon.svg', 'apple-touch-icon.png'] as $icon) {
            $this->assertFileExists(public_path($icon));
        }
    }

    public function test_address_starts_with_the_first_line(): void
    {
        $this->get('/')
            ->assertSee('<label for="street" class="block text-sm font-semibold text-brand-900">Service address</label>', false)
            ->assertSeeInOrder(['name="street"', 'name="unit"', 'name="city"', 'name="state"', 'name="postal_code"'], false)
            ->assertSee('class="address-details space-y-5"', false)
            ->assertSee('value="Cleveland"', false)
            ->assertSee('<option value="TN" selected>TN</option>', false)
            ->assertDontSee('<legend', false);
    }

    public function test_state_is_a_dropdown_of_every_state(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertMatchesRegularExpression('/<select\s[^>]*name="state"/', $html);
        $this->assertSame(51, preg_match_all('/<option value="([A-Z]{2})"[^>]*>\1<\/option>/', $html));
    }

    public function test_address_fields_stay_visible_after_a_validation_error(): void
    {
        $this->from('/')
            ->post(route('registrations.store'), $this->validData(['postal_code' => '3731']))
            ->assertRedirect('/');

        $this->get('/')
            ->assertSee('class="address-details space-y-5 is-revealed"', false)
            ->assertSee('value="123 Maple Street"', false);
    }

    public function test_a_submission_is_saved_and_asks_for_contact_details_via_a_signed_link(): void
    {
        $response = $this->post(route('registrations.store'), $this->validData(['unit' => 'Apt 2', 'postal_code' => '37311']))
            ->assertSessionHasNoErrors();

        $check = AvailabilityCheck::sole();

        // Sent to the signed contact-details step, valid for an hour.
        $location = $response->assertRedirect()->headers->get('Location');
        $this->assertStringStartsWith(route('availability-checks.contact.edit', $check), $location);
        parse_str(parse_url($location, PHP_URL_QUERY), $query);
        $this->assertArrayHasKey('signature', $query);
        $this->assertEqualsWithDelta(now()->addHour()->timestamp, (int) $query['expires'], 5);

        $this->assertDatabaseHas('availability_checks', [
            'id' => $check->id,
            'name' => 'Pat Neighbor',
            'email' => null,
            'phone' => null,
            'street' => '123 Maple Street',
            'unit' => 'Apt 2',
            'city' => 'Cleveland',
            'state' => 'TN',
            'postal_code' => '37311',
        ]);
    }

    public function test_addresses_anywhere_are_accepted(): void
    {
        $this->post(route('registrations.store'), $this->validData(['city' => 'Chattanooga', 'state' => 'TN']))
            ->assertSessionHasNoErrors();

        $check = AvailabilityCheck::sole();
        $this->assertSame(['Chattanooga', 'TN'], [$check->city, $check->state]);
    }

    public function test_thank_you_message_is_shown_after_registering(): void
    {
        $this->withSession(['registered' => true])
            ->get('/')
            ->assertSee("You're on the list!", false)
            ->assertDontSee('Count me in');
    }

    public function test_intro_is_hidden_on_mobile_only_after_registering(): void
    {
        $this->get('/')->assertSee('<header class="mb-10">', false);

        $this->withSession(['registered' => true])
            ->get('/')
            ->assertSee('<header class="mb-10 hidden lg:block">', false);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->from('/')
            ->post(route('registrations.store'), [])
            ->assertRedirect('/')
            ->assertSessionHasErrors(['name', 'street', 'city', 'state'])
            ->assertSessionDoesntHaveErrors(['unit', 'postal_code']);

        $this->assertDatabaseCount('availability_checks', 0);
    }

    public function test_required_message_uses_the_field_label(): void
    {
        $this->post(route('registrations.store'), $this->validData(['street' => '']))
            ->assertSessionHasErrors(['street' => 'The service address field is required.']);
    }

    public function test_state_must_be_a_us_state(): void
    {
        $this->post(route('registrations.store'), $this->validData(['state' => 'XX']))
            ->assertSessionHasErrors(['state' => 'Please choose a state.']);
    }

    public function test_fields_are_not_labelled_required_or_optional(): void
    {
        $this->get('/')
            ->assertDontSee('(optional)')
            ->assertDontSee('(required)')
            ->assertDontSee('*</label>', false);
    }

    public function test_zip_codes_must_be_valid(): void
    {
        $this->post(route('registrations.store'), $this->validData(['postal_code' => '3731']))
            ->assertSessionHasErrors(['postal_code' => 'Please enter a valid ZIP code.']);

        $this->assertDatabaseCount('availability_checks', 0);
    }

    public function test_honeypot_submissions_are_not_saved(): void
    {
        $this->post(route('registrations.store'), $this->validData(['company' => 'Spam Co']))
            ->assertRedirect(route('home'))
            ->assertSessionHas('registered', true);

        $this->assertDatabaseCount('availability_checks', 0);
    }
}
