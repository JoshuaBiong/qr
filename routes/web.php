<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\VotersController;
use App\Http\Controllers\PageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Homepage', [
       
    ]);
});

Route::get('/verify/{uuid}', [PageController::class, 'verify'])->name('voters.verify');
Route::get('/cards', [PageController::class, 'viewcards'])->name('cards');





Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('voters', VotersController::class)->names('voters');
    Route::post('/voters/import', [VotersController::class, 'import'])->name('voters.import');
    Route::post('/voters/{uuid}/update-status', [VotersController::class, 'updateStatus'])->name('voters.update-status');
    Route::post('/voters/feature-status', [VotersController::class, 'featureStatus'])->name('voters.feature-status');


Route::get('/settings/scan-once', [SettingsController::class, 'getStatus']);
Route::post('/settings/scan-once', [SettingsController::class, 'toggleStatus']);





});

require __DIR__.'/auth.php';
