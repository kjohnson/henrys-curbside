<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateContactRequest;
use App\Models\AvailabilityCheck;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

/**
 * The second step: optional email and phone for an availability check. Both routes sit
 * behind the signed, one-hour URL issued when the check was created, so only the visitor
 * who submitted it can update it.
 */
class AvailabilityCheckContactController extends Controller
{
    /**
     * Show the contact details form.
     */
    public function edit(Request $request, AvailabilityCheck $availabilityCheck): View
    {
        // The form posts to the same address under the same expiry, so the hour runs from
        // when the check was created, not from when this page was opened.
        $expires = $request->query('expires') ? Carbon::createFromTimestamp($request->query('expires')) : now()->addHour();

        return view('landing', [
            'contactFor' => $availabilityCheck,
            'contactUrl' => URL::temporarySignedRoute('availability-checks.contact.update', $expires, $availabilityCheck),
        ]);
    }

    /**
     * Save the email and phone, unless the visitor chose to skip. The thank-you page says
     * "We'll let you know!" only when they left a way to reach them.
     */
    public function update(UpdateContactRequest $request, AvailabilityCheck $availabilityCheck): RedirectResponse
    {
        $saved = false;

        if (! $request->boolean('skip')) {
            $availabilityCheck->update($request->validated());
            $saved = $availabilityCheck->email !== null || $availabilityCheck->phone !== null;
        }

        return to_route('home')->with(['registered' => true, 'contact_saved' => $saved]);
    }
}
