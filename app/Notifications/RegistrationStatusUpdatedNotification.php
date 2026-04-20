<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $status,
        private readonly ?string $reason = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Update Status Pendaftaran Jemaat')
            ->greeting('Shalom, '.$notifiable->name)
            ->line('Status pendaftaran Anda: '.strtoupper($this->status));

        if ($this->reason) {
            $message->line('Catatan staff: '.$this->reason);
        }

        return $message->line(
            $this->status === 'approved'
                ? 'Akun Anda sudah aktif dan dapat mengakses fitur penuh.'
                : 'Silakan hubungi staff gereja untuk informasi lanjutan.'
        );
    }
}
