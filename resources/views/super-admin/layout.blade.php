<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin') — NexoPOS SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-gray-900 text-white px-6 py-3 flex items-center justify-between">
    <a href="{{ route('super-admin.dashboard') }}" class="font-bold text-lg">NexoPOS SaaS</a>
    <form method="POST" action="{{ route('super-admin.logout') }}">
        @csrf
        <button type="submit" class="text-sm text-gray-300 hover:text-white">Logout</button>
    </form>
</nav>

<main class="max-w-5xl mx-auto px-4 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>
