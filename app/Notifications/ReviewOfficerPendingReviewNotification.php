<?php

namespace App\Notifications;

use App\Models\National\Eregistry\FileCirculation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewOfficerPendingReviewNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FileCirculation $circulation
    ) {
    }


    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $file = $this->circulation->file;

        return (new MailMessage)
            ->subject('File Pending Your Review')
            ->greeting('Mauri ' . $notifiable->first_name . ',')
            ->line('A file is pending your review in the Document Management System (DMS).')
            ->line('Subject: ' . $file->subject)
            ->line(
                'From: ' .
                ($file->ministry?->name ?? 'Another organisation')
            )
            ->action(
                'Review File',
                route('registry.files.show', 
                      $this->circulation->file->id)
            )
            ->line(
                'Please sign in to the DMS to receive and process the file.'
            );
    }

    public function toArray(object $notifiable): array
    {
        $file = $this->circulation->file;

        return [
            'type' => 'file_pending_review',
            'title' => 'File pending your review',
            'message' => 'A circulated file is pending your review.',
            'file_id' => $file->id,
            'file_circulation_id' => $this->circulation->id,
            'from_ministry' => $file->ministry?->name,
            'subject' => $file->subject,
            'url' => route('registry.files.show', 
                           $this->circulation->file->id),
        ];
    }
}