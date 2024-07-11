<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/css/toast.min.css">

    <style>
        html, body {
            height: 100%;
        }

        .page-content {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .page-content main {
            flex: 1;
        }

        .page-content footer {
            flex-shrink: 0;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="page-content min-h-screen bg-gray-100 dark:bg-gray-900">
    <livewire:layout.navigation/>

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <footer>
        <div class="bg-gray-300 dark:bg-gray-700">
            <div class="max-w-2xl mx-auto py-4">
{{--                <div class="text-center">--}}
{{--                    <h3 class="text-xl mb-3"> Download our fitness app </h3>--}}
{{--                    <p> Stay fit. All day, every day. </p>--}}
{{--                    <div class="flex justify-center my-10">--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="my-5 flex flex-col md:flex-row md:justify-between items-center text-sm dark:text-white">
                    <p class="order-2 md:order-1 mt-8 md:mt-0"> &copy; ООО "СКАД", 2024. </p>
                    <div class="order-1 md:order-2">
                        <a href="/" class="px-2 hover:text-indigo-400 dark:hover:text-indigo-600">О нас</a>
                        <a href="/" class="px-2 border-l hover:text-indigo-400 dark:hover:text-indigo-600">Связаться с нами</a>
                        <a href="/" class="px-2 border-l hover:text-indigo-400 dark:hover:text-indigo-600">Политика конфиденциальности</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
<script src="/assets/js/toast.min.js"></script>
<script src="https://kit.fontawesome.com/5850d038fd.js" crossorigin="anonymous"></script>
</body>
</html>
