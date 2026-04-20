<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationCredentialsNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $plainPassword)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pendaftaran Jemaat Berhasil')
            ->greeting('Shalom, '.$notifiable->name)
            ->line('Pendaftaran Anda berhasil. Akun Anda menunggu approval dari staff gereja.')
            ->line('Email: '.$notifiable->email)
            ->line('Password sementara: '.$this->plainPassword)
            ->line('Silakan login lalu ganti password Anda di menu settings setelah akun disetujui.');
    }
}
