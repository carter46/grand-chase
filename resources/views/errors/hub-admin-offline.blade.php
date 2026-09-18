<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Website subscription</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;min-height:100vh;display:flex;align-items:center;justify-content:center;">
    <div style="max-width:560px;width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:32px;box-shadow:0 10px 30px rgba(15,23,42,0.06);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;text-align:center;">
        <p style="margin:0 0 8px;font-size:13px;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;color:#64748b;">
            Admin · subscription {{ $status !== '' ? $status : 'offline' }}
        </p>
        <p style="margin:0 0 24px;font-size:18px;line-height:1.55;color:#0f172a;">{{ $message }}</p>
        <a href="{{ $cta_href }}" target="_blank" rel="noopener"
           style="display:inline-block;padding:12px 20px;background:#1e3a8a;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;">{{ $cta_label }}</a>
    </div>
</body>
</html>
