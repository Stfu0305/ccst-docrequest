<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$appointments = \App\Models\Appointment::where('document_request_id', 7)->get();

if ($appointments->count() > 0) {
    foreach ($appointments as $apt) {
        echo "Appointment ID: {$apt->id}\n";
        echo "Request ID: {$apt->document_request_id}\n";
        echo "Date: {$apt->appointment_date}\n";
        echo "Status: {$apt->status}\n";
        echo "---\n";
    }
} else {
    echo "No appointments found for request 7\n";
}

// Also check all appointments in the table
echo "\nTotal appointments in table: " . \App\Models\Appointment::count() . "\n";
?>
