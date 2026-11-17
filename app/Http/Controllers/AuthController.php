<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login dengan pilihan modul tujuan.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        $role = $request->session()->get('auth_role');
        $targetModule = $request->query('module');

        // Jika user sudah login sebelumnya:
        if ($role) {
            if ($targetModule === 'chatbotwa') {
                return redirect()->route('chatbotwa.dashboard');
            }
            if ($targetModule === 'pemeliharaanalat') {
                return redirect()->route('pemeliharaanalat.dashboard');
            }
            if ($targetModule === 'penyewaanreklame') {
                return redirect()->route('penyewaanreklame.dashboard');
            }
            if ($targetModule === 'absensi') {
                return $role === 'manajer'
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('karyawan.dashboard');
            }

            return $role === 'manajer'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('karyawan.dashboard');
        }

        $validModule = in_array($targetModule, ['absensi', 'chatbotwa', 'pemeliharaanalat', 'penyewaanreklame'], true)
            ? $targetModule
            : 'absensi';

        return view('login', [
            'selectedModule' => $validModule,
        ]);
    }

    /**
     * Autentikasi user dan alihkan langsung ke modul yang dipilih.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'string', 'in:karyawan,manajer'],
            'module' => ['nullable', 'string', 'in:absensi,chatbotwa,pemeliharaanalat,penyewaanreklame'],
        ]);

        $email = strtolower(trim($validated['email']));
        $explicitRole = $validated['role'] ?? null;
        $targetModule = $validated['module'] ?? 'absensi';

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
        } else {
            $request->session()->put('auth_role', 'karyawan');
            $request->session()->put('auth_user', [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@perusahaan.co.id',
                'nik' => 'PKL-2026-081',
                'division' => 'Divisi IT Development',
                'role_label' => 'Karyawan',
            ]);
        }

        // Pengalihan ke modul yang dipilih
        if ($targetModule === 'chatbotwa') {
            return redirect()->route('chatbotwa.dashboard');
        }

        if ($targetModule === 'pemeliharaanalat') {
            return redirect()->route('pemeliharaanalat.dashboard');
        }

        if ($targetModule === 'penyewaanreklame') {
            return redirect()->route('penyewaanreklame.dashboard');
        }

        // Default: Modul Absensi (sesuai peran)
        return $isManager
            ? redirect()->route('admin.dashboard')
            : redirect()->route('karyawan.dashboard');
    }

    /**
     * Akhiri sesi dan kembali ke halaman login.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['auth_role', 'auth_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
