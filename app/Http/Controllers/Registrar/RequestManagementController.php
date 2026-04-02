<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;

class RequestManagementController extends Controller
{
    public function index() { return view('registrar.requests.index'); }
    public function show($id) { return view('registrar.requests.show'); }
    public function updateStatus($id) {}
    public function markReceived($id) {}
}
