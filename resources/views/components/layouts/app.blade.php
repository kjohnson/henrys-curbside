<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ config('site.intro') }}">

        <title>{{ $title ?? config('site.name') }}</title>

        {{-- Lets CSS hide JavaScript-driven content (e.g. the rest of the address until the first
             line is typed) only when the script will be there to reveal it. --}}
        <script>document.documentElement.classList.add('js')</script>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans text-brand-950 antialiased">
        {{ $slot }}
    </body>
</html>
