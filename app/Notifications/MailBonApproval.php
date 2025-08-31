<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailBonApproval extends Notification
{

    protected $bonNumber;
    protected $link;

    public function __construct($bonNumber, $link)
    {
        $this->bonNumber = $bonNumber;
        $this->link = $link;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Approval Request: BON {$this->bonNumber}")
            ->greeting("Dear **{$notifiable->fullname}**,")
            ->line("Anda menerima permintaan approval dengan nomor BON **{$this->bonNumber}**.")
            ->line("Silakan klik tombol berikut untuk melihat detail dan melakukan approval:")
            ->action('Lihat & Approve', $this->link)
            ->line("Terima kasih atas perhatian dan tindak lanjutnya.")
            ->salutation("Best Regards,\nSanwamas WMS Teams");
    }
}
