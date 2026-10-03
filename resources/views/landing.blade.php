<x-layouts.app>
    {{-- After the first step the page picks up where the visitor left off. --}}
    @php($returning = session('registered') || isset($contactFor))

    <div>
        {{-- Left column: brand graphic, pinned in place on large screens. --}}
        <aside class="relative flex flex-col items-center justify-center overflow-hidden bg-brand-900 px-6 py-12 text-mist lg:fixed lg:inset-y-0 lg:left-0 lg:w-1/2 lg:py-16">
            <div class="relative flex flex-col items-center text-center">
                @if (config('site.logo'))
                    <img src="{{ asset(config('site.logo')) }}" alt="{{ config('site.name') }}" class="w-40 sm:w-52 lg:w-[min(20rem,50vh)]">
                @else
                    {{-- First page: the usual entrance. Second step (just submitted): starts at rest, then
                         rolls out and back in. Final thank-you page: stays at rest. --}}
                    <x-logo class="w-40 cursor-pointer select-none [-webkit-tap-highlight-color:transparent] sm:w-52 lg:w-[min(20rem,50vh)]" data-logo-replay :data-logo-intro="isset($contactFor) ? 'replay' : ($returning ? 'skip' : null)" />
                @endif

                <p class="mt-8 text-2xl font-semibold tracking-tight sm:text-3xl">{{ config('site.tagline') }}</p>
                <p class="mt-2 text-sm font-medium uppercase tracking-[0.2em] text-accent">{{ config('site.service_area') }}</p>
            </div>
        </aside>

        {{-- Map texture pinned behind the right column; a fixed layer rather than
             background-attachment: fixed, which iOS Safari ignores. --}}
        <div class="pointer-events-none fixed inset-0 -z-10 bg-map lg:left-1/2" aria-hidden="true"></div>

        {{-- Right column: scrolls with the page, over the pinned map. --}}
        <main class="flex min-h-dvh flex-col bg-mist/40 lg:ml-[50%]">
            <div class="mx-auto w-full max-w-xl flex-1 px-6 py-12 sm:px-10 lg:py-20">
                {{-- On stacked (mobile) layouts, hide the intro after submitting so the next step is in view. --}}
                <header @class(['mb-10', 'hidden lg:block' => $returning])>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700">Coming soon</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-brand-900 sm:text-5xl">{{ config('site.headline') }}</h1>
                    <p class="mt-5 text-lg leading-relaxed text-brand-950/75">{{ config('site.intro') }}</p>
                </header>

                <section id="register" class="rounded-2xl border border-brand-950/10 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="register-heading">
                    @if (session('registered'))
                        <div class="py-6 text-center" role="status">
                            <x-success-mark />
                            @if (session('contact_saved'))
                                <h2 id="register-heading" class="mt-5 text-2xl font-bold text-brand-900">We'll let you know!</h2>
                                <p class="mt-2 text-brand-950/70">Thanks! We'll reach out as soon as service opens up at your address.</p>
                            @else
                                <h2 id="register-heading" class="mt-5 text-2xl font-bold text-brand-900">You're on the list!</h2>
                                <p class="mt-2 text-brand-950/70">Thanks for your interest. We'll be in touch as soon as service opens up in your area.</p>
                            @endif
                        </div>
                    @elseif (isset($contactFor))
                        {{-- Doubles as the first form's success message, then asks for contact details. --}}
                        <div class="text-center" role="status">
                            <x-success-mark />
                            <h2 id="register-heading" class="mt-5 text-2xl font-bold text-brand-900">You're on the list!</h2>
                            <p class="mt-2 text-brand-950/70">Thanks for your interest in service at {{ $contactFor->street }}.</p>
                        </div>

                        <div class="mt-8 border-t border-brand-950/10 pt-8">
                            <h3 id="contact-heading" class="text-xl font-bold text-brand-900">How should we reach you?</h3>
                            <p class="mt-1 text-brand-950/65">Leave an email or phone number and we'll let you know when service starts.</p>
                        </div>

                        {{-- Posts back to the signed, one-hour URL; see AvailabilityCheckContactController. --}}
                        <form method="POST" action="{{ $contactUrl }}" class="mt-6 space-y-5" aria-labelledby="contact-heading" novalidate>
                            @csrf
                            @method('PUT')

                            <x-field name="email" label="Email" type="email" autocomplete="email" inputmode="email" optional />
                            <x-field name="phone" label="Phone number" type="tel" autocomplete="tel-national" inputmode="tel" placeholder="(555) 555-0123" data-mask="us-phone" optional />

                            <div class="space-y-3">
                                <button type="submit" class="w-full rounded-lg bg-accent px-5 py-3.5 text-base font-bold text-brand-950 shadow-sm transition hover:bg-accent-dark focus:outline-none focus-visible:ring-4 focus-visible:ring-accent/40">
                                    Save
                                </button>
                                <button type="submit" name="skip" value="1" class="w-full rounded-lg border border-brand-950/15 bg-white px-5 py-3 text-base font-semibold text-brand-900 transition hover:bg-mist focus:outline-none focus-visible:ring-4 focus-visible:ring-brand-700/15">
                                    Skip
                                </button>
                            </div>
                        </form>
                    @else
                        <h2 id="register-heading" class="text-2xl font-bold text-brand-900">Register your interest</h2>
                        <p class="mt-1 text-brand-950/65">No commitment and no payment — we'll simply let you know when we launch near you.</p>

                        <form method="POST" action="{{ route('registrations.store') }}" class="mt-8 space-y-5" novalidate>
                            @csrf

                            {{-- Honeypot: hidden from people, tempting to bots. --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="company">Company</label>
                                <input id="company" name="company" type="text" tabindex="-1" autocomplete="off">
                            </div>

                            <x-field name="name" label="Full name" autocomplete="name" />

                            {{-- Address first: typing in it reveals the rest (resources/js/address-reveal.js). Without
                                 JavaScript, or after a failed submission, every field is shown. City and state
                                 default to the service area (config/site.php). --}}
                            <fieldset class="space-y-5" data-address-reveal>
                                <x-field name="street" label="Service address" autocomplete="address-line1" />

                                <div @class(['address-details space-y-5', 'is-revealed' => $errors->any() || filled(old('street'))]) data-address-details>
                                    <x-field name="unit" label="Apartment or unit number" autocomplete="address-line2" optional />

                                    {{-- One row from sm up; on phones City gets its own row so the ZIP box stays wide enough. --}}
                                    <div class="grid grid-cols-12 gap-5">
                                        <x-field name="city" label="City" :value="config('site.service_location.city')" autocomplete="address-level2" class="col-span-12 sm:col-span-6" />
                                        <x-select name="state" label="State" :options="\App\Support\UsStates::abbreviations()" :value="config('site.service_location.state')" autocomplete="address-level1" class="col-span-6 sm:col-span-3" />
                                        <x-field name="postal_code" label="ZIP code" autocomplete="postal-code" inputmode="numeric" maxlength="10" optional class="col-span-6 sm:col-span-3" />
                                    </div>
                                </div>
                            </fieldset>

                            <button type="submit" class="w-full rounded-lg bg-accent px-5 py-3.5 text-base font-bold text-brand-950 shadow-sm transition hover:bg-accent-dark focus:outline-none focus-visible:ring-4 focus-visible:ring-accent/40">
                                Count me in
                            </button>

                            <p class="text-center text-xs text-brand-950/55">We'll only use your details to contact you about service. No spam, ever.</p>
                        </form>
                    @endif
                </section>
            </div>

            <footer @class(['border-t border-brand-950/10 px-6 pt-6 text-center text-sm text-brand-950/55', 'pb-6' => $returning])>
                <p>
                    &copy; {{ date('Y') }} {{ config('site.name') }} &middot;
                    <a href="mailto:{{ config('site.contact_email') }}" class="underline decoration-brand-950/20 underline-offset-2 hover:text-brand-900">{{ config('site.contact_email') }}</a>
                </p>

                {{-- First page only: a garbage truck along the very bottom of the page, driving off once
                     it scrolls into view (see .truck-drive in app.css and resources/js/truck.js). --}}
                @unless ($returning)
                    <div class="pointer-events-none relative -mx-6 mt-4 @container h-8 overflow-hidden">
                        <x-truck class="truck-drive absolute bottom-0 left-0 h-8 w-20" data-drive-when-visible />
                    </div>
                @endunless
            </footer>
        </main>
    </div>
</x-layouts.app>
