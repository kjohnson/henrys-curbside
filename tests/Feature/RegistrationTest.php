<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pat Neighbor',
            'email' => 'pat@example.com',
            'phone' => '(555) 555-0123',
            'street' => '123 Maple Street',
            'unit' => '',
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

    public function test_visitor_can_register_interest(): void
    {
        $this->post(route('registrations.store'), $this->validData())
            ->assertRedirect(route('home'))
            ->assertSessionHas('registered', true);

        $this->assertDatabaseHas('registrations', [
            'email' => 'pat@example.com',
            'state' => 'OH',
            'unit' => null,
        ]);

        $this->followingRedirects()->get(route('home'))->assertOk();
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
        $this->from('/')
            ->post(route('registrations.store'), [])
            ->assertRedirect('/')
            ->assertSessionHasErrors(['name', 'email', 'phone', 'street', 'city', 'state', 'postal_code'])
            ->assertSessionDoesntHaveErrors(['unit']);

        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_invalid_formats_are_rejected(): void
    {
        $this->post(route('registrations.store'), $this->validData([
            'email' => 'not-an-email',
            'phone' => 'call me',
            'state' => 'Ohio',
            'postal_code' => '4550',
        ]))->assertSessionHasErrors(['email', 'phone', 'state', 'postal_code']);
    }

    public function test_registering_twice_updates_the_existing_record(): void
    {
        $this->post(route('registrations.store'), $this->validData());
        $this->post(route('registrations.store'), $this->validData([
            'email' => 'PAT@example.com',
            'street' => '456 Oak Avenue',
        ]))->assertSessionHasNoErrors();

        $this->assertDatabaseCount('registrations', 1);
        $this->assertSame('456 Oak Avenue', Registration::first()->street);
    }

    public function test_honeypot_submissions_are_discarded(): void
    {
        $this->post(route('registrations.store'), $this->validData(['company' => 'Spam Co']))
            ->assertRedirect(route('home'))
            ->assertSessionHas('registered', true);

        $this->assertDatabaseCount('registrations', 0);
    }
}
