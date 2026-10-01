<?php

namespace Tests\Feature;

use App\Models\AvailabilityCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AvailabilityCheckContactTest extends TestCase
{
    use RefreshDatabase;

    private AvailabilityCheck $check;

    protected function setUp(): void
    {
        parent::setUp();

        $this->check = AvailabilityCheck::factory()->withoutContact()->create(['street' => '190 Church St NE']);
    }

    private function editUrl(?AvailabilityCheck $check = null, int $minutes = 60): string
    {
        return URL::temporarySignedRoute('availability-checks.contact.edit', now()->addMinutes($minutes), $check ?? $this->check);
    }

    private function updateUrl(?AvailabilityCheck $check = null, int $minutes = 60): string
    {
        return URL::temporarySignedRoute('availability-checks.contact.update', now()->addMinutes($minutes), $check ?? $this->check);
    }

    public function test_the_signed_link_shows_the_contact_form(): void
    {
        $this->get($this->editUrl())
            ->assertOk()
            // Doubles as the first form's success message, then asks for more.
            ->assertSeeInOrder(["You're on the list!", 'Thanks for your interest in service at 190 Church St NE.', 'How should we reach you?'], false)
            ->assertSee('name="email"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('data-mask="us-phone"', false)
            ->assertDontSee('name="street"', false)
            ->assertDontSee('data-drive-when-visible', false)
            ->assertSee('data-logo-intro="skip"', false);
    }

    public function test_the_form_posts_to_a_signed_url_with_the_same_expiry(): void
    {
        $editUrl = $this->editUrl(minutes: 45);
        parse_str(parse_url($editUrl, PHP_URL_QUERY), $editQuery);

        preg_match('/<form method="POST" action="([^"]+)"/', $this->get($editUrl)->getContent(), $match);
        $action = html_entity_decode($match[1]);
        parse_str(parse_url($action, PHP_URL_QUERY), $actionQuery);

        $this->assertStringStartsWith(route('availability-checks.contact.update', $this->check), $action);
        $this->assertArrayHasKey('signature', $actionQuery);
        $this->assertSame($editQuery['expires'], $actionQuery['expires']);
    }

    public function test_contact_details_are_saved_and_normalized(): void
    {
        $this->put($this->updateUrl(), ['email' => ' Pat@Example.com ', 'phone' => '+1 423.555.0123'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'))
            ->assertSessionHas('registered', true)
            ->assertSessionHas('contact_saved', true);

        $this->check->refresh();
        $this->assertSame('pat@example.com', $this->check->email);
        $this->assertSame('4235550123', $this->check->phone);
    }

    public function test_phone_numbers_are_stored_as_ten_digits_however_they_are_typed(): void
    {
        foreach (['(423) 555-0123', '423.555.0123', '4235550123', '+1 (423) 555-0123', '1-423-555-0123'] as $phone) {
            $this->put($this->updateUrl(), ['phone' => $phone])->assertSessionHasNoErrors();

            $this->assertSame('4235550123', $this->check->refresh()->phone, "from {$phone}");
        }
    }

    public function test_email_and_phone_are_optional(): void
    {
        $this->put($this->updateUrl(), ['email' => '', 'phone' => '423-555-0123'])
            ->assertSessionHasNoErrors();

        $this->check->refresh();
        $this->assertNull($this->check->email);
        $this->assertSame('4235550123', $this->check->phone);
    }

    public function test_skipping_leaves_the_record_as_is_even_with_invalid_input(): void
    {
        $this->put($this->updateUrl(), ['email' => 'not-an-email', 'phone' => '', 'skip' => '1'])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'))
            ->assertSessionHas('registered', true)
            ->assertSessionHas('contact_saved', false);

        $this->check->refresh();
        $this->assertNull($this->check->email);
        $this->assertNull($this->check->phone);
    }

    public function test_saving_with_both_fields_empty_counts_as_not_leaving_contact_details(): void
    {
        $this->put($this->updateUrl(), ['email' => '', 'phone' => ''])
            ->assertSessionHas('contact_saved', false);
    }

    public function test_clearing_a_field_on_a_later_save_removes_it(): void
    {
        $this->check->update(['email' => 'old@example.com', 'phone' => '4235550123']);

        $this->put($this->updateUrl(), ['email' => 'old@example.com', 'phone' => '']);

        $this->assertNull($this->check->refresh()->phone);
    }

    public function test_invalid_details_are_rejected_and_shown_on_the_form(): void
    {
        $editUrl = $this->editUrl();

        $this->from($editUrl)
            ->put($this->updateUrl(), ['email' => 'not-an-email', 'phone' => 'call me'])
            ->assertRedirect($editUrl)
            ->assertSessionHasErrors(['email', 'phone' => 'Please enter a valid 10-digit US phone number.']);

        $this->get($editUrl)->assertSee('value="not-an-email"', false);
        $this->assertNull($this->check->refresh()->email);
    }

    public function test_phone_numbers_must_be_valid_ten_digit_us_numbers(): void
    {
        foreach (['555-0123', '(423) 555-01234', '+44 20 7946 0958', '(123) 555-0123', '(423) 055-0123'] as $phone) {
            $this->put($this->updateUrl(), ['phone' => $phone])
                ->assertSessionHasErrors(['phone' => 'Please enter a valid 10-digit US phone number.']);
        }
    }

    public function test_unsigned_links_cannot_view_or_update_the_check(): void
    {
        $this->get(route('availability-checks.contact.edit', $this->check))->assertRedirect(route('home'));
        $this->put(route('availability-checks.contact.update', $this->check), ['email' => 'x@example.com'])->assertRedirect(route('home'));

        $this->assertNull($this->check->refresh()->email);
    }

    public function test_a_link_for_one_check_cannot_update_another(): void
    {
        $other = AvailabilityCheck::factory()->withoutContact()->create();
        $forged = str_replace("/availability-checks/{$this->check->id}/", "/availability-checks/{$other->id}/", $this->updateUrl());

        $this->put($forged, ['email' => 'x@example.com'])->assertRedirect(route('home'));

        $this->assertNull($other->refresh()->email);
    }

    public function test_links_expire_after_an_hour(): void
    {
        $editUrl = $this->editUrl();
        $updateUrl = $this->updateUrl();

        $this->travel(61)->minutes();

        $this->get($editUrl)->assertRedirect(route('home'));
        $this->put($updateUrl, ['email' => 'x@example.com'])->assertRedirect(route('home'));

        $this->assertNull($this->check->refresh()->email);
    }
}
