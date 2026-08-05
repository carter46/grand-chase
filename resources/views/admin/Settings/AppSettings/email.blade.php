<div class="row">
    <div class="col-md-12">
        <h4>Configuration</h4>
        <hr>
    </div>
    <div class="col-md-12">
        <form action="javascript:void(0)" method="POST" id="emailform">
            @csrf
            @method('PUT')
            <div class=" form-row">
                <div class="form-group col-md-12">
                    <div class="">
                        <h5 class="">Mail Server</h5>
                        <div class="selectgroup">
                            <label class="selectgroup-item">
                                <input type="radio" name="server" id="sendmailserver" value="sendmail"
                                    class="selectgroup-input" checked="">
                                <span class="selectgroup-button">Sendmail</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="server" id="smtpserver" value="smtp"
                                    class="selectgroup-input">
                                <span class="selectgroup-button">SMTP</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="">Email From</h5>
                    <input type="email" name="emailfrom" class="form-control  " value="{{ $settings->emailfrom }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="">Email From Name</h5>
                    <input type="text" name="emailfromname" class="form-control  "
                        value="{{ $settings->emailfromname }}" required>
                </div>
                <div class="form-group col-md-6 smtp d-none">
                    <h5 class="">SMTP Host</h5>
                    <input type="text" name="smtp_host" class="form-control   smtpinput"
                        value="{{ $settings->smtp_host }}">
                </div>
                <div class="form-group col-md-6 smtp d-none">
                    <h5 class="">SMPT Port</h5>
                    <input type="text" name="smtp_port" class="form-control   smtpinput"
                        value="{{ $settings->smtp_port }}">
                </div>
                <div class="form-group col-md-6 smtp d-none">
                    <h5 class="">SMPT Encryption</h5>
                    <input type="text" name="smtp_encrypt" class="form-control   smtpinput"
                        value="{{ $settings->smtp_encrypt }}">
                </div>
                <div class="form-group col-md-6 smtp d-none">
                    <h5 class="">SMPT Username</h5>
                    <input type="text" name="smtp_user" class="form-control   smtpinput"
                        value="{{ $settings->smtp_user }}">
                </div>
                <div class="form-group col-md-6 smtp d-none">
                    <h5 class="">SMPT Password</h5>
                    <input type="text" name="smtp_password" class="form-control   smtpinput"
                        value="{{ $settings->smtp_password }}">
                </div>
            </div>

            <div class="form-row border-top pt-4 mt-2">
                <div class="form-group col-12">
                    <h4 class="mb-1">Send Test Email</h4>
                    <p class="small text-muted mb-3">Save your SMTP settings first, then send a test to confirm delivery. Failures show here — they will not crash the page.</p>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="">Recipient</h5>
                    <input type="email" id="test_email_to" class="form-control"
                        value="{{ auth('admin')->user()->email ?? $settings->contact_email ?? '' }}"
                        placeholder="you@example.com">
                </div>
                <div class="form-group col-md-6 d-flex align-items-end">
                    <button type="button" id="send_test_email_btn" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fa fa-paper-plane"></i> Send Test Email
                    </button>
                </div>
                <div class="form-group col-12">
                    <div id="test_email_result" class="alert d-none mb-0" role="alert"></div>
                </div>
            </div>

            <hr>
            <div class="form-row">
                <div class="col-md-12">
                    <h4>Google Login Credentials</h4>
                    <hr>
                </div>
            </div>
            <div class=" form-row">
                <div class="form-group col-md-6">
                    <h5 class="">Client ID</h5>
                    <input type="text" name="google_id" class="form-control  " value="{{ $settings->google_id }}">
                    <small class=""> From console.cloud.google.com</small>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="">Client Secret</h5>
                    <input type="text" name="google_secret" class="form-control  "
                        value="{{ $settings->google_secret }}">
                    <small class=""> From console.cloud.google.com</small>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="">Redirect URL</h5>
                    <input type="text" name="google_redirect" class="form-control  "
                        value="{{ $settings->google_redirect }}">
                    <small class="">Set this to your Valid OAuth Redirect URI in console.cloud.google.com. Be sure
                        to replace the 'yoursite.com' with your website url </small>
                </div>
            </div>
            <div class="mt-4 form-row">
                <div class="col-md-12">
                    <h4>Google Captcha Credentials</h4>
                    <hr>
                </div>
            </div>
            <div class=" form-row">
                <div class="form-group col-md-6">
                    <h5 class="">Captcha Secret</h5>
                    <input type="text" name="capt_secret" class="form-control  "
                        value="{{ $settings->capt_secret }}">
                    <small class=""> From https://www.google.com/recaptcha/admin/create </small>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="">Captcha Site-Key</h5>
                    <input type="text" name="capt_sitekey" class="form-control  "
                        value="{{ $settings->capt_sitekey }}">
                    <small class=""> From https://www.google.com/recaptcha/admin/create</small>
                </div>
                <div class="form-group col-md-12">
                    <input type="submit" class="px-5 btn btn-primary btn-lg" value="Save">
                </div>
            </div>
        </form>
    </div>
</div>


@if ($settings->mail_server == 'sendmail')
    <script>
        document.getElementById("sendmailserver").checked = true;
    </script>
@else
    <script>
        document.getElementById("smtpserver").checked = true;
    </script>
@endif

<script>
(function () {
    var btn = document.getElementById('send_test_email_btn');
    var result = document.getElementById('test_email_result');
    if (!btn || !result) return;

    btn.addEventListener('click', function () {
        var to = (document.getElementById('test_email_to').value || '').trim();
        if (!to) {
            result.className = 'alert alert-danger mb-0';
            result.textContent = 'Enter a recipient email address.';
            result.classList.remove('d-none');
            return;
        }

        btn.disabled = true;
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
        result.classList.add('d-none');

        // Include current form SMTP fields so you can test before/after Save
        var payload = $('#emailform').serializeArray();
        payload.push({ name: 'test_to', value: to });

        $.ajax({
            url: "{{ route('sendtestemail') }}",
            type: 'POST',
            data: $.param(payload),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val() },
            success: function (response) {
                if (response.status === 200) {
                    result.className = 'alert alert-success mb-0';
                    result.textContent = response.message || ('Test email sent to ' + to);
                } else {
                    result.className = 'alert alert-danger mb-0';
                    result.textContent = response.message || 'Failed to send test email.';
                }
                result.classList.remove('d-none');
            },
            error: function (xhr) {
                var msg = 'Failed to send test email. Check SMTP settings.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                result.className = 'alert alert-danger mb-0';
                result.textContent = msg;
                result.classList.remove('d-none');
            },
            complete: function () {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        });
    });
})();
</script>