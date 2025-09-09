<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailQcApproval extends Notification
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
            ->subject("QC Approval Notification - IO {$this->io}")
            ->greeting("Dear {$notifiable->fullname},")
            ->line("Kami informasikan bahwa **Quality Control (QC)** untuk IO **{$this->io}** telah diperbarui.")
            ->line("Approval QC: **{$this->qc_result}**")
            ->line("Remarks: **{$this->remarks}**")
            ->action('Lihat Daftar QC', $this->link)
            ->line("Silakan klik tombol di atas untuk melihat QC secara lengkap.")
            ->salutation("Best Regards,\nSanwamas WMS Team");
    }
}
