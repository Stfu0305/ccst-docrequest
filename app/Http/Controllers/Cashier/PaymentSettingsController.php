<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;

class PaymentSettingsController extends Controller
{
    public function index() { return view('cashier.settings.index'); }
    public function update($id) {}
    public function toggle($id) {}
}
