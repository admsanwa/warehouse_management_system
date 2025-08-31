<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailMemoApproval extends Notification
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
            ->subject("Approval Request: Memo {$this->memoNumber}")
            ->greeting("Dear **{$notifiable->fullname}**,")
            ->line("Anda menerima permintaan approval memo dengan nomor **{$this->memoNumber}**.")
            ->line("Silakan klik tombol berikut untuk melihat detail memo dan melakukan approval:")
            ->action('Lihat & Approve', $this->link)
            ->line("Terima kasih atas perhatian dan tindak lanjutnya.")
            ->salutation("Best Regards,\nSanwamas WMS Teams");
    }
}
