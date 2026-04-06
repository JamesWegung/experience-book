<?php

use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Event\ImportFromExcelController as EventImportFromExcel;
use App\Http\Controllers\Event\DownloadTemplateController as EventDownloadTemplate;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('overview.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'role:admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/overview', [OverviewController::class, 'index'])->name('overview.index');

    Route::get('events/export-template', EventDownloadTemplate::class)->name('events.template');
    Route::post('events/import-by-xlsx', EventImportFromExcel::class)->name('events.importByXlsx');
    Route::resource('events', EventController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
