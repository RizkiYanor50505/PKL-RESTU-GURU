<?php

namespace App\Http\Controllers\PemeliharaanAlat;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('branch')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->query('q');
                $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->query('branch_id')))
            ->when(
                in_array($request->query('role'), Role::values(), true),
                fn ($q) => $q->where('role', $request->query('role'))
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('pemeliharaanalat.users.index', [
            'users' => $users,
            'roles' => Role::options(),
            'branches' => Branch::orderBy('name')->get(['id', 'name', 'code', 'city']),
        ]);
    }

    public function create(): View
    {
        return view('pemeliharaanalat.users.create', $this->formData(new User(['role' => Role::Pegawai, 'status' => UserStatus::Aktif])));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()->route('pemeliharaanalat.users.index')
            ->with('success', "Akun {$user->name} berhasil dibuat.");
    }

    public function edit(User $user): View
    {
        return view('pemeliharaanalat.users.edit', $this->formData($user));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // Cegah admin mengunci dirinya sendiri dari sistem.
        if ($user->is($request->user()) && ($data['role'] !== Role::Admin->value || $data['status'] !== UserStatus::Aktif->value)) {
            return back()->withInput()->with('error', 'Anda tidak dapat menurunkan peran atau menonaktifkan akun Anda sendiri.');
        }

        $user->update($data);

        return redirect()->route('pemeliharaanalat.users.index')
            ->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => $request->input('password')]);

        return redirect()->route('pemeliharaanalat.users.edit', $user)
            ->with('success', "Kata sandi {$user->name} berhasil direset.");
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $newStatus = $user->isActive() ? UserStatus::Nonaktif : UserStatus::Aktif;
        $user->update(['status' => $newStatus]);

        return back()->with('success', "Akun {$user->name} sekarang {$newStatus->label()}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->reports()->exists()) {
            return back()->with('error', "Akun {$user->name} sudah memiliki laporan kendala. Nonaktifkan akun ini sebagai gantinya.");
        }

        $user->delete();

        return redirect()->route('pemeliharaanalat.users.index')
            ->with('success', "Akun {$user->name} berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(User $user): array
    {
        return [
            'user' => $user,
            'roles' => Role::options(),
            'statuses' => UserStatus::options(),
            'branches' => Branch::orderBy('name')->get(['id', 'name', 'code', 'city']),
        ];
    }
}
