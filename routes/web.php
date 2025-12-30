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

// Workorders Group
Route::controller(WorkOrdersController::class)->group(function () {
    Route::get('/workorders/create', 'create')->name('workorders.create');
    Route::post('/workorders/create', 'store')->name('workorders.store');
    Route::get('/workorders/edit/{workOrder}','edit')->name('workorders.edit');
    Route::get('/workorders', 'index')->name('workorders');
    Route::get('/workorders/{workOrder}', 'show')->name('workorders.show');
})->middleware(['auth', 'verified']);

Route::get('v1/users', function() {
    return User::all();
});



require __DIR__.'/settings.php';
