<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyDigest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        /** @var array{request_count: int, chat_activity: int, active_programs: int} */
        public readonly array $summary,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'StudHub Daily Digest — ' . now()->format('F j, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.daily-digest',
            with: [
                'displayName' => $this->user->preferredDisplayName(),
                'summary' => $this->summary,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
