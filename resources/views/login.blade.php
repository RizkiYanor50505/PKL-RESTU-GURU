@php
    $selected = $selectedModule ?? 'absensi';
    $modules = [
        'absensi' => ['Absensi', 'Presensi GPS, pengajuan izin, dan persetujuan manajer.'],
        'chatbotwa' => ['Chatbot WA', 'Balasan otomatis untuk pesan WhatsApp.'],
        'penyewaanreklame' => ['Penyewaan Reklame', 'Titik reklame, jadwal tayang, dan transaksi sewa.'],
        'pemeliharaanalat' => ['Pelaporan & Pemeliharaan Alat', 'Laporan kerusakan dan jadwal perawatan alat.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Masuk · hadir.in</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/fill/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                    colors: { canvas: '#F4F4F0', obsidian: '#0B0F14', ink: '#0F1419', muted: '#4A5663', accent: '#10B981' },
                    transitionTimingFunction: { spring: 'cubic-bezier(0.32, 0.72, 0, 1)' },
                },
            },
        };
    </script>
    <style>
        @keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        .rise { animation: rise 0.8s cubic-bezier(0.32, 0.72, 0, 1) both; }
        @media (prefers-reduced-motion: reduce) { .rise { animation: none; } }
    </style>
</head>
<body class="min-h-[100dvh] bg-canvas font-sans text-ink">

<form method="POST" action="{{ route('login.submit') }}" id="loginForm" class="grid min-h-[100dvh] lg:h-[100dvh] lg:grid-cols-2 lg:overflow-hidden">
    @csrf
    <input type="hidden" name="role" id="roleInput" value="">

    <section class="flex flex-col justify-between gap-10 bg-obsidian px-6 py-8 text-white sm:px-10 lg:min-h-0 lg:gap-[2vh] lg:px-14 lg:py-[clamp(1rem,4.5vh,3.5rem)]">
        <div class="rise flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-accent text-obsidian">
                <i class="ph-fill ph-fingerprint text-xl"></i>
            </span>
            <span class="text-lg font-extrabold tracking-tight">hadir.in</span>
        </div>

        <div class="space-y-8 lg:space-y-[3vh]">
            <div class="rise space-y-4 lg:space-y-[1.5vh]" style="animation-delay: 80ms">
                <h1 class="text-5xl font-extrabold leading-[1.02] tracking-[-0.04em] sm:text-6xl lg:text-[clamp(2rem,7.5vh,4.5rem)]">
                    Satu akun,<br>empat layanan.
                </h1>
                <p class="max-w-md text-base leading-relaxed text-white/75 [@media(max-height:640px)]:hidden">
                    Pilih layanan yang ingin dibuka. Setelah masuk, Anda langsung tiba di halamannya.
                </p>
            </div>

            <fieldset class="space-y-3 lg:space-y-[1vh]">
                <legend class="sr-only">Pilih layanan</legend>
                @foreach ($modules as $key => [$name, $description])
                    <label class="rise group block cursor-pointer" style="animation-delay: {{ 160 + $loop->index * 70 }}ms">
                        <input type="radio" name="module" value="{{ $key }}" class="peer sr-only" @checked($selected === $key)>
                        <span class="flex items-center justify-between gap-4 rounded-3xl bg-white/[0.06] px-5 py-3 lg:py-[clamp(0.5rem,1.8vh,1rem)] transition-all duration-500 ease-spring hover:bg-white/10 active:scale-[0.99] peer-checked:bg-white/[0.16] peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-accent peer-checked:[&_.dot]:bg-accent peer-checked:[&_.dot]:text-obsidian">
                            <span class="block">
                                <span class="block text-base font-bold leading-tight lg:text-[clamp(0.9rem,2.2vh,1.125rem)]">{{ $name }}</span>
                                <span class="mt-0.5 block text-xs leading-snug text-white/70 sm:text-sm">{{ $description }}</span>
                            </span>
                            <span class="dot flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/15 text-transparent transition-all duration-500 ease-spring">
                                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.5 8.5l3 3 6-7"/></svg>
                            </span>
                        </span>
                    </label>
                @endforeach
            </fieldset>
        </div>
    </section>

    <section class="flex items-center px-6 py-12 sm:px-10 lg:min-h-0 lg:px-14 lg:py-[3vh]">
        <div class="rise mx-auto w-full max-w-md space-y-8 lg:space-y-[3vh]" style="animation-delay: 200ms">
            <div class="space-y-2">
                <h2 class="text-4xl font-extrabold tracking-[-0.03em]">Masuk</h2>
                <p class="text-base text-muted">Masukkan email dan kata sandi akun Anda.</p>
            </div>

            @if ($errors->any())
                <p role="alert" class="rounded-2xl bg-rose-100 px-4 py-3 text-sm font-semibold text-rose-900">{{ $errors->first() }}</p>
            @endif

            <div class="space-y-5 lg:space-y-[2vh]">
                <div class="space-y-2">
                    <label for="loginEmail" class="block text-sm font-semibold">Email</label>
                    <input type="email" name="email" id="loginEmail" required autocomplete="username"
                           value="{{ old('email', 'ahmad.fauzi@perusahaan.co.id') }}" placeholder="nama@perusahaan.co.id"
                           class="h-14 w-full rounded-2xl lg:h-[clamp(2.75rem,6.5vh,3.5rem)] bg-white px-5 text-base text-ink placeholder:text-muted/60 outline-none transition-all duration-500 ease-spring focus:ring-4 focus:ring-accent/30">
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="loginPassword" class="block text-sm font-semibold">Kata sandi</label>
                        <button type="button" id="togglePwd" class="rounded text-sm font-semibold text-muted underline-offset-4 hover:text-ink hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-accent">Tampilkan</button>
                    </div>
                    <input type="password" name="password" id="loginPassword" required autocomplete="current-password" value="password123"
                           class="h-14 w-full rounded-2xl lg:h-[clamp(2.75rem,6.5vh,3.5rem)] bg-white px-5 text-base text-ink outline-none transition-all duration-500 ease-spring focus:ring-4 focus:ring-accent/30">
                </div>
            </div>

            <button type="submit" class="group flex h-14 w-full lg:h-[clamp(2.75rem,6.5vh,3.5rem)] items-center justify-between rounded-full bg-obsidian py-1.5 pl-7 pr-1.5 text-base font-bold text-white transition-all duration-500 ease-spring hover:bg-ink active:scale-[0.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-obsidian">
                <span>Masuk</span>
                <span class="flex aspect-square h-full items-center justify-center rounded-full bg-accent text-obsidian transition-transform duration-500 ease-spring group-hover:translate-x-0.5 group-hover:-translate-y-px group-hover:scale-105">
                    <svg viewBox="0 0 16 16" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12L12 4M5.5 4H12v6.5"/></svg>
                </span>
            </button>

            <div class="space-y-3">
                <p class="text-sm text-muted">Akun demo, langsung masuk sebagai:</p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" data-demo-role="karyawan" data-demo-email="ahmad.fauzi@perusahaan.co.id"
                            class="h-12 rounded-2xl lg:h-[clamp(2.25rem,5.5vh,3rem)] bg-black/[0.05] text-sm font-bold transition-all duration-500 ease-spring hover:bg-black/10 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-accent">Karyawan</button>
                    <button type="button" data-demo-role="manajer" data-demo-email="manajer.hrd@perusahaan.co.id"
                            class="h-12 rounded-2xl lg:h-[clamp(2.25rem,5.5vh,3rem)] bg-black/[0.05] text-sm font-bold transition-all duration-500 ease-spring hover:bg-black/10 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-accent">Manajer</button>
                </div>
            </div>
        </div>
    </section>
</form>

<script>
    const form = document.getElementById('loginForm');
    const pwd = document.getElementById('loginPassword');
    const toggle = document.getElementById('togglePwd');

    toggle.addEventListener('click', () => {
        const hidden = pwd.type === 'password';
        pwd.type = hidden ? 'text' : 'password';
        toggle.textContent = hidden ? 'Sembunyikan' : 'Tampilkan';
    });

    document.querySelectorAll('[data-demo-role]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('roleInput').value = button.dataset.demoRole;
            document.getElementById('loginEmail').value = button.dataset.demoEmail;
            form.requestSubmit();
        });
    });
</script>
</body>
</html>
