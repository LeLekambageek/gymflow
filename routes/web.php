<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;

// Accueil → login
Route::get('/', fn() => redirect()->route('login'));

// Alias dashboard → redirige selon le rôle après login
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'owner') {
        return redirect()->route('owner.dashboard');
    }
    return redirect()->route('manager.dashboard');
})->middleware(['auth'])->name('dashboard');

// Routes Breeze (profil)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── ROUTES GYMFLOW ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── GÉRANT ──────────────────────────────────────────────────
    Route::get('/manager',                [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::post('/manager/register',      [ManagerController::class, 'registerMember'])->name('manager.register');
    Route::post('/manager/renew',         [ManagerController::class, 'renewSubscription'])->name('manager.renew');
    Route::get('/manager/search-members', [ManagerController::class, 'searchMembers'])->name('manager.search');
    Route::get('/manager/membres',        [ManagerController::class, 'members'])->name('manager.members');

    // ── PROPRIÉTAIRE UNIQUEMENT ──────────────────────────────────
    Route::middleware(['role:owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard',          [OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/revenue/detail',     [OwnerDashboardController::class, 'revenueDetail'])->name('revenue.detail');
        Route::get('/subscriptions',      [OwnerDashboardController::class, 'subscriptionOverview'])->name('subscriptions');

        // Gestion du staff (gérants)
        Route::get('/staff',              [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff',             [StaffController::class, 'store'])->name('staff.store');
        Route::put('/staff/{user}',       [StaffController::class, 'update'])->name('staff.update');
        Route::put('/staff/{user}/password', [StaffController::class, 'updatePassword'])->name('staff.password');
        Route::delete('/staff/{user}',    [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // ── ADMINISTRATEUR UNIQUEMENT ────────────────────────────────
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard',                  [AdminController::class, 'index'])->name('dashboard');
        Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('reset-password');
        Route::post('/users/{user}/credentials',   [AdminController::class, 'updateCredentials'])->name('update-credentials');
        Route::get('/users/{user}',               [AdminController::class, 'show'])->name('user.show');
    });

    // ── MODULES COMMUNS ──────────────────────────────────────────
    Route::resource('members', MemberController::class);

    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/',             [SubscriptionController::class, 'index'])->name('index');
        Route::post('/',            [SubscriptionController::class, 'store'])->name('store');
        Route::post('/{sub}/cancel',[SubscriptionController::class, 'cancel'])->name('cancel');
        Route::get('/plans',        [SubscriptionController::class, 'plans'])->name('plans');
        Route::post('/plans',       [SubscriptionController::class, 'storePlan'])->name('plans.store');
    });

    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/',                               [CourseController::class, 'index'])->name('index');
        Route::post('/',                              [CourseController::class, 'store'])->name('store');
        Route::get('/schedule',                       [CourseController::class, 'schedule'])->name('schedule');
        Route::post('/sessions',                      [CourseController::class, 'storeSession'])->name('sessions.store');
        Route::get('/sessions/{session}/attendance',  [CourseController::class, 'sessionAttendance'])->name('sessions.attendance');
        Route::post('/bookings/{booking}/attendance', [CourseController::class, 'markAttendance'])->name('bookings.attendance');
    });

    Route::resource('coaches', CoachController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/',       [PaymentController::class, 'index'])->name('index');
        Route::post('/',      [PaymentController::class, 'store'])->name('store');
        Route::get('/report', [PaymentController::class, 'report'])->name('report');
    });
});

require __DIR__.'/auth.php';
