@props([
    'title' => 'Dashboard',
    'totalAppointments' => 0,
    'totalDentists' => 0,
    'staticCard' => '',
    'appointments' => [],
    'dentistList' => [],
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex">

    {{-- Sidebar --}}
    <aside class="w-64 h-screen bg-gray-900 text-white p-4">
        <div class="font-bold text-xl mb-6">🦷 Dentist Appointment</div>

        <nav class="flex flex-col gap-2">
            <a href="{{ route('dashboard') }}"
               class="p-2 rounded {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('dentists.index') }}"
               class="p-2 rounded {{ request()->is('dentists*') ? 'bg-gray-700' : '' }}">
                Dentists
            </a>
        </nav>

        <form action="{{ route('logout') }}" method="POST" class="mt-10">
            @csrf
            <button class="bg-red-500 w-full py-2 rounded">Logout</button>
        </form>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>

</div>

</body>
</html>
