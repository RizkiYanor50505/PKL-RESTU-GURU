<?php

namespace Tests\Feature;

use App\Models\PengajuanIzin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function employee(): User
    {
        return User::factory()->create(['role' => 'karyawan', 'nik' => 'PKL-LEAVE-001', 'division' => 'Keuangan']);
    }

    private function manager(): User
    {
        return User::factory()->create(['role' => 'manajer', 'nik' => 'MGR-LEAVE-001']);
    }

    private function asEmployee(User $user): static
    {
        return $this->withSession(['auth_role' => 'karyawan', 'auth_user' => ['nik' => $user->nik, 'name' => $user->name]]);
    }

    private function asManager(User $user): static
    {
        return $this->withSession(['auth_role' => 'manajer', 'auth_user' => ['nik' => $user->nik, 'name' => $user->name]]);
    }

    public function test_employee_leave_request_is_stored_as_pending(): void
    {
        Storage::fake('local');
        $employee = $this->employee();

        $this->asEmployee($employee)->postJson(route('karyawan.izin.store'), [
            'jenis' => 'Sakit',
            'tgl_mulai' => '2026-11-09',
            'tgl_selesai' => '2026-11-10',
            'alasan' => 'Demam tinggi.',
            'foto_bukti' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ])->assertCreated()->assertJson(['success' => true]);

        $leave = PengajuanIzin::query()->firstOrFail();
        $this->assertSame($employee->id, $leave->user_id);
        $this->assertSame(PengajuanIzin::STATUS_PENDING, $leave->status_izin);
        Storage::disk('local')->assertExists($leave->foto_bukti);
    }

    public function test_leave_request_rejects_end_date_before_start_date(): void
    {
        $this->asEmployee($this->employee())->postJson(route('karyawan.izin.store'), [
            'jenis' => 'Sakit',
            'tgl_mulai' => '2026-11-10',
            'tgl_selesai' => '2026-11-09',
            'alasan' => 'Demam.',
        ])->assertUnprocessable()->assertJsonValidationErrors('tgl_selesai');

        $this->assertDatabaseCount('pengajuan_izins', 0);
    }

    public function test_employee_page_lists_only_own_requests(): void
    {
        $employee = $this->employee();
        PengajuanIzin::factory()->create(['user_id' => $employee->id, 'alasan' => 'Alasan milik saya']);
        PengajuanIzin::factory()->create(['alasan' => 'Alasan orang lain']);

        $this->asEmployee($employee)->get(route('karyawan.izin'))
            ->assertOk()
            ->assertSee('Alasan milik saya')
            ->assertDontSee('Alasan orang lain');
    }

    public function test_manager_approval_is_persisted_and_leaves_the_queue(): void
    {
        $manager = $this->manager();
        $leave = PengajuanIzin::factory()->create(['alasan' => 'Antrean uji persetujuan']);

        $this->asManager($manager)->get(route('admin.dashboard'))->assertSee('Antrean uji persetujuan');

        $this->asManager($manager)->postJson(route('admin.izin.decide', $leave), ['status' => 'disetujui'])
            ->assertOk()
            ->assertJson(['success' => true, 'pending_count' => 0]);

        $leave->refresh();
        $this->assertSame(PengajuanIzin::STATUS_APPROVED, $leave->status_izin);
        $this->assertSame($manager->id, $leave->approved_by);

        $this->asManager($manager)->get(route('admin.dashboard'))->assertDontSee('Antrean uji persetujuan');
    }

    public function test_manager_rejection_stores_the_note(): void
    {
        $leave = PengajuanIzin::factory()->create();

        $this->asManager($this->manager())->postJson(route('admin.izin.decide', $leave), [
            'status' => 'ditolak',
            'catatan_admin' => 'Bertabrakan dengan jadwal rilis.',
        ])->assertOk();

        $this->assertDatabaseHas('pengajuan_izins', [
            'id' => $leave->id,
            'status_izin' => 'ditolak',
            'catatan_admin' => 'Bertabrakan dengan jadwal rilis.',
        ]);
    }

    public function test_decided_request_cannot_be_decided_again(): void
    {
        $leave = PengajuanIzin::factory()->approved()->create();

        $this->asManager($this->manager())->postJson(route('admin.izin.decide', $leave), ['status' => 'ditolak'])
            ->assertStatus(409);

        $this->assertSame(PengajuanIzin::STATUS_APPROVED, $leave->fresh()->status_izin);
    }

    public function test_employee_cannot_decide_requests(): void
    {
        $leave = PengajuanIzin::factory()->create();

        $this->asEmployee($this->employee())->postJson(route('admin.izin.decide', $leave), ['status' => 'disetujui'])
            ->assertRedirect();

        $this->assertSame(PengajuanIzin::STATUS_PENDING, $leave->fresh()->status_izin);
    }

    public function test_history_shows_leave_records_and_counts_approved_days(): void
    {
        $employee = $this->employee();

        PengajuanIzin::factory()->approved()->create([
            'user_id' => $employee->id,
            'jenis' => 'Sakit',
            'tgl_mulai' => '2026-11-10',
            'tgl_selesai' => '2026-11-11',
            'alasan' => 'Sakit flu berat',
        ]);

        PengajuanIzin::factory()->create([
            'user_id' => $employee->id,
            'jenis' => 'Izin Khusus',
            'tgl_mulai' => '2026-11-15',
            'tgl_selesai' => '2026-11-15',
            'status_izin' => PengajuanIzin::STATUS_REJECTED,
            'alasan' => 'Ada urusan keluarga',
        ]);

        $response = $this->asEmployee($employee)->get(route('karyawan.riwayat'));
        $response->assertOk();
        $response->assertSee('Sakit flu berat');
        $response->assertSee('Ada urusan keluarga');
        $response->assertSee('Izin &amp; Cuti (2)', false);
        $response->assertViewHas('totalLeaveDays', 2);
    }

    public function test_employee_can_view_own_leave_proof_and_not_others(): void
    {
        Storage::fake('local');
        $employee1 = $this->employee();
        $employee2 = User::factory()->create(['role' => 'karyawan', 'nik' => 'PKL-OTHER-002']);

        $path = Storage::disk('local')->putFile('bukti-izin', UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'));
        $leave = PengajuanIzin::factory()->create([
            'user_id' => $employee1->id,
            'foto_bukti' => $path,
        ]);

        $this->asEmployee($employee1)->get(route('karyawan.izin.bukti', $leave))->assertOk();
        $this->asEmployee($employee2)->get(route('karyawan.izin.bukti', $leave))->assertForbidden();
    }
}
