<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\National\Eregistry\FileCirculation;

class FileDeliveredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    public function __construct(
        public FileCirculation $circulation,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $file = $this->circulation->file;

        return (new MailMessage)
            ->subject('New File Awaiting Receipt')
            ->greeting('Mauri ' . $notifiable->first_name . ',')
            ->line(
                'A new file has been dispatched to your ministry through the Document Management System (DMS).'
            )
            ->line('Subject: ' . $file->subject)
            ->line(
                'From: ' .
                ($file->ministry?->name ?? 'Another ministry')
            )
            ->action(
                'View and Receive File',
                route(
                    'registry.files.show',
                    $this->circulation->file->id
                )
            )
            ->line(
                'Please sign in to the Document Management System to receive and process the file.'
            );
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $file = $this->circulation->file;

        return [
            'type' => 'file_delivered',
            'title' => 'New file delivered and awaiting receipt',
            'message' => 'A new file has been dispatched to your ministry.',
            'subject' => $file->subject,
            'file_id' => $file->id,
            'dispatch_id' => $this->circulation->id,
            'from_ministry' => $file->ministry?->name,
            'url' => route(
                'registry.files.show',
                $this->circulation->file->id
            ),
        ];
    }
}
