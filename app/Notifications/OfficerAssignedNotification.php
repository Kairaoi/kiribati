<?php

namespace App\Notifications;

use App\Models\National\Eregistry\FileAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfficerAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FileAssignment $file_assignment
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
        $file = $this->file_assignment->fileCirculation->file;

        return (new MailMessage)
            ->subject('File Assigned to You')
            ->greeting('Mauri ' . $notifiable->first_name . ',')
            ->line('A file has been assigned to you in the Document Management System (DMS).')
            ->line('Subject: ' . $file->subject)
            ->line(
                'Assigned By: ' .
                ($this->file_assignment->assignedBy->name ?? 'N/A')
            )
            ->action(
                'Review File',
                route('registry.files.show', 
                      $file->id)
            )
            ->line(
                'Please sign in to the DMS to receive and process the file.'
            );
    }

    public function toArray(object $notifiable): array
    {
        $file = $this->file_assignment->fileCirculation->file;

        return [
            'type' => 'file_assignment',
            'title' => 'File assigned to you',
            'message' => 'A file has been assigned to you.',
            'file_id' => $file->id,
            'file_circulation_id' => $this->file_assignment->fileCirculation->id,
            'from_ministry' => $file->ministry?->name,
            'subject' => $file->subject,
            'url' => route('registry.files.show', 
                           $file->id),
        ];
    }
}