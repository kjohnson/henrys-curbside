<x-layouts.app>
    <div>
        {{-- Left column: brand graphic, pinned in place on large screens. --}}
        <aside class="relative flex flex-col items-center justify-center overflow-hidden bg-brand-900 px-6 py-12 text-mist lg:fixed lg:inset-y-0 lg:left-0 lg:w-1/2 lg:py-16">
            <div class="pointer-events-none absolute inset-0 opacity-[0.07] [background-image:radial-gradient(var(--color-mist)_1.5px,transparent_1.5px)] [background-size:22px_22px]" aria-hidden="true"></div>

            <div class="relative flex flex-col items-center text-center">
                @if (config('site.logo'))
                    <img src="{{ asset(config('site.logo')) }}" alt="{{ config('site.name') }}" class="w-48 sm:w-64 lg:w-[min(26rem,60vh)]">
                @else
                    <x-logo class="w-48 drop-shadow-2xl sm:w-64 lg:w-[min(26rem,60vh)]" />
                @endif

                <p class="mt-8 text-2xl font-semibold tracking-tight sm:text-3xl">{{ config('site.tagline') }}</p>
                <p class="mt-2 text-sm font-medium uppercase tracking-[0.2em] text-accent">{{ config('site.service_area') }}</p>
            </div>
        </aside>

        {{-- Right column: scrolls with the page. --}}
        <main class="flex min-h-dvh flex-col bg-mist/40 lg:ml-[50%]">
            <div class="mx-auto w-full max-w-xl flex-1 px-6 py-12 sm:px-10 lg:py-20">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700">Coming soon</p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight text-brand-900 sm:text-5xl">{{ config('site.headline') }}</h1>
                <p class="mt-5 text-lg leading-relaxed text-brand-950/75">{{ config('site.intro') }}</p>

                <ol class="mt-10 grid gap-4 sm:grid-cols-3">
                    @foreach (config('site.steps') as $step)
                        <li class="rounded-xl border border-brand-950/10 bg-white p-4">
                            <span class="flex size-7 items-center justify-center rounded-full bg-accent text-sm font-bold text-brand-950">{{ $loop->iteration }}</span>
                            <p class="mt-3 font-semibold text-brand-900">{{ $step['title'] }}</p>
                            <p class="mt-1 text-sm leading-snug text-brand-950/65">{{ $step['body'] }}</p>
                        </li>
                    @endforeach
                </ol>

                <section id="register" class="mt-12 rounded-2xl border border-brand-950/10 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="register-heading">
                    @if (session('registered'))
                        <div class="py-6 text-center" role="status">
                            <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-brand-100">
                                <svg class="size-7 text-brand-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7" /></svg>
                            </div>
                            <h2 id="register-heading" class="mt-5 text-2xl font-bold text-brand-900">You're on the list!</h2>
                            <p class="mt-2 text-brand-950/70">Thanks for your interest. We'll be in touch as soon as service opens up in your area.</p>
                        </div>
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

                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-field name="email" label="Email" type="email" autocomplete="email" inputmode="email" />
                                <x-field name="phone" label="Phone number" type="tel" autocomplete="tel" inputmode="tel" placeholder="(555) 555-0123" />
                            </div>

                            <fieldset class="space-y-5">
                                <legend class="mb-3 text-sm font-semibold uppercase tracking-wider text-brand-700">Service address</legend>

                                <x-field name="street" label="Street address" autocomplete="address-line1" />
                                <x-field name="unit" label="Apt, unit, or suite" autocomplete="address-line2" optional />

                                <div class="grid gap-5 sm:grid-cols-6">
                                    <x-field name="city" label="City" autocomplete="address-level2" class="sm:col-span-3" />
                                    <x-field name="state" label="State" autocomplete="address-level1" maxlength="2" placeholder="OH" class="sm:col-span-1" />
                                    <x-field name="postal_code" label="ZIP code" autocomplete="postal-code" inputmode="numeric" class="sm:col-span-2" />
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

            <footer class="border-t border-brand-950/10 px-6 py-6 text-center text-sm text-brand-950/55">
                &copy; {{ date('Y') }} {{ config('site.name') }} &middot;
                <a href="mailto:{{ config('site.contact_email') }}" class="underline decoration-brand-950/20 underline-offset-2 hover:text-brand-900">{{ config('site.contact_email') }}</a>
            </footer>
        </main>
    </div>
</x-layouts.app>
