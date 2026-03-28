@extends('super-admin.layout')

@section('title', 'Add Tenant')

@section('content')
<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('super-admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">Add New Tenant</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('super-admin.tenants.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subdomain *</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="subdomain" value="{{ old('subdomain') }}"
                        placeholder="shop1"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-gray-500 text-sm">.{{ env('APP_DOMAIN', 'shop.localhost') }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Lowercase letters, numbers and hyphens only.</p>
                @error('subdomain')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Business Name *</label>
                <input type="text" name="business_name" value="{{ old('business_name') }}"
                    placeholder="My Shop"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('business_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <hr class="my-4">
            <p class="text-sm text-gray-500 mb-4">Admin account for this tenant:</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Username *</label>
                <input type="text" name="admin_username" value="{{ old('admin_username') }}"
                    placeholder="admin"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('admin_username')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email *</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                    placeholder="admin@shop.com"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('admin_email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Password *</label>
                <input type="password" name="admin_password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('admin_password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
                Create Tenant
            </button>
        </form>
    </div>
</div>
@endsection
