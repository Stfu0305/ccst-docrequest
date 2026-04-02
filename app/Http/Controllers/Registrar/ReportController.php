<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index() { return view('registrar.reports.index'); }
    public function export() {}
}
