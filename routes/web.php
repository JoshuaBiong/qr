<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\VotersController;
use App\Http\Controllers\PageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Homepage', [
       
    ]);
});

Route::get('/verify/{uuid}', [PageController::class, 'verify'])->name('voters.verify');





Route::get('/votersList', function () {
    return Inertia::render('Admin/Voters/VotersList');
})->middleware(['auth', 'verified'])->name('votersList');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('voters', VotersController::class)->names('voters');
});

require __DIR__.'/auth.php';
