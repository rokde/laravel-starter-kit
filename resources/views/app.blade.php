<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();

            {{-- Set the current locale globally --}}
            @auth
                window.locale = @json(auth()->user()->preferredLocale() ?? app()->getLocale());
            @endauth
            @guest
                const navigatorLanguage = navigator.language.substring(0, 2);
                window.locale = (@json(config('app.locales')).includes(navigatorLanguage))
                    ? navigatorLanguage
                    : @json(app()->getLocale());
            @endguest
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @if(str_contains($page['component'], '::'))
            @php
                [$module, $path] = explode('::', $page['component']);
            @endphp
            @vite(['resources/js/app.ts', "app-modules/{$module}/resources/js/pages/{$path}.vue"])
        @else
            @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @endif
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
