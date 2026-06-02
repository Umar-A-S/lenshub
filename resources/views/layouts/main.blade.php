<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LensHub') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500;700&family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[var(--bg-page)] min-h-screen">
        <div class="flex">
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col ml-[220px] min-h-screen">
                <!-- Topbar -->
                <x-topbar />

                <!-- Page Content -->
                <main class="flex-1 p-[24px]">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
