<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ Auth::id() }}">
    @endauth

    <title>{{ config('app.name', 'Thrift-IT') }}</title>
    <!-- Favicon from S3 -->
    <link rel="icon" href="{{ Storage::disk('s3')->url('images/logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Pusher & WebRTC -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <meta name="user-id" content="{{ Auth::check() ? Auth::id() : '' }}">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Loader -->
    <div id="loader" class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-gray-900">
        <div class="spinner border-4 border-t-4 border-gray-200 border-t-gray-800 rounded-full w-16 h-16 animate-spin">
        </div>
    </div>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top-color: #B59F84;
            /* brand color */
            border-radius: 50%;
            width: 64px;
            height: 64px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="font-sans antialiased dark:bg-gray-800">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Success Message -->
        <x-alert :message="session('success')" type="success" />
        <x-alert :message="session('error')" type="error" />
        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        @unless (request()->routeIs('private.chat'))
            <x-footer />
        @endunless
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @stack('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById('loader');
            if (loader) loader.style.display = 'none';
        });
    </script>
</body>

</html>
