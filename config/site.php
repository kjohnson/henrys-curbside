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

    'service_area' => env('SITE_SERVICE_AREA', 'Serving Cleveland, Tennessee'),

    // The service area's city and state, pre-filled on the form (visitors can change them).
    'service_location' => [
        'city' => env('SITE_SERVICE_CITY', 'Cleveland'),
        'state' => env('SITE_SERVICE_STATE', 'TN'),
    ],

    'contact_email' => env('SITE_CONTACT_EMAIL', 'hello@example.com'),

];
