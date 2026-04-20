<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRegistrationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly User $newUser)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pendaftaran Jemaat Baru Menunggu Approval')
            ->line('Ada pendaftaran jemaat baru yang menunggu approval.')
            ->line('Nama: '.$this->newUser->name)
            ->line('Email: '.$this->newUser->email)
            ->action('Lihat Pendaftaran Pending', url('/admin/registrations/pending'));
    }
}
