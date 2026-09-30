{{-- Henry's Curbside badge: a wheeled bin on the curb, circled by out-and-back arrows. --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 400 400', 'role' => 'img', 'xmlns' => 'http://www.w3.org/2000/svg']) }} aria-labelledby="logo-title">
    <title id="logo-title">{{ config('site.name') }}</title>
    <defs>
        <path id="logo-arc-top" d="M 48 200 A 152 152 0 0 1 352 200" />
        <path id="logo-arc-bottom" d="M 36 200 A 164 164 0 0 0 364 200" />
        <marker id="logo-arrowhead" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="3.2" markerHeight="3.2" orient="auto">
            <path d="M 0 0 L 10 5 L 0 10 z" fill="#f4b41a" />
        </marker>
    </defs>

    {{-- Badge --}}
    <circle cx="200" cy="200" r="196" fill="#f6f1e3" />
    <circle cx="200" cy="200" r="188" fill="#1d4d3b" />
    <circle cx="200" cy="200" r="178" fill="none" stroke="#f6f1e3" stroke-width="2" stroke-dasharray="2 6" opacity="0.6" />

    {{-- Wordmark --}}
    <text font-family="'Instrument Sans', 'Arial Black', Helvetica, sans-serif" font-weight="700" font-size="34" letter-spacing="6" fill="#f6f1e3">
        <textPath href="#logo-arc-top" startOffset="50%" text-anchor="middle">HENRY'S</textPath>
    </text>
    <text font-family="'Instrument Sans', 'Arial Black', Helvetica, sans-serif" font-weight="700" font-size="26" letter-spacing="7" fill="#f4b41a">
        <textPath href="#logo-arc-bottom" startOffset="50%" text-anchor="middle">CURBSIDE</textPath>
    </text>
    <circle cx="42" cy="200" r="5" fill="#f4b41a" />
    <circle cx="358" cy="200" r="5" fill="#f4b41a" />

    {{-- Out-and-back arrows --}}
    <g fill="none" stroke="#f4b41a" stroke-width="9" stroke-linecap="round" marker-end="url(#logo-arrowhead)">
        <path d="M 84 145.9 A 128 128 0 0 1 294 120.6" />
        <path d="M 316 254.1 A 128 128 0 0 1 106 279.4" />
    </g>

    {{-- Curb --}}
    <path d="M 128 280 H 236 V 292 H 262" fill="none" stroke="#f6f1e3" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />

    {{-- Wheeled bin --}}
    <path d="M 214 138 h 22 a 4 4 0 0 1 4 4 v 8 h -30 v -8 a 4 4 0 0 1 4 -4 z" fill="#f4b41a" />
    <path d="M 162 170 H 238 L 230 262 Q 229 268 223 268 H 177 Q 171 268 170 262 Z" fill="#f6f1e3" />
    <rect x="152" y="150" width="96" height="18" rx="5" fill="#f4b41a" />
    <g stroke="#1d4d3b" stroke-width="5" stroke-linecap="round" opacity="0.8">
        <line x1="185" y1="186" x2="187" y2="250" />
        <line x1="200" y1="186" x2="200" y2="250" />
        <line x1="215" y1="186" x2="213" y2="250" />
    </g>
    <circle cx="178" cy="266" r="13" fill="#0f2a20" stroke="#f6f1e3" stroke-width="3" />
    <circle cx="178" cy="266" r="4" fill="#f4b41a" />
</svg>
