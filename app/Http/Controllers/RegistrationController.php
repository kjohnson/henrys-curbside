<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\AvailabilityCheck;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Show the landing page and interest form.
     */
    public function create(): View
    {
        return view('landing');
    }

    /**
     * Record a new availability check, then ask for contact details on a second step
     * reached through a signed link that only this visitor holds, valid for an hour.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        // Bots fill the hidden "company" field; accept silently so they get no signal.
        if ($request->filled('company')) {
            return to_route('home')->with('registered', true);
        }

        $check = AvailabilityCheck::create($request->validated());

        Log::info('Availability check received', ['id' => $check->id]);

        return redirect(URL::temporarySignedRoute('availability-checks.contact.edit', now()->addHour(), $check));
    }
}
