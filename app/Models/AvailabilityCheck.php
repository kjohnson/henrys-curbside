<?php

namespace App\Models;

use App\Jobs\GeocodeAvailabilityCheck;
use Database\Factories\AvailabilityCheckFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A visitor asking whether service is available at their address. Name and address are
 * required; email and phone are optional. The address is geocoded (latitude/longitude) for
 * the admin map whenever it's created or changed.
 */
#[Fillable(['name', 'email', 'phone', 'street', 'unit', 'city', 'state', 'postal_code'])]
class AvailabilityCheck extends Model
{
    /** @use HasFactory<AvailabilityCheckFactory> */
    use HasFactory;

    /**
     * The fields that make up the address used for geocoding.
     */
    public const ADDRESS_FIELDS = ['street', 'city', 'state', 'postal_code'];

    protected static function booted(): void
    {
        // Geocode after the response is sent, so visitors and admins don't wait on it.
        static::created(fn (AvailabilityCheck $check) => GeocodeAvailabilityCheck::dispatch($check)->afterResponse());

        static::updated(function (AvailabilityCheck $check) {
            if ($check->wasChanged(self::ADDRESS_FIELDS)) {
                GeocodeAvailabilityCheck::dispatch($check)->afterResponse();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * The phone number formatted for display, e.g. "(423) 555-0123". It's stored as digits.
     */
    public function formattedPhone(): ?string
    {
        if ($this->phone === null || strlen($this->phone) !== 10) {
            return $this->phone;
        }

        return sprintf('(%s) %s-%s', substr($this->phone, 0, 3), substr($this->phone, 3, 3), substr($this->phone, 6));
    }

    /**
     * The address on one line, e.g. "190 Church St NE, Apt 2, Cleveland, TN 37311".
     */
    public function fullAddress(): string
    {
        return trim(collect([$this->street, $this->unit, $this->city])->filter()->implode(', ')." {$this->state} {$this->postal_code}");
    }
}
