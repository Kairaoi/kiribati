<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" >


    <!-- DataTables CSS (CDN) -->

    <style>
        [x-cloak] { display: none !important; }
        
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }

        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }

        .font-roboto {
            font-family: 'Roboto', sans-serif;
        }
    </style>

    @livewireStyles
   
    <!-- Add custom styles or styles from individual pages -->
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
   
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</head>

<body class="min-h-screen flex flex-col font-sans antialiased bg-white">
    <x-banner />

    @livewire('navigation-menu')

    @if (isset($header))
        {{ $header }}
    @elseif (View::hasSection('header'))
        @yield('header')
    @endif

    <main class="flex-grow bg-gray-50">
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
    @include('footer')
</body>

</html>
