<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data
    x-bind:data-theme="$store.theme.mode"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}">
        <script>
            document.documentElement.setAttribute(
                'data-theme',
                window.matchMedia('(prefers-color-scheme: dark)').matches ? 'luxury' : 'corporate'
            );
        </script>

        <title>{{($title ?? null)?->isNotEmpty() ? $title : config('app.name') }}</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="57x57" href="{{Vite::asset('resources/assets/favicon/apple-icon-57x57.png')}}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{Vite::asset('resources/assets/favicon/apple-icon-60x60.png')}}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{Vite::asset('resources/assets/favicon/apple-icon-72x72.png')}}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{Vite::asset('resources/assets/favicon/apple-icon-76x76.png')}}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{Vite::asset('resources/assets/favicon/apple-icon-114x114.png')}}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{Vite::asset('resources/assets/favicon/apple-icon-120x120.png')}}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{Vite::asset('resources/assets/favicon/apple-icon-144x144.png')}}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{Vite::asset('resources/assets/favicon/apple-icon-152x152.png')}}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{Vite::asset('resources/assets/favicon/apple-icon-180x180.png')}}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{Vite::asset('resources/assets/favicon/android-icon-192x192.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{Vite::asset('resources/assets/favicon/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{Vite::asset('resources/assets/favicon/favicon-96x96.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{Vite::asset('resources/assets/favicon/favicon-16x16.png')}}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{Vite::asset('resources/assets/favicon/ms-icon-144x144.png')}}">
        <meta name="theme-color" content="#ffffff">

        <!-- Styles -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
        @if(($style ?? null)?->isNotEmpty())
            {{ $style }}
        @endif
    </head>
    <body class="bg-base-200 min-h-screen" x-cloak>
        {{ $slot }}

        <x-partials.notifications.toast-wrapper/>

        @stack('scripts')
    </body>
</html>
