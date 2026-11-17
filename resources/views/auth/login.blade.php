@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="w-full max-w-md mx-auto reveal-item">

    <!-- Double-Bezel Machined Login Enclosure -->
    <div class="bezel-shell">
        <div class="bezel-core p-7 sm:p-9 space-y-7">

            <!-- Brand Identity Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-obsidian text-emerald-400 flex items-center justify-center shadow-lg">
                        <i class="ph-fill ph-fingerprint text-2xl"></i>
                    </div>
                    <div>
                        <span class="eyebrow-tag">hadir.in v2.4</span>
                        <h1 class="text-2xl font-extrabold text-ink tracking-tight mt-1">Masuk ke Akun</h1>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="role" id="loginRoleInput" value="">

                <div class="space-y-1.5">
                    <label for="loginEmail" class="block text-xs font-bold uppercase tracking-wider text-muted">
                        Email Perusahaan
                    </label>
                    <div class="relative">
                        <input type="email" name="email" id="loginEmail" required value="ahmad.fauzi@perusahaan.co.id"
                               placeholder="nama@perusahaan.co.id"
                               class="haptic-input pl-11">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-muted pointer-events-none">
                            <i class="ph-light ph-envelope-simple text-lg"></i>
                        </span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="loginPassword" class="block text-xs font-bold uppercase tracking-wider text-muted">
                            Kata Sandi
                        </label>
                        <button type="button" onclick="togglePassword()" id="togglePwdBtn" class="text-xs font-semibold text-accent hover:underline">
                            Tampilkan
                        </button>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="loginPassword" required value="password123"
                               class="haptic-input pl-11 font-mono">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-muted pointer-events-none">
                            <i class="ph-light ph-lock-key text-lg"></i>
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-island-primary w-full group">
                        <span>Masuk Sekarang</span>
                        <span class="icon-nest">
                            <i class="ph-light ph-arrow-up-right text-base"></i>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Quick Demo Account Triggers -->
            <div class="pt-5 border-t border-slate-900/[0.06] space-y-3">
                <span class="block text-[11px] font-bold uppercase tracking-widest text-muted text-center">
                    Akses Cepat Demo
                </span>
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" onclick="quickFill('karyawan')"
                            class="sub-tray px-3.5 py-2.5 flex items-center justify-between text-xs font-bold text-ink hover:bg-white transition-all duration-500 ease-spring active:scale-95 group">
                        <span class="flex items-center gap-2">
                            <i class="ph-light ph-user text-base text-accent"></i>
                            <span>Karyawan</span>
                        </span>
                        <i class="ph-light ph-caret-right text-muted group-hover:translate-x-0.5 transition-transform"></i>
                    </button>

                    <button type="button" onclick="quickFill('manajer')"
                            class="sub-tray px-3.5 py-2.5 flex items-center justify-between text-xs font-bold text-ink hover:bg-white transition-all duration-500 ease-spring active:scale-95 group">
                        <span class="flex items-center gap-2">
                            <i class="ph-light ph-shield-star text-base text-ink"></i>
                            <span>Manajer</span>
                        </span>
                        <i class="ph-light ph-caret-right text-muted group-hover:translate-x-0.5 transition-transform"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('loginPassword');
        const btn = document.getElementById('togglePwdBtn');
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        if (btn) {
            btn.textContent = isPwd ? 'Sembunyikan' : 'Tampilkan';
        }
    }

    function quickFill(role) {
        const email = document.getElementById('loginEmail');
        const roleInput = document.getElementById('loginRoleInput');
        const form = document.getElementById('loginForm');

        if (role === 'karyawan') {
            email.value = 'ahmad.fauzi@perusahaan.co.id';
            roleInput.value = 'karyawan';
        } else {
            email.value = 'manajer.hrd@perusahaan.co.id';
            roleInput.value = 'manajer';
        }

        form.submit();
    }
</script>
@endpush
