{{-- Sends an idle dashboard to the login page once the server session has expired,
     instead of letting the next click fail with "419 Page Expired".
     Usage: @include('partials.session-timeout', ['guard' => 'web' | 'admin']) --}}
@php
    $timeoutGuard = ($guard ?? 'web') === 'admin' ? 'admin' : 'web';
    $timeoutLoginUrl = route($timeoutGuard === 'admin' ? 'adminloginform' : 'login', ['session' => 'expired']);
    $timeoutMs = max(1, (int) config('session.lifetime', 120)) * 60 * 1000;
@endphp
<script>
    (function () {
        var GUARD = @json($timeoutGuard);
        var LOGIN_URL = @json($timeoutLoginUrl);
        var CHECK_URL = @json(route('session.check'));
        var LIFETIME_MS = {{ $timeoutMs }};
        var nativeFetch = window.fetch ? window.fetch.bind(window) : null;
        var timer = null;

        function arm() {
            clearTimeout(timer);
            timer = setTimeout(check, LIFETIME_MS + 5000);
        }

        function expire() {
            window.location.href = LOGIN_URL;
        }

        function check() {
            if (!nativeFetch) {
                expire();
                return;
            }
            nativeFetch(CHECK_URL, { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data && data[GUARD]) {
                        arm();
                    } else {
                        expire();
                    }
                })
                .catch(arm);
        }

        if (nativeFetch) {
            window.fetch = function () {
                var request = nativeFetch.apply(window, arguments);
                request.then(arm, function () {});
                return request;
            };
        }
        if (window.jQuery) {
            window.jQuery(document).ajaxComplete(arm);
        }

        arm();
    })();
</script>
