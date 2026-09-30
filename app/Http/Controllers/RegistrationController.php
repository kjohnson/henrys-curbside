<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
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

        Log::info('Registration received', $request->validated());

        return to_route('home')->with('registered', true);
    }
}
