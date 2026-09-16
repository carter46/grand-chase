<?php

use App\Http\Controllers\Admin\ClearCacheController;
use Illuminate\Support\Facades\Route;
use App\Models\Settings;
use Laravel\Fortify\Http\Controllers\NewPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/home.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
require __DIR__ . '/botman.php';

// 7th Trade Hub Protocol v1 — fixed paths (demo segment used for demo + owned)
Route::post('/api/7th-tradehub/v1/health', \App\Http\Controllers\SeventhTradeHub\HealthController::class)
    ->name('seventh-tradehub.health');
Route::post('/api/7th-tradehub/v1/subscription/sync', \App\Http\Controllers\SeventhTradeHub\SubscriptionSyncController::class)
    ->name('seventh-tradehub.subscription.sync');
Route::get('/auth/7th-tradehub/demo/consume', \App\Http\Controllers\SeventhTradeHub\ConsumeController::class)
    ->name('seventh-tradehub.consume');

//activate and deactivate Online Trader
Route::any('/activate', function () {
	return view('activate.index', [
		'settings' => Settings::where('id', '1')->first(),
	]);
});

Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
});

Route::get('register-license', [ClearCacheController::class, 'saveLicense']);

Route::any('/revoke', function () {
	return view('revoke.index');
});

Route::post('/reset-password', [NewPasswordController::class, 'store'])
	->middleware(['guest:' . config('fortify.guard')])
	->name('password.update');