<?php

use App\Http\Controllers\API\AbsenController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfilPerusahaanController;
use App\Http\Controllers\API\RolesController;
use App\Http\Controllers\API\SettingRolesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

//yang belum login
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

//yang sudah login
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'change_password']);
    //absen
    Route::get('/cek_absen_hari_ini/{users_id}/{tanggal_hari_ini}', [AbsenController::class, 'cek_absen_hari_ini']);
    Route::get('/absen_history/{users_id}', [AbsenController::class, 'absen_history']);
});

//yang sudah login
Route::middleware(['auth:sanctum', 'Admin'])->prefix('admin')->group(function () {
    Route::post('/search-user', [AuthController::class, 'search']);
    Route::resource('roles', RolesController::class);
    Route::resource('setting_roles', SettingRolesController::class);
    Route::resource('profil_perusahaan', ProfilPerusahaanController::class);
    Route::resource('absen', AbsenController::class);
});

Route::middleware(['auth:sanctum', 'Pegawai'])->prefix('pegawai')->group(function () {
    Route::resource('roles', RolesController::class);
    Route::resource('setting_roles', SettingRolesController::class);
    Route::resource('profil_perusahaan', ProfilPerusahaanController::class);
    Route::resource('absen', AbsenController::class);
});
