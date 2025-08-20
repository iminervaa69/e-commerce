<!DOCTYPE html>
<script src="https://unpkg.com/alpinejs" defer></script>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="dark:bg-gray-800">
    
    @include('layouts.navbar')
    @include('layouts.sidebar')

    <div class="p-4 sm:ml-64">
        @yield('content')
    </div>

    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>

</body>

</html>

@yield('insert-scripts')
