<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('img/logo-removebg.png') }}" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    @livewireStyles
    @include('layouts.additional.styles')
    @stack('css')
</head>

<body>
    <div id="app">
        @include('layouts.sidebar')
        @yield('content')
    </div>

    <!-- java script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- bootstrap -->
    @include('layouts.additional.script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    
    
    
    @livewireScripts
    <script src="/dist/assets/static/js/initTheme.js"></script>
    @stack('js')
</body>
</html>
