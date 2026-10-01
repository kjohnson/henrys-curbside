<?php

namespace App\Console\Commands;

use App\Jobs\GeocodeAvailabilityCheck;
use App\Models\AvailabilityCheck;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('availability-checks:geocode {--all : Re-geocode every check, not just ones without coordinates}')]
#[Description('Look up map coordinates for availability checks')]
class GeocodeAvailabilityChecks extends Command
{
    public function handle(): int
    {
        $checks = AvailabilityCheck::query()
            ->unless($this->option('all'), fn ($query) => $query->whereNull('latitude'))
            ->get();

        foreach ($checks as $check) {
            GeocodeAvailabilityCheck::dispatchSync($check);
        }

        $located = $checks->filter(fn (AvailabilityCheck $check) => $check->refresh()->latitude !== null)->count();
        $this->info("Geocoded {$checks->count()} check(s): {$located} located.");

        return self::SUCCESS;
    }
}
