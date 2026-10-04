{{-- Adds a show/hide button to every password field that does not already have its own toggle.
     Opt out per field with data-no-reveal. --}}
<style>
    .pw-reveal-wrap { position: relative; display: block; width: 100%; }
    .input-group > .pw-reveal-wrap { flex: 1 1 auto; width: 1%; min-width: 0; }
    .pw-reveal-wrap > input.pw-reveal-input { padding-right: 2.5rem !important; }
    .pw-reveal-btn {
        position: absolute; top: 50%; right: .6rem; transform: translateY(-50%);
        display: inline-flex; align-items: center; justify-content: center;
        width: 1.75rem; height: 1.75rem; padding: 0; margin: 0;
        border: 0; background: transparent; color: #8a8f98; cursor: pointer; z-index: 5; line-height: 1;
    }
    .pw-reveal-btn:hover, .pw-reveal-btn:focus { color: #5c6370; outline: none; }
    .pw-reveal-btn svg { width: 18px; height: 18px; pointer-events: none; }
</style>
<script>
    (function () {
        var EYE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        var EYE_OFF = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

        function hasOwnToggle(input) {
            if (input.hasAttribute(':type') || input.hasAttribute('x-bind:type')) {
                return true;
            }
            var parent = input.parentElement;
            return !!(parent && parent.querySelector('button[type="button"], [data-password-toggle]'));
        }

        function enhance(input) {
            if (input.dataset.pwReveal || input.hasAttribute('data-no-reveal') || hasOwnToggle(input)) {
                return;
            }
            input.dataset.pwReveal = '1';

            var wrap = document.createElement('span');
            wrap.className = 'pw-reveal-wrap';
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);
            input.classList.add('pw-reveal-input');

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pw-reveal-btn';
            btn.setAttribute('data-password-toggle', '');
            btn.setAttribute('aria-label', 'Show password');
            btn.setAttribute('tabindex', '-1');
            btn.innerHTML = EYE;
            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.innerHTML = show ? EYE_OFF : EYE;
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
            wrap.appendChild(btn);

            if (input.form) {
                input.form.addEventListener('submit', function () {
                    input.type = 'password';
                    btn.innerHTML = EYE;
                    btn.setAttribute('aria-label', 'Show password');
                });
            }
        }

        function scan(root) {
            if (!root || !root.querySelectorAll) {
                return;
            }
            if (root.matches && root.matches('input[type="password"]')) {
                enhance(root);
            }
            root.querySelectorAll('input[type="password"]').forEach(enhance);
        }

        function init() {
            scan(document);
            new MutationObserver(function (mutations) {
                mutations.forEach(function (m) {
                    m.addedNodes.forEach(scan);
                });
            }).observe(document.body, { childList: true, subtree: true });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
