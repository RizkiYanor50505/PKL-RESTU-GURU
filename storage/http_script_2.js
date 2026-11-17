
        window.addEventListener('error', function (e) {
            console.error('JS ERROR:', e);
            let b = document.getElementById('debugJsErrorBanner');
            if (!b) {
                b = document.createElement('div');
                b.id = 'debugJsErrorBanner';
                b.style.cssText = 'position:fixed;bottom:16px;left:16px;z-index:999999;background:#991B1B;color:#FFF;padding:10px 16px;border-radius:12px;font-family:monospace;font-size:11px;max-width:85vw;box-shadow:0 8px 24px rgba(0,0,0,0.4);border:1px solid #EF4444;display:flex;align-items:flex-start;gap:12px;';
                document.body ? document.body.appendChild(b) : document.addEventListener('DOMContentLoaded', () => document.body.appendChild(b));
            }
            b.innerHTML = '<div style="flex:1;"><strong>⚠️ JavaScript Error:</strong> ' + (e.message || 'Error') + '<br><small>' + (e.filename ? e.filename.split('/').pop() : '') + ' (baris ' + (e.lineno || '?') + ')</small></div>' +
                '<button type="button" onclick="this.parentElement.remove()" style="background:transparent;border:none;color:#FFF;font-size:16px;cursor:pointer;line-height:1;padding:0 4px;">&times;</button>';
        });
    