<?php

namespace Tests\Feature;

use App\Models\AvailabilityCheck;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_check_with_contact_details(): void
    {
        $check = AvailabilityCheck::factory()->create();

        $this->assertDatabaseHas('availability_checks', [
            'id' => $check->id,
            'name' => $check->name,
            'email' => $check->email,
            'phone' => $check->phone,
            'state' => 'TN',
            'postal_code' => '37312',
        ]);
    }

    public function test_email_and_phone_are_optional(): void
    {
        $check = AvailabilityCheck::factory()->withoutContact()->create();

        $this->assertDatabaseHas('availability_checks', [
            'id' => $check->id,
            'email' => null,
            'phone' => null,
        ]);
    }

    public function test_zip_code_is_optional(): void
    {
        $check = AvailabilityCheck::factory()->create(['postal_code' => null]);

        $this->assertDatabaseHas('availability_checks', ['id' => $check->id, 'postal_code' => null]);
    }

    public function test_name_and_address_are_required(): void
    {
        foreach (['name', 'street', 'city', 'state'] as $column) {
            try {
                AvailabilityCheck::factory()->create([$column => null]);
                $this->fail("Expected {$column} to be required.");
            } catch (QueryException $e) {
                $this->assertStringContainsString('NOT NULL', $e->getMessage());
            }
        }

        $this->assertDatabaseCount('availability_checks', 0);
    }
}
