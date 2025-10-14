<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.rtl.min.css') }}"/>

    @yield('external_stylesheet')
    @stack('internal_stylesheet')
</head>

<body>
<main class="flex-auto py-5">
    @section('content')
    @show
</main>

<script src="{{ asset('assets/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js')}}"></script>

@yield('external_scripts')
@stack('internal_scripts')
</body>
</html>
