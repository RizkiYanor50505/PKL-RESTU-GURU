<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated visitors are redirected to login when accessing protected routes.
     */
    public function test_unauthenticated_users_cannot_access_karyawan_or_manajer_pages(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('karyawan.dashboard'))->assertRedirect(route('login'));
        $this->get(route('karyawan.scan'))->assertRedirect(route('login'));
        $this->get(route('karyawan.izin'))->assertRedirect(route('login'));
        $this->get(route('karyawan.riwayat'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    /**
     * Karyawan can access employee pages but is blocked from manager pages.
     */
    public function test_karyawan_role_can_only_access_karyawan_pages(): void
    {
        $loginResponse = $this->post(route('login.submit'), [
            'email' => 'ahmad.fauzi@perusahaan.co.id',
            'password' => 'password123',
            'role' => 'karyawan',
        ]);

        $loginResponse->assertRedirect(route('karyawan.dashboard'));
        $this->assertEquals('karyawan', session('auth_role'));

        $dashboard = $this->get(route('karyawan.dashboard'));
        $dashboard->assertStatus(200);
        $dashboard->assertSee('Scan Presensi');
        $dashboard->assertDontSee('Command Center', false);

        $this->get(route('karyawan.scan'))->assertStatus(200);
        $this->get(route('karyawan.izin'))->assertStatus(200);
        $this->get(route('karyawan.riwayat'))->assertStatus(200);

        // Karyawan attempting to open manager page is redirected back to karyawan dashboard
        $forbidden = $this->get(route('admin.dashboard'));
        $forbidden->assertRedirect(route('karyawan.dashboard'));
        $forbidden->assertSessionHas('access_error');
    }

    /**
     * Manajer can access manager pages but is blocked from employee pages.
     */
    public function test_manajer_role_can_only_access_manajer_pages(): void
    {
        $loginResponse = $this->post(route('login.submit'), [
            'email' => 'manajer.hrd@perusahaan.co.id',
            'password' => 'password123',
            'role' => 'manajer',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertEquals('manajer', session('auth_role'));

        $adminPage = $this->get(route('admin.dashboard'));
        $adminPage->assertStatus(200);
        $adminPage->assertSee('Command Center', false);
        $adminPage->assertDontSee('Scan Presensi');

        // Manajer attempting to open karyawan pages is redirected back to manager dashboard
        $this->get(route('karyawan.dashboard'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('access_error');

        $this->get(route('karyawan.scan'))
            ->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Logout clears role session and redirects to login page.
     */
    public function test_logout_clears_role_session(): void
    {
        $this->withSession(['auth_role' => 'karyawan'])
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertNull(session('auth_role'));
    }
}
