<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    /**
     * @return array<string, string>
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pat Neighbor',
            'email' => 'Pat@Example.com',
            'phone' => '(555) 555-0123',
            'street' => '123 Maple Street',
            'city' => 'Springfield',
            'state' => 'oh',
            'postal_code' => '45501',
        ], $overrides);
    }

    public function test_landing_page_shows_the_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Register your interest')
            ->assertSee('name="email"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="street"', false);
    }

    public function test_address_is_prefilled_with_the_default_location(): void
    {
        $this->get('/')
            ->assertSee('value="Cleveland"', false)
            ->assertSee('value="TN"', false)
            ->assertSee('value="37312"', false)
            ->assertDontSee('name="unit"', false);
    }

    public function test_visitor_input_replaces_the_default_location_after_a_validation_error(): void
    {
        $this->from('/')
            ->post(route('registrations.store'), $this->validData(['email' => 'not-an-email']))
            ->assertRedirect('/');

        $this->get('/')
            ->assertSee('value="Springfield"', false)
            ->assertDontSee('value="Cleveland"', false);
    }

    public function test_registration_is_logged_as_info(): void
    {
        Log::spy();

        $this->post(route('registrations.store'), $this->validData())
            ->assertRedirect(route('home'))
            ->assertSessionHas('registered', true);

        Log::shouldHaveReceived('info')->once()->with('Registration received', [
            'name' => 'Pat Neighbor',
            'email' => 'pat@example.com',
            'phone' => '(555) 555-0123',
            'street' => '123 Maple Street',
            'city' => 'Springfield',
            'state' => 'OH',
            'postal_code' => '45501',
        ]);
    }

    public function test_thank_you_message_is_shown_after_registering(): void
    {
        $this->withSession(['registered' => true])
            ->get('/')
            ->assertSee("You're on the list!", false)
            ->assertDontSee('Count me in');
    }

    public function test_required_fields_are_validated(): void
    {
        Log::spy();

        $this->from('/')
            ->post(route('registrations.store'), [])
            ->assertRedirect('/')
            ->assertSessionHasErrors(['name', 'email', 'phone', 'street', 'city', 'state', 'postal_code']);

        Log::shouldNotHaveReceived('info');
    }

    public function test_invalid_formats_are_rejected(): void
    {
        Log::spy();

        $this->post(route('registrations.store'), $this->validData([
            'email' => 'not-an-email',
            'phone' => 'call me',
            'state' => 'Ohio',
            'postal_code' => '4550',
        ]))->assertSessionHasErrors(['email', 'phone', 'state', 'postal_code']);

        Log::shouldNotHaveReceived('info');
    }

    public function test_phone_numbers_are_normalized_to_us_format(): void
    {
        Log::spy();

        foreach (['423.555.0123', '4235550123', '+1 (423) 555-0123', '1-423-555-0123'] as $phone) {
            $this->post(route('registrations.store'), $this->validData(['phone' => $phone]))
                ->assertSessionHasNoErrors();
        }

        Log::shouldHaveReceived('info')->times(4)->withArgs(
            fn (string $message, array $context) => $context['phone'] === '(423) 555-0123'
        );
    }

    public function test_phone_numbers_must_be_valid_ten_digit_us_numbers(): void
    {
        foreach (['555-0123', '(423) 555-01234', '+44 20 7946 0958', '(123) 555-0123', '(423) 055-0123'] as $phone) {
            $this->post(route('registrations.store'), $this->validData(['phone' => $phone]))
                ->assertSessionHasErrors(['phone' => 'Please enter a valid 10-digit US phone number.']);
        }
    }

    public function test_honeypot_submissions_are_not_logged(): void
    {
        Log::spy();

        $this->post(route('registrations.store'), $this->validData(['company' => 'Spam Co']))
            ->assertRedirect(route('home'))
            ->assertSessionHas('registered', true);

        Log::shouldNotHaveReceived('info');
    }
}
