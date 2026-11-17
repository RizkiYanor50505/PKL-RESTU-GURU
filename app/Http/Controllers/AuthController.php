<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login screen or redirect if already authenticated.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        $role = $request->session()->get('auth_role');

        if ($role === 'manajer') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'karyawan') {
            return redirect()->route('karyawan.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate user and assign role session (karyawan or manajer).
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'string', 'in:karyawan,manajer'],
        ]);

        $email = strtolower(trim($validated['email']));
        $explicitRole = $validated['role'] ?? null;

        $isManager = $explicitRole === 'manajer'
            || str_contains($email, 'manajer')
            || str_contains($email, 'manager')
            || str_contains($email, 'admin')
            || str_contains($email, 'hrd');

        if ($isManager) {
            $request->session()->put('auth_role', 'manajer');
            $request->session()->put('auth_user', [
                'name' => 'Budi Santoso',
                'email' => 'manajer.hrd@perusahaan.co.id',
                'nik' => 'MGR-2026-014',
                'division' => 'Manajer Operasional & HRD',
                'role_label' => 'Manajer / HRD',
            ]);

            return redirect()->route('admin.dashboard');
        }

        $request->session()->put('auth_role', 'karyawan');
        $request->session()->put('auth_user', [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@perusahaan.co.id',
            'nik' => 'PKL-2026-081',
            'division' => 'Divisi IT Development',
            'role_label' => 'Karyawan',
        ]);

        return redirect()->route('karyawan.dashboard');
    }

    /**
     * Terminate active role session and return to login screen.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['auth_role', 'auth_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
