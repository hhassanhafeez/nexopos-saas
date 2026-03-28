<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white rounded-xl shadow-md p-8 w-full max-w-sm">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Super Admin</h1>

    <form method="POST" action="{{ route('super-admin.login.post') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                autofocus>
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
            Login
        </button>
    </form>
</div>
</body>
</html>
