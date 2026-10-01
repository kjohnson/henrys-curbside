{{-- Small side-view garbage truck in the logo's style: one-colour compactor body and cab, with
     the window and wheel gaps cut out, and plain black wheels.
     Drawn 1 unit = 1px at 80×32, wheels touching the bottom edge so it can sit on a line. --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 80 32', 'xmlns' => 'http://www.w3.org/2000/svg', 'aria-hidden' => 'true']) }}>
    <defs>
        {{-- Cuts the window out of the cab and a gap around each wheel, like the logo's bin. --}}
        <mask id="truck-cutouts">
            <rect width="80" height="32" fill="white" />
            <path d="M59 12.5H65L69.5 17H59Z" fill="black" />
            <circle cx="25" cy="27" r="6.5" fill="black" />
            <circle cx="38" cy="27" r="6.5" fill="black" />
            <circle cx="64" cy="27" r="6.5" fill="black" />
        </mask>
    </defs>

    {{-- Motion lines: each rounded end sits ~4.75 units from the body's back.
         Hidden while parked; they fade in once the truck is driving (see app.css). --}}
    <g class="truck-motion-lines stroke-brand-900" stroke-width="2.5" stroke-linecap="round">
        <line x1="4" y1="10" x2="12" y2="10" />
        <line x1="0" y1="15" x2="10" y2="15" />
        <line x1="4" y1="20" x2="10" y2="20" />
    </g>

    <g class="fill-green-600" mask="url(#truck-cutouts)">
        {{-- Compactor body: the back sweeps down from the roof in a radius-10 curve --}}
        <path d="M26 6H51Q54 6 54 9V21Q54 24 51 24H19Q16 24 16 21V16A10 10 0 0 1 26 6Z" />
        {{-- Cab, separated from the body by a 2-unit gap --}}
        <path d="M56 10H66L72 17Q74 18 74 20V24H56Z" />
    </g>

    {{-- Wheels: tandem rear axle (13 apart, so their gaps meet without overlapping) and front --}}
    <g class="fill-black">
        <circle cx="25" cy="27" r="5" />
        <circle cx="38" cy="27" r="5" />
        <circle cx="64" cy="27" r="5" />
    </g>
</svg>
