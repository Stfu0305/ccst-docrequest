<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'type' => 'account_verified',
                'subject' => 'Your CCST DocRequest Account Has Been Verified',
                'body' => "Dear {{ student_name }},\n\nYour account has been verified by the registrar. You can now log in to the CCST DocRequest System.\n\nLogin URL: {{ login_url }}\n\nThank you for using CCST DocRequest!",
            ],
            [
                'type' => 'registration_pending',
                'subject' => 'New Student Registration - Pending Verification',
                'body' => "Hello Registrar,\n\nA new student has registered and is awaiting verification.\n\nStudent Name: {{ student_name }}\nStudent Number: {{ student_number }}\nEmail: {{ email }}\n\nPlease review the student's information and uploaded ID before approving.\n\nThank you for your prompt action.",
            ],
            [
                'type' => 'appointment_confirmed',
                'subject' => 'Appointment Confirmed - {{ reference_number }}',
                'body' => "Dear {{ student_name }},\n\nYour appointment has been confirmed.\n\n📅 Date: {{ appointment_date }}\n⏰ Time Slot: {{ time_slot }}\n💰 Amount Due: {{ amount }}\n\nPlease arrive on time and bring your school ID.\n\nThank you!",
            ],
            [
                'type' => 'appointment_reminder',
                'subject' => 'Reminder: Your Appointment is Tomorrow',
                'body' => "Dear {{ student_name }},\n\nThis is a reminder that your appointment is tomorrow.\n\n📅 Date: {{ appointment_date }}\n⏰ Time Slot: {{ time_slot }}\n💰 Amount Due: {{ amount }}\n\nDon't forget to bring your school ID and payment.\n\nThank you!",
            ],
            [
                'type' => 'document_ready',
                'subject' => 'Your Documents Are Ready for Pickup',
                'body' => "Dear {{ student_name }},\n\nYour documents are now ready for pickup!\n\n📄 Reference Number: {{ reference_number }}\n🔑 Claiming Number: {{ claiming_number }}\n\nPlease book an appointment through the system to schedule your pickup.\n\nThank you!",
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }
}