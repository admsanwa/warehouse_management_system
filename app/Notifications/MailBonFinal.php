<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailBonFinal extends Notification
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
            ->subject("BON Approved: {$this->bonNumber}")
            ->greeting("Dear {$notifiable->fullname},")
            ->line("Proses approval untuk BON **{$this->bonNumber}** telah *disetujui* dan selesai diproses.")
            ->line("Anda dapat melihat detail BON dengan klik tombol berikut:")
            ->action('Lihat BON', $this->link)
            ->line("Terima kasih atas kerja sama dan partisipasi Anda.")
            ->salutation("Best Regards,\nSanwamas WMS Teams");
    }
}
