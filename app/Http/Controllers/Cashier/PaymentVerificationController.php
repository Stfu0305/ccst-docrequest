<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;

class PaymentVerificationController extends Controller
{
    public function index() { return view('cashier.payments.index'); }
    public function show($id) { return view('cashier.payments.show'); }
    public function verify($id) {}
    public function reject($id) {}
    public function markCashPaid($id) {}
}
