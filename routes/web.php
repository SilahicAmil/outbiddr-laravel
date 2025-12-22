<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\WorkOrdersController;
use App\Models\User;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/workorders/create', [WorkOrdersController::class, 'create'])->name('workorders.create');
Route::get('/workorders', [WorkOrdersController::class, 'index'])->name('workorders');
Route::get('/workorders/{workOrder}', [WorkOrdersController::class, 'show']);

Route::get('v1/users', function() {
    return User::all();
});



require __DIR__.'/settings.php';
