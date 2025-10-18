<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page_title')</title>

    @yield('external_stylesheet')
    @stack('internal_stylesheet')
</head>

<body>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<main class="flex-auto py-5">
    @section('content')
    @show
</main>

<script src="{{ asset('assets/vendor/jquery/jquery.min.js')}}"></script>

@yield('external_scripts')
@stack('internal_scripts')
</body>
</html>
