<?php

use App\Http\Controllers\EmployeeSuperiorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Test login route for development
Route::get('/test-login', function () {
    Auth::loginUsingId(199210092023211029);

    $user = Auth::user();

    if ($user) {
        // return redirect('/hierarchy');
        return redirect('/dropdown_bawahan');
    }
    return 'No users found. Please run seeders first.';
});

Route::get('/access-denied', function () {
    return view('access-denied');
})->name('access-denied');

Route::POST('atur_bawahan', [EmployeeSuperiorController::class, 'atur_bawahan'])->name('atur_bawahan');

Route::GET('dropdown_bawahan', [EmployeeSuperiorController::class, 'dropdown_bawahan'])->name('dropdown_bawahan');

// Routes for superior management
Route::post('superior/store', [EmployeeSuperiorController::class, 'store'])->name('superior.store');
Route::delete('superior/destroy', [EmployeeSuperiorController::class, 'destroy'])->name('superior.destroy');

// Livewire superior management
Route::get('superior-livewire', function () {
    return view('pages.superior-livewire');
})->name('superior-livewire');

// Modern superior management with 2 columns
Route::get('superior-modern', function () {
    return view('pages.superior-modern');
})->name('superior-modern');

// Inertia.js hierarchy management routes
use App\Http\Controllers\InertiaHierarchyController;

Route::prefix('hierarchy')->name('hierarchy.')->middleware('auth')->group(function () {
    Route::get('/', [InertiaHierarchyController::class, 'index'])->name('index');
    Route::post('/move-employee', [InertiaHierarchyController::class, 'moveEmployee'])->name('move');
    Route::post('/set-superior', [InertiaHierarchyController::class, 'setSuperior'])->name('set-superior');
    Route::delete('/remove-superior', [InertiaHierarchyController::class, 'removeSuperior'])->name('remove-superior');
});
