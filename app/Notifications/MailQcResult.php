<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailQcResult extends Notification
{
    protected $io;
    protected $link;
    protected $qc_result;

    public function __construct($io, $qc_result, $remarks, $link)
    {
        $this->io = $io;
        $this->qc_result = $qc_result;
        $this->remarks = $remarks;
        $this->link = $link;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("QC Result Notification - IO {$this->io}")
            ->greeting("Dear {$notifiable->fullname},")
            ->line("Kami informasikan bahwa pengecekan **Quality Control (QC)** untuk IO **{$this->io}** telah selesai dilakukan.")
            ->line("Hasil QC: **{$this->qc_result}**")
            ->line("Remarks: **{$this->remarks}**")
            ->action('Lihat Daftar QC', $this->link)
            ->line("Silakan klik tombol di atas untuk melihat detail hasil pengecekan secara lengkap.")
            ->salutation("Best Regards,\nSanwamas WMS Team");
    }
}
