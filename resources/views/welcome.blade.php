<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#081224">

        <title>SIS Laravel</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @include('frontend.pages.landing')
        @include('frontend.pages.signin')
        @include('frontend.partials.app-shell')
        @include('frontend.partials.modals')
    </body>
</html>
