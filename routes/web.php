<?php

use App\Http\Controllers\ListController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/lists', [ListController::class,'index'])->name('lists.index');
    Route::post('/lists', [ListController::class,'store'])->name('lists.store');
    Route::put('/lists/{lists}', [ListController::class,'update'])->name('lists.update');
    Route::delete('/lists/{lists}', [ListController::class,'destroy'])->name('lists.destroy');
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
