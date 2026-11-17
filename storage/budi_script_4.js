
        let toastTimer = null;
        function showToast(title, message, isSuccess = true) {
            const toast = document.getElementById('toastNotification');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const toastIconWrap = document.getElementById('toastIconWrap');
            const toastIconGlyph = document.getElementById('toastIconGlyph');

            if (!toast) return;
            toastTitle.textContent = title;
            toastMessage.textContent = message;

            if (isSuccess) {
                toastIconWrap.className = 'w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 flex items-center justify-center shrink-0 mt-0.5';
                toastIconGlyph.className = 'ph-fill ph-check-circle text-lg';
            } else {
                toastIconWrap.className = 'w-8 h-8 rounded-full bg-rose-500/20 text-rose-400 border border-rose-400/30 flex items-center justify-center shrink-0 mt-0.5';
                toastIconGlyph.className = 'ph-fill ph-warning-octagon text-lg';
            }

            toast.classList.remove('hidden');
            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(hideToast, 3600);
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) toast.classList.add('hidden');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                hideToast();
            }
        });

        // GPU-Safe IntersectionObserver for Staggered Entry Choreography
        document.addEventListener('DOMContentLoaded', function () {
            const items = document.querySelectorAll('.reveal-item');
            if (!('IntersectionObserver' in window)) {
                items.forEach(el => el.classList.add('is-visible'));
                return;
            }
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.06 });

            items.forEach((el, index) => {
                el.style.transitionDelay = `${Math.min(index * 55, 280)}ms`;
                observer.observe(el);
            });
        });
    