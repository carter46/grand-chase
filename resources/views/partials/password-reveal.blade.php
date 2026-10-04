{{-- Adds a show/hide button to every password field that does not already have its own toggle.
     Opt out per field with data-no-reveal.
     With ['noSave' => true] (dashboard layouts) password fields are rendered as masked text fields and
     autofill hints are disabled, so browsers stop offering to save PINs, codes and passwords set for other people.
     Keep a field saveable with data-allow-save. --}}
<style>
    .pw-masked { -webkit-text-security: disc; text-security: disc; }
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
        var NO_SAVE = {{ !empty($noSave) ? 'true' : 'false' }};
        var MASK_SUPPORTED = !!(window.CSS && CSS.supports &&
            (CSS.supports('-webkit-text-security', 'disc') || CSS.supports('text-security', 'disc')));
        var EYE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        var EYE_OFF = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

        window.pwSetVisible = function (input, visible) {
            if (input.hasAttribute('data-pw-mask')) {
                input.classList.toggle('pw-masked', !visible);
            } else {
                input.type = visible ? 'text' : 'password';
            }
        };

        function isHidden(input) {
            return input.hasAttribute('data-pw-mask') ? input.classList.contains('pw-masked') : input.type === 'password';
        }

        function hasBoundType(input) {
            return input.hasAttribute(':type') || input.hasAttribute('x-bind:type');
        }

        function hasOwnToggle(input) {
            if (hasBoundType(input)) {
                return true;
            }
            var parent = input.parentElement;
            return !!(parent && parent.querySelector('button[type="button"]:not(.pw-reveal-btn)'));
        }

        function isOwnAccountForm(form) {
            return !!(form && form.querySelector('[data-allow-save], input[autocomplete="current-password"]'));
        }

        function blockSaving(el) {
            el.setAttribute('data-lpignore', 'true');
            el.setAttribute('data-1p-ignore', 'true');
            el.setAttribute('data-form-type', 'other');
        }

        function prepareMask(input) {
            if (input.hasAttribute('data-pw-mask')) {
                if (!MASK_SUPPORTED) {
                    input.removeAttribute('data-pw-mask');
                    input.classList.remove('pw-masked');
                    input.type = 'password';
                }
                return;
            }
            if (!NO_SAVE || !MASK_SUPPORTED || input.type !== 'password' ||
                input.hasAttribute('data-allow-save') || hasBoundType(input) || isOwnAccountForm(input.form)) {
                return;
            }
            input.type = 'text';
            input.setAttribute('data-pw-mask', '');
            input.classList.add('pw-masked');
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocapitalize', 'off');
            input.setAttribute('spellcheck', 'false');
            blockSaving(input);
        }

        function enhance(input) {
            if (input.dataset.pwReveal) {
                return;
            }
            prepareMask(input);
            input.dataset.pwReveal = '1';
            if (input.hasAttribute('data-no-reveal') || hasOwnToggle(input)) {
                return;
            }

            var wrap = document.createElement('span');
            wrap.className = 'pw-reveal-wrap';
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);
            input.classList.add('pw-reveal-input');

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pw-reveal-btn';
            btn.setAttribute('aria-label', 'Show password');
            btn.setAttribute('tabindex', '-1');
            btn.innerHTML = EYE;

            function render(visible) {
                window.pwSetVisible(input, visible);
                btn.innerHTML = visible ? EYE_OFF : EYE;
                btn.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            }

            btn.addEventListener('click', function () {
                render(isHidden(input));
            });
            wrap.appendChild(btn);

            if (input.form) {
                input.form.addEventListener('submit', function () {
                    render(false);
                });
            }
        }

        function hardenFields(root) {
            if (!NO_SAVE) {
                return;
            }
            var forms = root.tagName === 'FORM' ? [root] : Array.prototype.slice.call(root.querySelectorAll('form'));
            forms.forEach(function (form) {
                if (isOwnAccountForm(form)) {
                    return;
                }
                if (!form.hasAttribute('autocomplete')) {
                    form.setAttribute('autocomplete', 'off');
                }
                blockSaving(form);
                form.querySelectorAll('input[type="text"], input[type="number"], input[type="tel"], input[type="email"], input:not([type])').forEach(function (field) {
                    if (!field.hasAttribute('autocomplete')) {
                        field.setAttribute('autocomplete', 'off');
                    }
                    blockSaving(field);
                });
            });
        }

        function scan(root) {
            if (!root || !root.querySelectorAll) {
                return;
            }
            var selector = 'input[type="password"], input[data-pw-mask]';
            if (root.matches && root.matches(selector)) {
                enhance(root);
            }
            root.querySelectorAll(selector).forEach(enhance);
            hardenFields(root);
        }

        scan(document);
        document.addEventListener('DOMContentLoaded', function () {
            scan(document);
        });
        new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(scan);
            });
        }).observe(document.documentElement, { childList: true, subtree: true });
    })();
</script>
