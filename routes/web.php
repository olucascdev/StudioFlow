<?php

use App\Http\Controllers\ListController;
use App\Http\Controllers\TaskController;
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

    //Route para exibit listas especificas com as tasks
    Route::get('/lists/{lists}/tasks', [ListController::class,'show'])->name('lists.show');

    Route::prefix('lists/{lists}')->name('lists.')->group(function () {
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
        Route::patch('/tasks/{task}/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    });
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
