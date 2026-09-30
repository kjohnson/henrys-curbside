{{-- Henry's Curbside mark: an abstract rolling bin (tapered body, tipped lid, cut-out wheel) above the wordmark. --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 200 250', 'role' => 'img', 'xmlns' => 'http://www.w3.org/2000/svg']) }} aria-labelledby="logo-title">
    <title id="logo-title">{{ config('site.name') }}</title>
    <defs>
        {{-- Clears a gap in the body around the wheel. --}}
        <mask id="logo-wheel-gap">
            <rect width="200" height="250" fill="white" />
            <circle cx="102" cy="126" r="20" fill="black" />
        </mask>
    </defs>

    <g transform="translate(10 0)">
        {{-- Motion lines: each rounded end sits 12 units from the tilted bin's left side. --}}
        <g class="stroke-mist" stroke-width="8" stroke-linecap="round" opacity="0.45">
            <line x1="48.1" y1="70" x2="66.1" y2="70" />
            <line x1="38.8" y1="90" x2="70.8" y2="90" />
            <line x1="63.4" y1="110" x2="75.4" y2="110" />
        </g>

        {{-- Bin, tipped back onto its wheel as if being rolled --}}
        <g transform="rotate(-8 102 126)">
            <polygon class="fill-mist stroke-mist" points="94,56 146,56 140,122 100,122" stroke-width="10" stroke-linejoin="round" mask="url(#logo-wheel-gap)" />
            {{-- Lid floats clear of the body: at least 4 units apart at its closest (left) end. --}}
            <rect class="fill-accent" x="86" y="29.6" width="68" height="10" rx="5" transform="rotate(-7 154 39.6)" />
            <circle class="fill-accent" cx="102" cy="126" r="13" />
        </g>
    </g>

    {{-- Wordmark --}}
    <text class="fill-mist" x="100" y="200" text-anchor="middle" font-family="'Instrument Sans', Helvetica, Arial, sans-serif" font-weight="700" font-size="46" letter-spacing="-1.5">henry's</text>
    <text class="fill-accent" x="102" y="232" text-anchor="middle" font-family="'Instrument Sans', Helvetica, Arial, sans-serif" font-weight="600" font-size="13" letter-spacing="6">CURBSIDE</text>
</svg>
