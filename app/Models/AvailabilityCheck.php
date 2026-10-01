<?php

namespace App\Models;

use Database\Factories\AvailabilityCheckFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A visitor asking whether service is available at their address. Name and address are
 * required; email and phone are optional.
 */
#[Fillable(['name', 'email', 'phone', 'street', 'unit', 'city', 'state', 'postal_code'])]
class AvailabilityCheck extends Model
{
    /** @use HasFactory<AvailabilityCheckFactory> */
    use HasFactory;
}
