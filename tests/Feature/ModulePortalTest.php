<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModulePortalTest extends TestCase
{
    public function test_guest_is_redirected_to_login_from_modules(): void
    {
        foreach (['chatbotwa.dashboard', 'pemeliharaanalat.dashboard', 'penyewaanreklame.dashboard'] as $routeName) {
            $this->get(route($routeName))->assertRedirect(route('login'));
        }
    }

    public function test_logged_in_user_can_open_every_module_dashboard(): void
    {
        $session = ['auth_role' => 'karyawan', 'auth_user' => ['name' => 'Tester', 'nik' => 'T-1']];

        $this->withSession($session)->get(route('chatbotwa.dashboard'))->assertOk()->assertSee('Chatbot WA');
        $this->withSession($session)->get(route('pemeliharaanalat.dashboard'))->assertOk()->assertSee('Pelaporan & Pemeliharaan Alat', false);
        $this->withSession($session)->get(route('penyewaanreklame.dashboard'))->assertOk()->assertSee('Penyewaan Reklame');
    }

    public function test_login_redirects_to_selected_module_dashboard(): void
    {
        $modules = [
            'chatbotwa' => 'chatbotwa.dashboard',
            'pemeliharaanalat' => 'pemeliharaanalat.dashboard',
            'penyewaanreklame' => 'penyewaanreklame.dashboard',
        ];

        foreach ($modules as $moduleKey => $expectedRoute) {
            $response = $this->post(route('login.submit'), [
                'email' => 'ahmad.fauzi@perusahaan.co.id',
                'password' => 'password123',
                'module' => $moduleKey,
            ]);

            $response->assertRedirect(route($expectedRoute));
            $this->session(['auth_role' => null]);
        }
    }

    public function test_login_page_lists_every_module_and_preselects_the_requested_one(): void
    {
        $this->get(route('login', ['module' => 'chatbotwa']))
            ->assertOk()
            ->assertSee('value="chatbotwa" class="peer sr-only" checked', false)
            ->assertSee('value="absensi"', false)
            ->assertSee('value="pemeliharaanalat"', false)
            ->assertSee('value="penyewaanreklame"', false);
    }
}
