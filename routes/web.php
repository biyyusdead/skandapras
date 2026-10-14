<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Pengguna;
use App\Http\Controllers\Waka;
use App\Http\Controllers\Kepala;

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        Route::get('/school',  [Admin\SchoolController::class, 'edit'])->name('school.edit');
        Route::put('/school',  [Admin\SchoolController::class, 'update'])->name('school.update');

        // Bulk actions — WAJIB di atas resource
        Route::post('users/bulk-action',           [Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');
        Route::post('departments/bulk-action',     [Admin\DepartmentController::class, 'bulkAction'])->name('departments.bulk-action');
        Route::post('teachers/bulk-action',        [Admin\TeacherController::class, 'bulkAction'])->name('teachers.bulk-action');
        Route::post('achievements/bulk-action',    [Admin\AchievementController::class, 'bulkAction'])->name('achievements.bulk-action');
        Route::post('extracurriculars/bulk-action',[Admin\ExtracurricularController::class, 'bulkAction'])->name('extracurriculars.bulk-action');
        Route::post('rules/bulk-action',           [Admin\SchoolRuleController::class, 'bulkAction'])->name('rules.bulk-action');
        Route::post('internships/bulk-action',     [Admin\InternshipController::class, 'bulkAction'])->name('internships.bulk-action');
        Route::post('products/bulk-action',        [Admin\FeaturedProductController::class, 'bulkAction'])->name('products.bulk-action');
        Route::post('admissions/bulk-action',      [Admin\AdmissionController::class, 'bulkAction'])->name('admissions.bulk-action');
        Route::post('news/bulk-action',            [Admin\NewsController::class, 'bulkAction'])->name('news.bulk-action');
        Route::post('alumni/bulk-action',          [Admin\AlumniController::class, 'bulkAction'])->name('alumni.bulk-action');

        // Resource
        Route::resource('users',            Admin\UserController::class)->except(['show']);
        Route::resource('departments',      Admin\DepartmentController::class)->except(['show']);
        Route::resource('teachers',         Admin\TeacherController::class)->except(['show']);
        Route::resource('achievements',     Admin\AchievementController::class)->except(['show']);
        Route::resource('extracurriculars', Admin\ExtracurricularController::class)->except(['show']);
        Route::resource('rules',            Admin\SchoolRuleController::class)->except(['show']);
        Route::resource('internships',      Admin\InternshipController::class)->except(['show']);
        Route::resource('products',         Admin\FeaturedProductController::class)->except(['show']);
        Route::resource('admissions',       Admin\AdmissionController::class)->except(['show']);
        Route::resource('news',             Admin\NewsController::class)->except(['show']);
        Route::resource('alumni',           Admin\AlumniController::class)->except(['show']);


        Route::get('logs', [Admin\ActivityLogController::class, 'index'])->name('logs.index');
    });

    /*
    |--------------------------------------------------------------------------
    | WAKA SARPRAS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:waka_sarpras'])->prefix('waka')->name('waka.')->group(function () {
        Route::view('/dashboard', 'waka.dashboard')->name('dashboard');

        // Bulk actions — WAJIB di atas resource
        Route::post('rooms/bulk-action',      [Waka\RoomController::class, 'bulkAction'])->name('rooms.bulk-action');
        Route::post('items/bulk-action',      [Waka\ItemController::class, 'bulkAction'])->name('items.bulk-action');
        Route::post('categories/bulk-action', [Waka\CategoryController::class, 'bulkAction'])->name('categories.bulk-action');

        // Custom route sebelum resource — biar tidak dianggap resource show
        Route::delete('rooms/{room}/panorama', [Waka\RoomController::class, 'deletePanorama'])->name('rooms.delete-panorama');

        // Sarpras resource
        Route::resource('rooms',      Waka\RoomController::class);
        Route::resource('categories', Waka\CategoryController::class)->except(['show']);
        Route::resource('items',      Waka\ItemController::class)->except(['show']);

        // Laporan Kerusakan
        Route::get('/damage-reports',                  [Waka\DamageReportController::class, 'index'])->name('damage-reports.index');
        Route::get('/damage-reports/{report}',         [Waka\DamageReportController::class, 'show'])->name('damage-reports.show');
        Route::post('/damage-reports/{report}/verify', [Waka\DamageReportController::class, 'verify'])->name('damage-reports.verify');
        Route::post('/damage-reports/{report}/approve',[Waka\DamageReportController::class, 'approve'])->name('damage-reports.approve');
        Route::post('/damage-reports/{report}/reject', [Waka\DamageReportController::class, 'reject'])->name('damage-reports.reject');

        // Approval
        Route::get('/approvals', [Waka\ApprovalController::class, 'index'])->name('approvals.index');

        // Repair Report
        Route::get('/repair-reports',              [Waka\RepairReportController::class, 'index'])->name('repair-reports.index');
        Route::get('/repair-reports/create/{report}',[Waka\RepairReportController::class, 'create'])->name('repair-reports.create');
        Route::post('/repair-reports',             [Waka\RepairReportController::class, 'store'])->name('repair-reports.store');
        Route::get('/repair-reports/{repair}/edit',[Waka\RepairReportController::class, 'edit'])->name('repair-reports.edit');
        Route::put('/repair-reports/{repair}',     [Waka\RepairReportController::class, 'update'])->name('repair-reports.update');
        Route::delete('/repair-reports/{repair}',  [Waka\RepairReportController::class, 'destroy'])->name('repair-reports.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | KEPALA SEKOLAH
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepala')->name('kepala.')->group(function () {
        Route::view('/dashboard', 'kepala.dashboard')->name('dashboard');

        Route::get('/rooms',                   [Kepala\RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/{room}',            [Kepala\RoomController::class, 'show'])->name('rooms.show');
        Route::get('/damage-reports',          [Kepala\DamageReportController::class, 'index'])->name('damage-reports.index');
        Route::get('/damage-reports/{report}', [Kepala\DamageReportController::class, 'show'])->name('damage-reports.show');
        Route::get('/repair-reports',          [Kepala\RepairReportController::class, 'index'])->name('repair-reports.index');

        Route::get('/approvals',                    [Kepala\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{report}/approve',  [Kepala\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{report}/reject',   [Kepala\ApprovalController::class, 'reject'])->name('approvals.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | PENGGUNA RUANGAN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:pengguna_ruangan'])->prefix('pengguna')->name('pengguna.')->group(function () {
        Route::view('/dashboard', 'pengguna.dashboard')->name('dashboard');

        Route::get('/rooms',        [Pengguna\RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/{room}', [Pengguna\RoomController::class, 'show'])->name('rooms.show');

        Route::get('/reports',             [Pengguna\DamageReportController::class, 'index'])->name('damage-reports.index');
        Route::get('/reports/create',      [Pengguna\DamageReportController::class, 'create'])->name('damage-reports.create');
        Route::post('/reports',            [Pengguna\DamageReportController::class, 'store'])->name('damage-reports.store');
        Route::get('/reports/{report}',    [Pengguna\DamageReportController::class, 'show'])->name('damage-reports.show');
        Route::delete('/reports/{report}', [Pengguna\DamageReportController::class, 'destroy'])->name('damage-reports.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS & SETTINGS
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        Route::get('/notifications',              [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}/read',    [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all',    [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all');
        Route::delete('/notifications/{id}',      [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications',           [\App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');

        Route::get('/settings',                   [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile',           [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password',          [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');

        Route::get('/notifications/dropdown',     [\App\Http\Controllers\NotificationController::class, 'dropdown'])->name('notifications.dropdown');
    });


/*
|--------------------------------------------------------------------------
| PUBLIC FRONTEND
|--------------------------------------------------------------------------
*/
Route::name('public.')->group(function () {
    // Home
    Route::get('/', [\App\Http\Controllers\Public\HomeController::class, 'index'])->name('home');

    // Chatbot
    Route::post('/chatbot/chat', [\App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');

    // Tentang
    Route::prefix('tentang')->name('profile.')->group(function () {
        Route::get('/',          [\App\Http\Controllers\Public\ProfileController::class, 'index'])->name('index');
        Route::get('/sambutan',  [\App\Http\Controllers\Public\ProfileController::class, 'sambutan'])->name('sambutan');
        Route::get('/sejarah',   [\App\Http\Controllers\Public\ProfileController::class, 'sejarah'])->name('sejarah');
        Route::get('/struktur',  [\App\Http\Controllers\Public\ProfileController::class, 'struktur'])->name('struktur');
    });

    // Sarpras
    Route::get('/sarpras',                  [\App\Http\Controllers\Public\RoomController::class, 'index'])->name('rooms.index');
    Route::get('/sarpras/peta',             [\App\Http\Controllers\Public\RoomController::class, 'map'])->name('rooms.map');
    Route::get('/sarpras/pengaduan',        [\App\Http\Controllers\Public\DamageReportController::class, 'create'])->name('sarpras.pengaduan');
    Route::post('/sarpras/pengaduan',       [\App\Http\Controllers\Public\DamageReportController::class, 'store'])->name('sarpras.pengaduan.store');
    Route::get('/sarpras/{room:code}',      [\App\Http\Controllers\Public\RoomController::class, 'show'])->name('rooms.show');

    // Akademik
    Route::prefix('akademik')->name('akademik.')->group(function () {
        Route::get('/guru',                      [\App\Http\Controllers\Public\TeacherController::class, 'index'])->name('guru.index');
        Route::get('/jurusan',                   [\App\Http\Controllers\Public\DepartmentController::class, 'index'])->name('kompetensi.index');
        Route::get('/jurusan/{department:slug}', [\App\Http\Controllers\Public\DepartmentController::class, 'show'])->name('kompetensi.show');
        Route::get('/spmb',                      [\App\Http\Controllers\Public\AdmissionController::class, 'index'])->name('spmb.index');
        Route::get('/spmb/panduan',              [\App\Http\Controllers\Public\AdmissionController::class, 'panduan'])->name('spmb.panduan');
        Route::get('/produk',                    [\App\Http\Controllers\Public\FeaturedProductController::class, 'index'])->name('produk.index');
        Route::get('/produk/{product}',          [\App\Http\Controllers\Public\FeaturedProductController::class, 'show'])->name('produk.show');
        Route::get('/alumni',                    [\App\Http\Controllers\Public\AlumniController::class, 'index'])->name('alumni.index');
        Route::get('/alumni/{alumni:slug}',      [\App\Http\Controllers\Public\AlumniController::class, 'show'])->name('alumni.show');
    });

    // Kesiswaan
    Route::prefix('kesiswaan')->name('kesiswaan.')->group(function () {
        Route::get('/prestasi',        [\App\Http\Controllers\Public\AchievementController::class, 'index'])->name('prestasi.index');
        Route::get('/prestasi/{achievement}', [\App\Http\Controllers\Public\AchievementController::class, 'show'])->name('prestasi.show');
        Route::get('/ekstrakurikuler', [\App\Http\Controllers\Public\ExtracurricularController::class, 'index'])->name('ekstrakurikuler.index');
        Route::get('/ekstrakurikuler/{extracurricular}', [\App\Http\Controllers\Public\ExtracurricularController::class, 'show'])->name('ekstrakurikuler.show');
        Route::get('/pkl-bkk',         [\App\Http\Controllers\Public\InternshipController::class, 'index'])->name('pkl-bkk.index');
        Route::get('/pkl-bkk/{internship}', [\App\Http\Controllers\Public\InternshipController::class, 'show'])->name('pkl-bkk.show');
        Route::get('/tata-tertib',     [\App\Http\Controllers\Public\SchoolRuleController::class, 'index'])->name('tata-tertib.index');
    });

    // Berita
    Route::get('/berita',             [\App\Http\Controllers\Public\NewsController::class, 'index'])->name('news.index');
    Route::get('/berita/{news:slug}', [\App\Http\Controllers\Public\NewsController::class, 'show'])->name('news.show');
});

require __DIR__.'/auth.php';
