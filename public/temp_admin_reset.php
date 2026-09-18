<?php
/**
 * TEMPORARY admin password reset — DELETE THIS FILE when finished.
 *
 * URL (typical Laravel): https://yoursite.com/temp_admin_reset.php
 * If Hostinger maps public_html to the project root instead of /public,
 * move this file next to artisan and change the two require paths below
 * to __DIR__ . '/vendor/autoload.php' and __DIR__ . '/bootstrap/app.php'.
 *
 * Access key (change this): must match the form field.
 */
$ACCESS_KEY = 'RESET-NOW-DELETE-ME';

$message = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require __DIR__ . '/../vendor/autoload.php';
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $key = (string) ($_POST['access_key'] ?? '');
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password_confirmation'] ?? '');

        if (!hash_equals($ACCESS_KEY, $key)) {
            throw new RuntimeException('Wrong access key.');
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Enter a valid admin email.');
        }
        if (strlen($password) < 6) {
            throw new RuntimeException('Password must be at least 6 characters.');
        }
        if ($password !== $password2) {
            throw new RuntimeException('Passwords do not match.');
        }
        if (!Illuminate\Support\Facades\Schema::hasTable('admins')) {
            throw new RuntimeException('admins table not found.');
        }

        $admin = Illuminate\Support\Facades\DB::table('admins')
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();

        if (!$admin) {
            throw new RuntimeException('No admin found with that email.');
        }

        $payload = [
            'password' => Illuminate\Support\Facades\Hash::make($password),
        ];
        if (Illuminate\Support\Facades\Schema::hasColumn('admins', 'updated_at')) {
            $payload['updated_at'] = date('Y-m-d H:i:s');
        }
        if (Illuminate\Support\Facades\Schema::hasColumn('admins', 'password_token')) {
            $payload['password_token'] = null;
        }

        Illuminate\Support\Facades\DB::table('admins')
            ->where('id', $admin->id)
            ->update($payload);

        $ok = true;
        $message = 'Password updated for ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8')
            . '. Log in at /admin/login, then DELETE this file.';
    } catch (Throwable $e) {
        $ok = false;
        $message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Temp admin password reset</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #0f172a; }
        .box { width: 100%; max-width: 420px; margin: 24px; background: #fff; border-radius: 12px; padding: 28px; }
        h1 { margin: 0 0 8px; font-size: 20px; }
        .warn { margin: 0 0 20px; font-size: 13px; color: #b45309; background: #fffbeb; border: 1px solid #fcd34d;
            border-radius: 8px; padding: 10px 12px; }
        label { display: block; font-size: 13px; font-weight: 600; margin: 0 0 6px; }
        input { width: 100%; box-sizing: border-box; padding: 10px 12px; margin: 0 0 14px; border: 1px solid #cbd5e1;
            border-radius: 8px; font-size: 15px; }
        button { width: 100%; padding: 12px; border: 0; border-radius: 8px; background: #1e3a8a; color: #fff;
            font-size: 15px; font-weight: 600; cursor: pointer; }
        .msg { margin: 0 0 16px; padding: 10px 12px; border-radius: 8px; font-size: 14px; }
        .msg.ok { background: #ecfdf5; color: #065f46; border: 1px solid #6ee7b7; }
        .msg.err { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
    </style>
</head>
<body>
<div class="box">
    <h1>Temporary admin password reset</h1>
    <p class="warn">Delete <code>public/temp_admin_reset.php</code> as soon as you are done. Anyone who can open this URL can reset an admin password if they know the access key.</p>

    <?php if ($message !== ''): ?>
        <p class="msg <?= $ok ? 'ok' : 'err' ?>"><?= $ok ? $message : htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <label for="access_key">Access key</label>
        <input id="access_key" name="access_key" type="password" required placeholder="RESET-NOW-DELETE-ME">

        <label for="email">Admin email</label>
        <input id="email" name="email" type="email" required placeholder="admin@demo.com">

        <label for="password">New password</label>
        <input id="password" name="password" type="password" required minlength="6">

        <label for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="6">

        <button type="submit">Reset password</button>
    </form>
</div>
</body>
</html>
