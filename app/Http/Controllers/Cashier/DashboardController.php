<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        // Pending GCash payments — student uploaded proof, cashier hasn't acted yet
        $gcashPending = DocumentRequest::where('payment_method', 'gcash')
            ->where('status', 'payment_uploaded')
            ->count();

        // Pending bank transfer payments
        $bankPending = DocumentRequest::where('payment_method', 'bank_transfer')
            ->where('status', 'payment_uploaded')
            ->count();

        // Pending cash payments — student chose cash, cashier hasn't marked paid yet
        $cashPending = DocumentRequest::where('payment_method', 'cash')
            ->where('status', 'payment_method_set')
            ->count();

        // Total pending across all methods
        $totalPending = $gcashPending + $bankPending + $cashPending;

        // Verified today — payment_verified status, verified_at is today
        $verifiedToday = DocumentRequest::where('status', 'payment_verified')
            ->whereHas('officialReceipt', fn($q) =>
                $q->whereDate('issued_at', today())
            )->count();

        // Rejected today — payment_rejected status, verified_at is today
        $rejectedToday = DocumentRequest::where('status', 'payment_rejected')
            ->whereHas('paymentProof', fn($q) =>
                $q->whereDate('verified_at', today())
            )->count();

        // Recent pending payments for the table — latest 10
        $recentPending = DocumentRequest::whereIn('status', [
                'payment_uploaded',
                'payment_method_set',
            ])
            ->whereNotNull('payment_method')
            ->latest()
            ->limit(10)
            ->get();

        return view('cashier.dashboard', compact(
            'totalPending',
            'gcashPending',
            'bankPending',
            'cashPending',
            'verifiedToday',
            'rejectedToday',
            'recentPending'
        ));
    }

    public function account()
    {
        return view('cashier.account.index');
    }
}