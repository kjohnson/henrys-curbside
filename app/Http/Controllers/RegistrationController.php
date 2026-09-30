<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
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
     * Record a new registration of interest.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        // Bots fill the hidden "company" field; accept silently so they get no signal.
        if ($request->filled('company')) {
            return to_route('home')->with('registered', true);
        }

        // Re-registering with the same email updates the existing record rather than
        // erroring, so the form never reveals whether an address is already on the list.
        Registration::updateOrCreate(
            ['email' => $request->validated('email')],
            $request->safe()->except('email'),
        );

        return to_route('home')->with('registered', true);
    }
}
