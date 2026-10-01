{{-- Henry's Curbside mark: an abstract rolling bin (tapered body, tipped lid, cut-out wheel) above the wordmark. --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 200 250', 'role' => 'img', 'xmlns' => 'http://www.w3.org/2000/svg'])->class('overflow-visible') }} aria-labelledby="logo-title">
    <title id="logo-title">{{ config('site.name') }}</title>
    <defs>
        {{-- Clears a gap in the body around the wheel. --}}
        <mask id="logo-wheel-gap">
            <rect width="200" height="250" fill="white" />
            <circle cx="102" cy="126" r="20" fill="black" />
        </mask>
    </defs>

    {{-- Entrance animation (see app.css): the mark rolls in from the left, then the bin rocks forward and settles. --}}
    <g class="logo-roll-in">
        <g transform="translate(10 0)">
            {{-- Motion lines: each rounded end sits 12 units from the tilted bin's left side.
                 Drawn right-to-left with pathLength="1" so the animation can trim them from the left.
                 --peak / --dip: how far the bin's side moves at each line's height at the rock's
                 +14° and -2° extremes, so the lines keep their 12-unit gap as it rocks. --}}
            <g class="stroke-mist" stroke-width="8" stroke-linecap="round" opacity="0.45">
                <line class="logo-motion-line" x1="66.1" y1="70" x2="48.1" y2="70" pathLength="1" style="--peak: 14.1px; --dip: -2.14px" />
                <line class="logo-motion-line" x1="70.8" y1="90" x2="38.8" y2="90" pathLength="1" style="--peak: 9.13px; --dip: -1.4px" />
                <line class="logo-motion-line" x1="75.4" y1="110" x2="63.4" y2="110" pathLength="1" style="--peak: 4.16px; --dip: -0.65px" />
            </g>

            {{-- Pivots on the wheel centre (102, 126). --}}
            <g class="logo-rock">
                {{-- Bin, tipped back onto its wheel as if being rolled --}}
                <g transform="rotate(-8 102 126)">
                    <polygon class="fill-mist stroke-mist" points="94,56 146,56 140,122 100,122" stroke-width="10" stroke-linejoin="round" mask="url(#logo-wheel-gap)" />
                    {{-- Lid floats clear of the body: at least 4 units apart at its closest (left) end.
                         During the rock it closes to parallel with the body (see .logo-lid in app.css). --}}
                    <g class="logo-lid">
                        <rect class="fill-accent" x="86" y="29.6" width="68" height="10" rx="5" transform="rotate(-7 154 39.6)" />
                    </g>
                    <circle class="fill-accent" cx="102" cy="126" r="13" />
                </g>
            </g>
        </g>
    </g>

    {{-- Wordmark --}}
    <text class="fill-mist" x="100" y="200" text-anchor="middle" font-family="'Instrument Sans', Helvetica, Arial, sans-serif" font-weight="700" font-size="46" letter-spacing="-1.5">henry's</text>
    <text class="fill-accent" x="102" y="232" text-anchor="middle" font-family="'Instrument Sans', Helvetica, Arial, sans-serif" font-weight="600" font-size="13" letter-spacing="6">CURBSIDE</text>
</svg>
