<?php
use App\Http\Controllers\{HomeController, AdminController, TelegramController};
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::post('/inscription', [HomeController::class, 'subscribe'])->middleware('throttle:5,1');
Route::get('/verifier/{token}', [HomeController::class, 'verifyForm']);
Route::post('/verifier/{token}', [HomeController::class, 'verify'])->middleware('throttle:10,1');
Route::post('/telegram/{secret}', [TelegramController::class, 'webhook']);
Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin/connexion', [AdminController::class, 'login'])->middleware('throttle:5,1');
Route::post('/admin/alerte', [AdminController::class, 'send']);
