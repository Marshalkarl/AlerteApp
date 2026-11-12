<?php
use App\Http\Controllers\{HomeController, AdminController, ClientController, TelegramController};
use App\Http\Middleware\{AdminOnly, ClientOnly};
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::post('/inscription', [HomeController::class, 'subscribe'])->middleware('throttle:5,1');
Route::get('/verifier/{token}', [HomeController::class, 'verifyForm']);
Route::post('/verifier/{token}', [HomeController::class, 'verify'])->middleware('throttle:10,1');
Route::post('/telegram/{secret}', [TelegramController::class, 'webhook']);

// Admin
Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin/connexion', [AdminController::class, 'login'])->middleware('throttle:5,1');
Route::post('/admin/alerte', [AdminController::class, 'send']);
Route::middleware(AdminOnly::class)->group(function () {
    Route::get('/admin/habitants', [AdminController::class, 'subscribers']);
    Route::delete('/admin/habitants/{subscriber}', [AdminController::class, 'destroySubscriber']);
    Route::post('/admin/deconnexion', [AdminController::class, 'logout']);
    Route::get('/admin/quartiers', [AdminController::class, 'quartiers']);
    Route::post('/admin/quartiers', [AdminController::class, 'storeQuartier']);
    Route::put('/admin/quartiers/{quartier}', [AdminController::class, 'updateQuartier']);
    Route::delete('/admin/quartiers/{quartier}', [AdminController::class, 'destroyQuartier']);
});

// Habitants
Route::get('/connexion', [ClientController::class, 'loginForm']);
Route::post('/connexion', [ClientController::class, 'requestCode'])->middleware('throttle:5,1');
Route::get('/connexion/code', [ClientController::class, 'codeForm']);
Route::post('/connexion/code', [ClientController::class, 'verifyCode'])->middleware('throttle:10,1');
Route::middleware(ClientOnly::class)->group(function () {
    Route::get('/mon-espace', [ClientController::class, 'dashboard']);
    Route::put('/mon-espace/quartier', [ClientController::class, 'updateQuartier']);
    Route::delete('/mon-espace', [ClientController::class, 'unsubscribe']);
    Route::post('/deconnexion', [ClientController::class, 'logout']);
});