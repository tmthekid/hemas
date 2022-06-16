<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Spin the wheel</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @yield('styles')
    </head>
    <body class="antialiased" style="background: url('background.jpg')">
        <div class="w-full px-2 md:px-0 lg:w-1/3" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            @yield('content')
        </div>
        @yield('scripts')
    </body>
</html>