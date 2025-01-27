<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- DataTables CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">

    <!-- Styles -->
   

    <!-- Add custom styles or styles from individual pages -->
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">

        <!-- Navigation Bar -->
        <nav class="bg-white shadow-md fixed-top w-full z-10">
            <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                <div class="relative flex items-center justify-between h-16">
                    <!-- Mobile menu toggle button -->
                    <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                        <button @click="open = ! open" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Branding / Logo -->
                    <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                        <a href="{{ url('/') }}" class="text-2xl font-semibold text-gray-800">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:block sm:ml-6">
                        <div class="flex space-x-4">
                            @guest
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-900">Login</a>
                                @endif

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-gray-500 hover:text-gray-900">Register</a>
                                @endif
                            @else
                                <span class="text-gray-500">Welcome, {{ Auth::user()->name }}</span>
                                <a href="{{ route('logout') }}" class="text-gray-500 hover:text-gray-900"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="py-4 mt-16"> <!-- mt-16 adds margin to offset the fixed navbar -->
            @yield('content') <!-- The content of each page will be injected here -->
        </main>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    

    <!-- DataTables JS (CDN) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

    <!-- Add custom scripts or scripts from individual pages -->
    @stack('scripts')

    
</body>
</html>
