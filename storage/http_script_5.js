
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
