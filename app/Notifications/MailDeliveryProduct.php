<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailDeliveryProduct extends Notification
{

    protected $io;
    protected $link;

    public function __construct($io, $link)
    {
        $this->io = $io;
        $this->link = $link;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Delivery Information: IO {$this->io}")
            ->greeting("Dear **{$notifiable->fullname}**,")
            ->line("Kami ingin menginformasikan bahwa IO dengan nomor **{$this->io}** telah siap untuk proses pengiriman.")
            ->line("Silakan klik tombol berikut untuk melihat detail dan melakukan tracker status:")
            ->action('Lihat & Lacak Delivery', $this->link)
            ->line("Terima kasih atas perhatian dan tindak lanjutnya.")
            ->salutation("Best Regards,\nSanwamas WMS Teams");
    }
}
