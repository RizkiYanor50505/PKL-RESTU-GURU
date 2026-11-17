<?php

namespace Tests\Feature;

use App\Models\Lokasi;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AttendanceScanTest extends TestCase
{
    use RefreshDatabase;

    private const NIK = 'PKL-TEST-001';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'attendance.qr_token' => 'LOC-TEST',
            'attendance.office_latitude' => -6.2,
            'attendance.office_longitude' => 106.8,
            'attendance.radius_meters' => 100,
            'attendance.max_accuracy_meters' => 50,
            'attendance.timezone' => 'Asia/Jakarta',
        ]);

        $this->travelToJakarta('07:50');
    }

    private function travelToJakarta(string $time): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-09 '.$time, 'Asia/Jakarta'));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function postScan(array $overrides = []): TestResponse
    {
        return $this->withEmployeeSession()->postJson(route('karyawan.scan.verify'), array_merge([
            'qr_token' => 'LOC-TEST',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'accuracy' => 10,
        ], $overrides));
    }

    private function withEmployeeSession(): static
    {
        return $this->withSession([
            'auth_role' => 'karyawan',
            'auth_user' => ['nik' => self::NIK, 'name' => 'Tester'],
        ]);
    }

    public function test_check_in_inside_radius_is_stored_as_on_time(): void
    {
        $this->postScan()
            ->assertOk()
            ->assertJson(['success' => true, 'type' => 'masuk', 'status' => Presensi::STATUS_ON_TIME, 'distance' => 0]);

        $this->assertDatabaseHas('presensis', [
            'nik' => self::NIK,
            'attendance_date' => '2026-11-09 00:00:00',
            'status' => Presensi::STATUS_ON_TIME,
            'late_minutes' => 0,
        ]);
    }

    public function test_check_in_associates_user_and_lokasi_records_when_present(): void
    {
        $user = User::factory()->create(['nik' => self::NIK]);
        $lokasi = Lokasi::factory()->create(['qr_token' => 'LOC-TEST']);

        $this->postScan()->assertOk();

        $presensi = Presensi::first();
        $this->assertNotNull($presensi);
        $this->assertEquals($user->id, $presensi->user_id);
        $this->assertEquals($lokasi->id, $presensi->lokasi_id);
        $this->assertTrue($presensi->user->is($user));
        $this->assertTrue($presensi->lokasi->is($lokasi));
    }

    public function test_check_in_after_tolerance_is_stored_as_late(): void
    {
        $this->travelToJakarta('08:30');

        $this->postScan()->assertOk()->assertJson(['status' => Presensi::STATUS_LATE, 'late_minutes' => 30]);
    }

    public function test_check_out_before_opening_time_is_rejected_at_layer_seven(): void
    {
        $this->postScan()->assertOk();

        $this->travelToJakarta('12:00');
        $this->postScan()->assertStatus(422)->assertJson(['layer' => 7]);

        $this->assertNull(Presensi::first()->pulang_at);
    }

    public function test_check_out_after_opening_time_is_stored(): void
    {
        $this->postScan()->assertOk();

        $this->travelToJakarta('17:05');
        $this->postScan()->assertOk()->assertJson(['type' => 'pulang']);

        $this->assertNotNull(Presensi::first()->pulang_at);
    }

    public function test_third_scan_on_same_day_is_rejected_at_layer_seven(): void
    {
        $this->postScan()->assertOk();
        $this->travelToJakarta('17:05');
        $this->postScan()->assertOk();

        $this->postScan()->assertStatus(422)->assertJson(['layer' => 7]);
        $this->assertDatabaseCount('presensis', 1);
    }

    public function test_scan_outside_radius_is_rejected_at_layer_six_and_not_stored(): void
    {
        $this->postScan(['latitude' => -6.204])
            ->assertStatus(422)
            ->assertJson(['success' => false, 'layer' => 6]);

        $this->assertDatabaseCount('presensis', 0);
    }

    public function test_scan_with_weak_gps_accuracy_is_rejected_at_layer_five(): void
    {
        $this->postScan(['accuracy' => 120])
            ->assertStatus(422)
            ->assertJson(['layer' => 5]);
    }

    public function test_scan_with_wrong_qr_token_is_rejected_at_layer_one(): void
    {
        $this->postScan(['qr_token' => 'LOC-SALAH'])
            ->assertStatus(422)
            ->assertJson(['layer' => 1]);
    }

    public function test_scan_page_renders_live_camera_reader(): void
    {
        $this->withEmployeeSession()
            ->get(route('karyawan.scan'))
            ->assertOk()
            ->assertSee('id="qrReader"', false)
            ->assertSee('scan\/verify', false);
    }

    public function test_dashboard_shows_today_check_in_time(): void
    {
        $this->withEmployeeSession()
            ->get(route('karyawan.dashboard'))
            ->assertOk()
            ->assertSee('Belum Presensi');

        $this->postScan()->assertOk();

        $this->withEmployeeSession()
            ->get(route('karyawan.dashboard'))
            ->assertOk()
            ->assertSee('07:50:00 WIB')
            ->assertSee('Hadir Tepat Waktu');
    }

    public function test_history_lists_only_own_records(): void
    {
        Presensi::factory()->late(24)->checkedOut()->create(['nik' => self::NIK, 'attendance_date' => '2026-11-05']);
        Presensi::factory()->create(['nik' => 'PKL-LAIN-999', 'attendance_date' => '2026-11-06', 'masuk_ip' => '9.9.9.9']);

        $this->withEmployeeSession()
            ->get(route('karyawan.riwayat'))
            ->assertOk()
            ->assertSee('Telat 24 Menit')
            ->assertDontSee('9.9.9.9');
    }

    public function test_scan_requires_karyawan_session(): void
    {
        $this->postJson(route('karyawan.scan.verify'), [])->assertRedirect();
    }

    public function test_manager_can_update_lokasi_coordinates_and_radius_for_subsequent_scans(): void
    {
        $this->withSession(['auth_role' => 'manajer'])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('id="adminGeofenceMap"', false);

        $this->withSession(['auth_role' => 'manajer'])
            ->postJson(route('admin.lokasi.update'), [
                'nama_lokasi' => 'Kantor Cabang Bandung',
                'latitude' => -6.9024812,
                'longitude' => 107.61881,
                'radius_meters' => 150,
                'qr_token' => 'LOC-BDG-01',
                'allowed_ips' => '103.14.88.19, 127.0.0.1',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'lokasi' => [
                    'nama_lokasi' => 'Kantor Cabang Bandung',
                    'radius_meters' => 150,
                    'qr_token' => 'LOC-BDG-01',
                ],
            ]);

        $this->assertDatabaseHas('lokasis', [
            'nama_lokasi' => 'Kantor Cabang Bandung',
            'qr_token' => 'LOC-BDG-01',
            'radius_meters' => 150,
        ]);

        // Scan at the new Bandung coordinate with the new token succeeds
        $this->postScan([
            'qr_token' => 'LOC-BDG-01',
            'latitude' => -6.9024812,
            'longitude' => 107.61881,
        ])->assertOk()->assertJson(['success' => true, 'radius' => 150]);
    }
}
