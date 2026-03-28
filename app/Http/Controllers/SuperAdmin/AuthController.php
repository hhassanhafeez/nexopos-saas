<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Session::get('super_admin_authenticated')) {
            return redirect()->route('super-admin.dashboard');
        }

        return view('super-admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required']);

        if ($request->password === env('SUPER_ADMIN_PASSWORD', 'changeme')) {
            Session::put('super_admin_authenticated', true);
            return redirect()->route('super-admin.dashboard');
        }

        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    public function logout()
    {
        Session::forget('super_admin_authenticated');
        return redirect()->route('super-admin.login');
    }
}
