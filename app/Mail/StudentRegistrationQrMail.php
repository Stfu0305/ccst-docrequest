<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentRegistrationQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rawPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $rawPassword)
    {
        $this->user = $user;
        $this->rawPassword = $rawPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your CCST Account Credentials & Info',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Construct the data string for the QR code
        $qrData = "CCST Student Information\n";
        $qrData .= "Name: {$this->user->name}\n";
        $qrData .= "Student No: {$this->user->student_number}\n";
        $qrData .= "Strand & Section: {$this->user->strand} - {$this->user->grade_level} {$this->user->section}\n";
        $qrData .= "Email: {$this->user->email}\n";
        $qrData .= "Password: {$this->rawPassword}";

        // Use QRServer API to generate the QR code image URL
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrData);

        return new Content(
            view: 'emails.student-registration-qr',
            with: [
                'user' => $this->user,
                'rawPassword' => $this->rawPassword,
                'qrUrl' => $qrUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
