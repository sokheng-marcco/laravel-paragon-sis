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
    <body
        data-page="{{ $page }}"
        @isset($portalRole) data-portal-role="{{ $portalRole }}" @endisset
        @isset($modalType) data-modal-type="{{ $modalType }}" @endisset
        @isset($modalMode) data-modal-mode="{{ $modalMode }}" @endisset
        @isset($modalIdParameter) data-modal-id="{{ request()->route($modalIdParameter) }}" @endisset
        @isset($modalCourseParameter) data-modal-course-id="{{ request()->route($modalCourseParameter) }}" @endisset
        @isset($modalReturnRoute) data-modal-return-url="{{ route($modalReturnRoute, request()->query()) }}" @endisset
    >
        @if ($page === 'landing')
            @include('frontend.pages.landing')
        @elseif ($page === 'signin')
            @include('frontend.pages.signin')
        @elseif ($page === 'change-password')
            @include('frontend.pages.change-password')
        @else
            @include('frontend.partials.app-shell')
            @include('frontend.partials.modals')
        @endif
    </body>
</html>
