<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailMemoFinal extends Notification
{
    protected $memoNumber;
    protected $link;

    public function __construct($memoNumber, $link)
    {
        $this->memoNumber = $memoNumber;
        $this->link = $link;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Memo Approved: {$this->memoNumber}")
            ->greeting("Dear {$notifiable->fullname},")
            ->line("Memo dengan nomor **{$this->memoNumber}** telah selesai *disetujui*.")
            ->line("Anda dapat melihat detail memo dengan klik tombol berikut:")
            ->action('Lihat Memo', $this->link)
            ->line("Terima kasih atas kerja sama Anda.")
            ->salutation("Best Regards,\nSanwamas WMS Teams");
    }
}
