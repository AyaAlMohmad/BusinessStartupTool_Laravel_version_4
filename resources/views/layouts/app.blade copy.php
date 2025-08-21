<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Valuenation</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
            <style>
                .icon-blue {
                    color: #3b82f6;
                    transition: color 0.2s ease;
                }
                .icon-blue:hover {
                    color: #1d4ed8;
                }
                
                .icon-red {
                    color: #f63b44;
                    transition: color 0.2s ease;
                }
                .icon-red:hover {
                    color: #bb0b0b;
                }
            </style>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            <div class="flex">
                <!-- Sidebar Container -->
                {{-- <aside class="w-64 min-h-screen bg-white border-r"> --}}
    
                    <!-- Navigation Menu -->
                   {{-- @include('layouts.sidebar') --}}
                {{-- </aside> --}}
    
                <!-- Main Content -->

            @include('layouts.sidebar')
                <!-- Page Content -->
                <main class="flex-1 mx-auto w-1/2 p-8">
                  @yield('content')
                </main>
        </div>
    </body>
</html>
