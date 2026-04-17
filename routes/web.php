<?php

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\ProfileController;

// Student Controllers
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\DocumentRequestController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\AppointmentController as StudentAppointment;
use App\Http\Controllers\Student\NotificationController as StudentNotification;

// Registrar Controllers
use App\Http\Controllers\Registrar\DashboardController as RegistrarDashboard;
use App\Http\Controllers\Registrar\RequestManagementController;
use App\Http\Controllers\Registrar\AppointmentController as RegistrarAppointment;
use App\Http\Controllers\Registrar\ReportController;
use App\Http\Controllers\Registrar\NotificationController as RegistrarNotification;

// Cashier Controllers
use App\Http\Controllers\Cashier\DashboardController as CashierDashboard;
use App\Http\Controllers\Cashier\PaymentVerificationController;
use App\Http\Controllers\Cashier\ReceiptController;
use App\Http\Controllers\Cashier\PaymentSettingsController;
use App\Http\Controllers\Cashier\NotificationController as CashierNotification;

// ═══════════════════════════════════════════════════════════════════
// PUBLIC ROUTES
// ═══════════════════════════════════════════════════════════════════

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// ─────────────────────────────────────────────────────────────────────────────
// POST-LOGIN REDIRECT
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match($role) {
        'student'   => redirect()->route('student.dashboard'),
        'registrar' => redirect()->route('registrar.dashboard'),
        'cashier'   => redirect()->route('cashier.dashboard'),
        default     => abort(403, 'Unknown role.'),
    };
})->middleware('auth')->name('dashboard');

// ─────────────────────────────────────────────────────────────────────────────
// STUDENT ROUTES
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {

    // ── Dashboard & Info ─────────────────────────────────────────────────────
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('/documents', [StudentDashboard::class, 'documents'])->name('documents.available');
    
    // ── Account ──────────────────────────────────────────────────────────────
    Route::get('/account', [StudentDashboard::class, 'account'])->name('account.index');
    Route::get('/account/photo', [StudentDashboard::class, 'servePhoto'])->name('account.photo');
    Route::post('/account/photo', [StudentDashboard::class, 'updatePhoto'])->name('account.updatePhoto');
    Route::patch('/account/profile', [StudentDashboard::class, 'updateProfile'])->name('account.updateProfile');
    Route::patch('/account/password', [StudentDashboard::class, 'updatePassword'])->name('account.updatePassword');

    // ── Document Requests ────────────────────────────────────────────────────
    Route::controller(DocumentRequestController::class)->group(function() {
        Route::get('/requests/create', 'create')->name('requests.create');
        Route::post('/requests', 'store')->name('requests.store');
        Route::get('/requests/{id}', 'show')->name('requests.show');
        Route::delete('/requests/{id}', 'cancel')->name('requests.cancel');
        Route::get('/history', 'history')->name('requests.history');
    });

    // ── Payments ─────────────────────────────────────────────────────────────
    Route::controller(PaymentController::class)->group(function() {
        Route::patch('/requests/{id}/payment-method', 'setMethod')->name('payments.setMethod');
        Route::get('/requests/{id}/upload', 'showUpload')->name('payments.showUpload');
        Route::post('/requests/{id}/upload', 'store')->name('payments.store');
        Route::post('/requests/{id}/reupload', 'reupload')->name('payments.reupload');
        Route::get('/receipts/{id}/download', 'downloadReceipt')->name('receipts.download');
    });

    // ── Appointments ─────────────────────────────────────────────────────────
    Route::controller(StudentAppointment::class)->group(function() {
        Route::post('/appointments', 'store')->name('appointments.store');
        Route::patch('/appointments/{id}', 'reschedule')->name('appointments.reschedule');
        Route::delete('/appointments/{id}', 'cancel')->name('appointments.cancel');
    });

    // ── Notifications (AJAX) ─────────────────────────────────────────────────
    Route::controller(StudentNotification::class)->group(function() {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::patch('/notifications/{id}/read', 'markOneRead')->name('notifications.markOneRead');
        Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// REGISTRAR ROUTES
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:registrar'])->prefix('registrar')->name('registrar.')->group(function () {

    // ── Dashboard & Account ──────────────────────────────────────────────────
    Route::get('/dashboard', [RegistrarDashboard::class, 'index'])->name('dashboard');
    Route::get('/account', [RegistrarDashboard::class, 'account'])->name('account');
    Route::patch('/announcements/{id}', [RegistrarDashboard::class, 'update'])->name('announcements.update');
    Route::patch('/announcements/{id}/publish', [RegistrarDashboard::class, 'publish'])->name('announcements.publish');

    // ── Request Management ───────────────────────────────────────────────────
    Route::controller(RequestManagementController::class)->group(function() {
        Route::get('/requests', 'index')->name('requests.index');
        Route::get('/requests/{id}', 'show')->name('requests.show');
        Route::patch('/requests/{id}/status', 'updateStatus')->name('requests.updateStatus');
        Route::patch('/requests/{id}/received', 'markReceived')->name('requests.markReceived');
    });

    // ── Appointments Management ─────────────────────────────────────────
    Route::controller(RegistrarAppointment::class)->group(function() {
        Route::get('/appointments', 'index')->name('appointments.index');
        Route::patch('/appointments/{id}/complete', 'complete')->name('appointments.complete');
        Route::patch('/appointments/{id}/missed', 'missed')->name('appointments.missed');
        Route::get('/time-slots/{id}/data', 'getSlotData')->name('timeslots.data');
        Route::post('/time-slots', 'storeSlot')->name('timeslots.store');
        Route::patch('/time-slots/{id}', 'updateSlot')->name('timeslots.update');
        Route::patch('/time-slots/{id}/toggle', 'toggleSlot')->name('timeslots.toggle');
    });

    // ── Reports ──────────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    

    // ── Notifications (AJAX) ─────────────────────────────────────────────────
    Route::controller(RegistrarNotification::class)->group(function() {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::patch('/notifications/{id}/read', 'markOneRead')->name('notifications.markOneRead');
        Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
    });

    // ── Account ──────────────────────────────────────────────────────────
    Route::get('/account', [RegistrarDashboard::class, 'account'])->name('account');
    Route::get('/account/photo', [RegistrarDashboard::class, 'servePhoto'])->name('account.photo');
    Route::post('/account/photo', [RegistrarDashboard::class, 'updatePhoto'])->name('account.updatePhoto');
    Route::patch('/account/profile', [RegistrarDashboard::class, 'updateProfile'])->name('account.updateProfile');
    Route::patch('/account/password', [RegistrarDashboard::class, 'updatePassword'])->name('account.updatePassword');

    Route::get('/payments/{id}/proof', [RequestManagementController::class, 'serveProof'])->name('payments.proof');
});

// ─────────────────────────────────────────────────────────────────────────────
// CASHIER ROUTES
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {

    // ── Dashboard & Account ──────────────────────────────────────────────────
    Route::get('/dashboard', [CashierDashboard::class, 'index'])->name('dashboard');
    Route::get('/account', [CashierDashboard::class, 'account'])->name('account');
    Route::patch('/account/profile', [CashierDashboard::class, 'updateProfile'])->name('account.updateProfile');
    Route::patch('/account/password', [CashierDashboard::class, 'updatePassword'])->name('account.updatePassword');
    Route::get('/account/photo', [CashierDashboard::class, 'servePhoto'])->name('account.photo');
    Route::post('/account/photo', [CashierDashboard::class, 'updatePhoto'])->name('account.updatePhoto');

    // ── Payment Verification ─────────────────────────────────────────────────
    Route::controller(PaymentVerificationController::class)->group(function() {
        Route::get('/payments', 'index')->name('payments.index');
        Route::get('/payments/{id}', 'show')->name('payments.show');
        Route::patch('/payments/{id}/verify', 'verify')->name('payments.verify');
        Route::patch('/payments/{id}/reject', 'reject')->name('payments.reject');
        Route::patch('/payments/{id}/mark-cash-paid', 'markCashPaid')->name('payments.markCashPaid');
        Route::get('/payments/{id}/proof', [PaymentVerificationController::class, 'serveProof'])->name('payments.proof');
    });

    // ── Receipts ─────────────────────────────────────────────────────────────
    Route::get('/receipts/{id}/download', [ReceiptController::class, 'download'])->name('receipts.download');

    // ── Payment Settings ─────────────────────────────────────────────────────
    Route::controller(PaymentSettingsController::class)->group(function() {
        Route::get('/settings', 'index')->name('settings.index');
        Route::patch('/settings/{id}', 'update')->name('settings.update');
        Route::patch('/settings/{id}/toggle', 'toggle')->name('settings.toggle');
    });

    // ── Notifications (AJAX) ─────────────────────────────────────────────────
    Route::controller(CashierNotification::class)->group(function() {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::patch('/notifications/{id}/read', 'markOneRead')->name('notifications.markOneRead');
        Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
    });

    Route::post('/settings', [PaymentSettingsController::class, 'store'])->name('settings.store');
    Route::delete('/settings/{id}', [PaymentSettingsController::class, 'destroy'])->name('settings.destroy');

});

// Debug route - Remove after testing
Route::get('/debug-notifications', function () {
    $user = App\Models\User::find(10); // Change to your student ID
    $notifications = $user->notifications()->latest()->take(10)->get();
    
    $result = [];
    foreach ($notifications as $n) {
        $result[] = [
            'id' => $n->id,
            'data' => $n->data,
            'created_at' => $n->created_at->toDateTimeString(),
        ];
    }
    
    return response()->json($result);
})->middleware('auth');
