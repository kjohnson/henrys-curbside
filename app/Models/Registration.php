<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'street', 'unit', 'city', 'state', 'postal_code'])]
class Registration extends Model
{
    //
}
