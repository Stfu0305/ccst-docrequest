<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    use SendsDatabaseNotifications;

    /**
     * Display all email templates
     */
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('registrar.email-templates.index', compact('templates'));
    }

    /**
     * Update email template
     */
    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $template->update([
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        $this->sendNotificationToCurrentUser(
            "✅ Email template '{$template->type}' has been updated.",
            route('registrar.email-templates.index')
        );
        session()->flash('check_notifications', true);

        return response()->json(['success' => true, 'message' => 'Template updated successfully.']);
    }

    /**
     * Reset template to default
     */
    public function reset($id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        // Get default content based on type
        $defaults = $this->getDefaultTemplates();
        if (isset($defaults[$template->type])) {
            $template->update([
                'subject' => $defaults[$template->type]['subject'],
                'body' => $defaults[$template->type]['body'],
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);
        }

        $this->sendNotificationToCurrentUser(
            "✅ Email template '{$template->type}' has been reset to default.",
            route('registrar.email-templates.index')
        );
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.email-templates.index')
            ->with('success', 'Template reset to default.');
    }

    /**
     * Preview email template
     */
    public function preview($id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        // Sample data for preview
        $sampleData = [
            'student_name' => 'Juan Dela Cruz',
            'student_number' => '2024-0001',
            'reference_number' => 'DQST-2026-00042',
            'appointment_date' => now()->addDays(3)->format('F d, Y'),
            'time_slot' => '9:00 AM - 10:00 AM',
            'claiming_number' => 'CLM-4AKZ29',
            'registrar_name' => auth()->user()->name,
            'school_name' => 'Clark College of Science and Technology',
        ];

        return view('registrar.email-templates.preview', compact('template', 'sampleData'));
    }

    /**
     * Get default email templates
     */
    private function getDefaultTemplates()
    {
        return [
            'account_verified' => [
                'subject' => 'Your CCST DocRequest Account Has Been Verified',
                'body' => "Dear {{ student_name }},\n\nYour account has been verified by the registrar. You can now log in to the CCST DocRequest System.\n\nLogin URL: {{ login_url }}\n\nThank you for using CCST DocRequest!",
            ],
            'registration_pending' => [
                'subject' => 'New Student Registration - Pending Verification',
                'body' => "Hello Registrar,\n\nA new student has registered and is awaiting verification.\n\nStudent Name: {{ student_name }}\nStudent Number: {{ student_number }}\nEmail: {{ email }}\n\nPlease review the student's information and uploaded ID before approving.\n\nThank you for your prompt action.",
            ],
            'appointment_confirmed' => [
                'subject' => 'Appointment Confirmed - {{ reference_number }}',
                'body' => "Dear {{ student_name }},\n\nYour appointment has been confirmed.\n\n📅 Date: {{ appointment_date }}\n⏰ Time Slot: {{ time_slot }}\n💰 Amount Due: {{ amount }}\n\nPlease arrive on time and bring your school ID.\n\nThank you!",
            ],
            'appointment_reminder' => [
                'subject' => 'Reminder: Your Appointment is Tomorrow',
                'body' => "Dear {{ student_name }},\n\nThis is a reminder that your appointment is tomorrow.\n\n📅 Date: {{ appointment_date }}\n⏰ Time Slot: {{ time_slot }}\n💰 Amount Due: {{ amount }}\n\nDon't forget to bring your school ID and payment.\n\nThank you!",
            ],
            'document_ready' => [
                'subject' => 'Your Documents Are Ready for Pickup',
                'body' => "Dear {{ student_name }},\n\nYour documents are now ready for pickup!\n\n📄 Reference Number: {{ reference_number }}\n🔑 Claiming Number: {{ claiming_number }}\n\nPlease book an appointment through the system to schedule your pickup.\n\nThank you!",
            ],
        ];
    }

    public function show($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return response()->json($template);
    }

}