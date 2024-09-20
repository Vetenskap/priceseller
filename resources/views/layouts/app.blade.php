<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Priceseller') }}</title>

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

<livewire:cookies />

<div class="page-content min-h-screen bg-gray-100 dark:bg-gray-900">
    <livewire:notification-div/>
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
        <div class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
            <div class="w-1/2 mx-auto py-5">
                {{--                <div class="text-center">--}}
                {{--                    <h3 class="text-xl mb-3"> Download our fitness app </h3>--}}
                {{--                    <p> Stay fit. All day, every day. </p>--}}
                {{--                    <div class="flex justify-center my-10">--}}
                {{--                    </div>--}}
                {{--                </div>--}}
                <div class=" flex flex-col md:flex-row md:justify-between items-center text-sm dark:text-white">
                    <p class="order-2 md:order-1 mt-8 md:mt-0"> Copyright &copy; 2023 - {{ date('Y') }}. </p>
                    <div class="order-1 md:order-2">
                        <a href="/" class="px-2 hover:text-indigo-400 dark:hover:text-indigo-600">Связаться с нами</a>
                        <a href="{{route('privacy-policy')}}"
                           class="px-2 border-l hover:text-indigo-400 dark:hover:text-indigo-600">Политика
                            конфиденциальности</a>
                        <a href="{{route('cookies')}}"
                           class="px-2 border-l hover:text-indigo-400 dark:hover:text-indigo-600">Куки</a>
                    </div>
                </div>
                <p class="order-2 md:order-1 mt-8 md:mt-0 text-sm dark:text-white">ООО "ИВиКО". </p>
            </div>
        </div>
    </footer>
</div>
<script src="/assets/js/toast.min.js"></script>
<script src="https://kit.fontawesome.com/5850d038fd.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropdownContainers = document.querySelectorAll('.dropdown-container');

        dropdownContainers.forEach(container => {
            const dropdownButton = container.querySelector('.dropdown-button');
            const dropdownMenu = container.querySelector('.dropdown-menu');
            const searchInput = container.querySelector('.search-input');
            let isOpen = false;

            // Function to close all dropdowns
            function closeAllDropdowns() {
                dropdownContainers.forEach(cont => {
                    const menu = cont.querySelector('.dropdown-menu');
                    menu.classList.add('hidden');
                    menu.style.zIndex = 10; // reset z-index
                });
            }

            // Function to toggle the dropdown state
            function toggleDropdown() {
                closeAllDropdowns(); // close all dropdowns first
                isOpen = !isOpen;
                dropdownMenu.classList.toggle('hidden', !isOpen);
                if (isOpen) {
                    dropdownMenu.style.zIndex = 1000; // set a high z-index for the opened dropdown
                }
            }

            dropdownButton.addEventListener('click', (event) => {
                event.stopPropagation(); // prevent click from propagating to the document
                toggleDropdown();
            });

            // Add event listener to filter items based on input
            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();
                const items = dropdownMenu.querySelectorAll('div');

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Close the dropdown if clicked outside
            document.addEventListener('click', (event) => {
                if (!container.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                    dropdownMenu.style.zIndex = 10; // reset z-index
                    isOpen = false;
                }
            });
        });
    });
</script>
</body>
</html>
