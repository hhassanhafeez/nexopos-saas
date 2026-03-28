@extends('super-admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tenants</h1>
    <a href="{{ route('super-admin.tenants.create') }}"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
        + Add Tenant
    </a>
</div>

@if($tenants->isEmpty())
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
        No tenants yet. Add your first one.
    </div>
@else
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Business</th>
                <th class="px-4 py-3 text-left">Subdomain</th>
                <th class="px-4 py-3 text-left">URL</th>
                <th class="px-4 py-3 text-left">Created</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($tenants as $tenant)
            <tr>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $tenant->business_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $tenant->id }}</td>
                <td class="px-4 py-3">
                    @foreach($tenant->domains as $domain)
                        <a href="http://{{ $domain->domain }}" target="_blank"
                            class="text-blue-600 hover:underline">
                            {{ $domain->domain }}
                        </a>
                    @endforeach
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $tenant->created_at->diffForHumans() }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('super-admin.tenants.destroy', $tenant->id) }}"
                        onsubmit="return confirm('Delete tenant {{ $tenant->business_name }}? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
