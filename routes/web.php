<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ClientCrudController;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EntityInfoController;
use App\Http\Controllers\DropPointController;
use App\Http\Controllers\VehicleExpenseController;
use App\Http\Controllers\EntityInlineProxyController;
use App\Http\Controllers\FileController;
use App\Models\File;
use App\Http\Controllers\MasterApp\TimesheetController;
use App\Http\Controllers\MasterApp\TimesheetClockController;
use App\Http\Controllers\MasterApp\DashboardController;
use App\Http\Controllers\MasterApp\UserTimesheetController;
use App\Http\Controllers\UserController1;


Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('masterapp.dashboard');
    }

    return redirect()->route('login.view');
});

Route::redirect('/dashboard', '/');

// Protected Routes
Route::middleware('auth')->group(function () {

    /*  PROFILE  */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/changepassword', [ProfileController::class, 'changepassword'])->name('profile.changepassword');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// });
require __DIR__ . '/auth.php';
require __DIR__ . '/master-app.php';
