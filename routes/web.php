<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\PensionsController;

Route::view('/', 'welcome');

// ===================== DASHBOARD =====================
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard',            [DashboardController::class, 'index'  ])->name('dashboard');
    Route::get('/dashboard/inputer',    [DashboardController::class, 'inputer'])->middleware('role:inputer')->name('dashboard.inputer');
    Route::get('/dashboard/checker',    [DashboardController::class, 'checker'])->middleware('role:checker')->name('dashboard.checker');
    Route::get('/dashboard/viewer',     [DashboardController::class, 'viewer' ])->middleware('role:viewer')->name('dashboard.viewer');
});

// ===================== PROFILE =====================
Route::middleware('auth')->group(function () {
    Route::get   ('/profile', [ProfileController::class, 'edit'   ])->name('profile.edit');
    Route::patch ('/profile', [ProfileController::class, 'update' ])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===================== PROJECTS =====================
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/projects', [ProjectController::class,'index'])->name('projects.index');

    Route::middleware('role:checker')->group(function () {
        Route::get   ('/projects/create',         [ProjectController::class,'create'])->name('projects.create');
        Route::post  ('/projects',                [ProjectController::class,'store' ])->name('projects.store');
        Route::get   ('/projects/{project}/edit', [ProjectController::class,'edit'  ])->name('projects.edit');
        Route::put   ('/projects/{project}',      [ProjectController::class,'update'])->name('projects.update');
        Route::delete('/projects/{project}',      [ProjectController::class,'destroy'])->name('projects.destroy');

        // Import project dari Excel (opsional)
        Route::get ('/projects/import',  [ProjectController::class,'importForm' ])->name('projects.import');
        Route::post('/projects/import',  [ProjectController::class,'importStore'])->name('projects.import.store');
    });
});

// ===================== DEBTORS =====================
Route::middleware(['auth','verified'])->group(function () {
    // List
    Route::get('/debtors', [DebtorController::class,'index'])->name('debtors.index');

    // Create + Import + Delete + Edit (khusus inputer/checker)
    Route::middleware('role:inputer|checker')->group(function () {
        Route::get ('/debtors/create',  [DebtorController::class,'create'     ])->name('debtors.create');
        Route::post('/debtors',         [DebtorController::class,'store'      ])->name('debtors.store');

        Route::get ('/debtors/import',  [DebtorController::class,'importForm' ])->name('debtors.import');
        Route::post('/debtors/import',  [DebtorController::class,'importStore'])->name('debtors.import.store');

        // EDIT / UPDATE
        Route::get ('/debtors/{debtor}/edit', [DebtorController::class,'edit'])
            ->whereNumber('debtor')->name('debtors.edit');
        Route::put ('/debtors/{debtor}',      [DebtorController::class,'update'])
            ->whereNumber('debtor')->name('debtors.update');

        // Hapus debitur
        Route::delete('/debtors/{debtor}', [DebtorController::class,'destroy'])
            ->whereNumber('debtor')->name('debtors.destroy');

        // Rollback batch import (opsional)
        Route::delete('/debtors/import/rollback/{batch}', [DebtorController::class,'rollbackImport'])
            ->name('debtors.import.rollback');
    });

    // Detail
    Route::get('/debtors/{debtor}', [DebtorController::class,'show'])
        ->whereNumber('debtor')->name('debtors.show');

    // Print schedule
    Route::get('/debtors/{debtor}/schedule/print', [DebtorController::class,'printSchedule'])
        ->whereNumber('debtor')->name('debtors.schedule.print');

    // Approve / Reject debitur (opsi) – checker saja
    Route::post('/debtors/{debtor}/approve', [DebtorController::class,'approve'])
        ->whereNumber('debtor')->middleware('role:checker')->name('debtors.approve');
});

// ===================== DATA COLLECTION =====================
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/collections/obligations', [CollectionController::class, 'obligations'])
        ->name('collections.obligations');
});

// ===================== PAYMENTS =====================
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/payments',        [PaymentsController::class, 'index'  ])->name('payments.index');
    Route::get('/payments/export', [PaymentsController::class, 'export' ])->name('payments.export');

    // approve / reject oleh CHECKER
    Route::middleware('role:checker')->group(function () {
        Route::post('/repayments/{repayment}/approve', [PaymentsController::class, 'approve'])->name('repayments.approve');
        Route::post('/repayments/{repayment}/reject',  [PaymentsController::class, 'reject' ])->name('repayments.reject');
    });
});

// ===================== REPORTING =====================
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/reporting',                          [ReportingController::class,'index'])->name('reporting.index');

    Route::get('/reporting/outstanding',              [ReportingController::class,'outstanding'])->name('reporting.outstanding');
    Route::get('/reporting/outstanding/export',       [ReportingController::class,'exportOutstanding'])->name('reporting.outstanding.export');

    Route::get('/reporting/arrears',                  [ReportingController::class,'arrears'])->name('reporting.arrears');
    Route::get('/reporting/arrears/export',           [ReportingController::class,'exportArrears'])->name('reporting.arrears.export');
});

// ===================== DATA PENSIUN =====================
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/pensions',                [PensionsController::class,'index'])->name('pensions.index');
    Route::get('/pensions/create',         [PensionsController::class,'create'])->name('pensions.create');
    Route::post('/pensions',               [PensionsController::class,'store'])->name('pensions.store');
    Route::get('/pensions/{pension}/edit', [PensionsController::class,'edit'])->name('pensions.edit');
    Route::put('/pensions/{pension}',      [PensionsController::class,'update'])->name('pensions.update');
    Route::delete('/pensions/{pension}',   [PensionsController::class,'destroy'])->name('pensions.destroy');

    Route::get('/pensions/import',         [PensionsController::class,'importForm'])->name('pensions.import.form');
    Route::post('/pensions/import',        [PensionsController::class,'importStore'])->name('pensions.import.store');
    Route::get('/pensions/template',       [PensionsController::class,'template'])->name('pensions.template');
});

require __DIR__.'/auth.php';
