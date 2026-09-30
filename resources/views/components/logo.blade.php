{{-- Henry's Curbside badge: a wheeled bin on the curb, circled by out-and-back arrows. --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 400 400', 'role' => 'img', 'xmlns' => 'http://www.w3.org/2000/svg']) }} aria-labelledby="logo-title">
    <title id="logo-title">{{ config('site.name') }}</title>
    <defs>
        <path id="logo-arc-top" d="M 48 200 A 152 152 0 0 1 352 200" />
        <path id="logo-arc-bottom" d="M 36 200 A 164 164 0 0 0 364 200" />
        <marker id="logo-arrowhead" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="3.2" markerHeight="3.2" orient="auto">
            <path class="fill-accent" d="M 0 0 L 10 5 L 0 10 z" />
        </marker>
    </defs>

    {{-- Badge --}}
    <circle class="fill-mist" cx="200" cy="200" r="196" />
    <circle class="fill-brand-700" cx="200" cy="200" r="188" />
    <circle class="stroke-mist" cx="200" cy="200" r="178" fill="none" stroke-width="2" stroke-dasharray="2 6" opacity="0.6" />

    {{-- Wordmark --}}
    <text class="fill-mist" font-family="'Instrument Sans', 'Arial Black', Helvetica, sans-serif" font-weight="700" font-size="34" letter-spacing="6">
        <textPath href="#logo-arc-top" startOffset="50%" text-anchor="middle">HENRY'S</textPath>
    </text>
    <text class="fill-accent" font-family="'Instrument Sans', 'Arial Black', Helvetica, sans-serif" font-weight="700" font-size="26" letter-spacing="7">
        <textPath href="#logo-arc-bottom" startOffset="50%" text-anchor="middle">CURBSIDE</textPath>
    </text>
    <circle class="fill-accent" cx="42" cy="200" r="5" />
    <circle class="fill-accent" cx="358" cy="200" r="5" />

    {{-- Out-and-back arrows --}}
    <g class="stroke-accent" fill="none" stroke-width="9" stroke-linecap="round" marker-end="url(#logo-arrowhead)">
        <path d="M 84 145.9 A 128 128 0 0 1 294 120.6" />
        <path d="M 316 254.1 A 128 128 0 0 1 106 279.4" />
    </g>

    {{-- Curb --}}
    <path class="stroke-mist" d="M 128 280 H 236 V 292 H 262" fill="none" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />

    {{-- Wheeled bin --}}
    <path class="fill-accent" d="M 214 138 h 22 a 4 4 0 0 1 4 4 v 8 h -30 v -8 a 4 4 0 0 1 4 -4 z" />
    <path class="fill-mist" d="M 162 170 H 238 L 230 262 Q 229 268 223 268 H 177 Q 171 268 170 262 Z" />
    <rect class="fill-accent" x="152" y="150" width="96" height="18" rx="5" />
    <g class="stroke-brand-900" stroke-width="5" stroke-linecap="round" opacity="0.8">
        <line x1="185" y1="186" x2="187" y2="250" />
        <line x1="200" y1="186" x2="200" y2="250" />
        <line x1="215" y1="186" x2="213" y2="250" />
    </g>
    <circle class="fill-brand-950 stroke-mist" cx="178" cy="266" r="13" stroke-width="3" />
    <circle class="fill-accent" cx="178" cy="266" r="4" />
</svg>
