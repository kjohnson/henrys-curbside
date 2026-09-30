<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microsite Content
    |--------------------------------------------------------------------------
    |
    | Copy for the landing page. Swap these values (or set the matching
    | environment variables) to re-use this microsite for another service.
    | Set SITE_LOGO to a path under public/ to replace the built-in logo.
    |
    */

    'name' => env('SITE_NAME', "Henry's Curbside"),

    'logo' => env('SITE_LOGO'),

    'tagline' => env('SITE_TAGLINE', 'Trash day, handled.'),

    'headline' => env('SITE_HEADLINE', 'Never drag a bin to the curb again.'),

    'intro' => env('SITE_INTRO', 'We roll your trash cans out to the curb before pickup and bring them back when the truck has gone. We are launching soon in the neighborhood — register your interest and we will reach out when a route opens on your street.'),

    'steps' => [
        ['title' => 'Register', 'body' => 'Tell us who you are and where your cans live.'],
        ['title' => 'We schedule', 'body' => 'We match your address to your local pickup day.'],
        ['title' => 'Out & back', 'body' => 'Cans go out the night before and come back after pickup.'],
    ],

    'service_area' => env('SITE_SERVICE_AREA', 'Serving Cleveland, Tennessee'),

    // Pre-filled into the address fields; visitors can still change them.
    'default_location' => [
        'city' => env('SITE_DEFAULT_CITY', 'Cleveland'),
        'state' => env('SITE_DEFAULT_STATE', 'TN'),
        'postal_code' => env('SITE_DEFAULT_POSTAL_CODE', '37312'),
    ],

    'contact_email' => env('SITE_CONTACT_EMAIL', 'hello@example.com'),

];
