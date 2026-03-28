<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')->latest()->get();
        return view('super-admin.dashboard', compact('tenants'));
    }

    public function create()
    {
        return view('super-admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subdomain'      => ['required', 'alpha_dash', 'min:3', 'max:30', 'unique:tenants,id'],
            'business_name'  => ['required', 'string', 'min:3', 'max:100'],
            'admin_username' => ['required', 'string', 'min:5', 'max:50'],
            'admin_email'    => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:6'],
        ]);

        $subdomain = Str::slug($request->subdomain, '-');

        // Stancl stores non-custom-column attributes in the data JSON automatically
        $tenant = Tenant::create([
            'id'             => $subdomain,
            'business_name'  => $request->business_name,
            'admin_username' => $request->admin_username,
            'admin_email'    => $request->admin_email,
            'admin_password' => $request->admin_password,
        ]);

        $appDomain = env('APP_DOMAIN', 'shop.localhost');
        $tenantDomain = "{$subdomain}.{$appDomain}";

        $tenant->domains()->create(['domain' => $tenantDomain]);

        return redirect()->route('super-admin.dashboard')
            ->with('success', "Tenant \"{$request->business_name}\" created. URL: http://{$tenantDomain}");
    }

    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete(); // stancl automatically drops the tenant database
        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Tenant deleted successfully.');
    }
}
