<?php

namespace App\Notifications;

use App\Models\Community;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Community $community,
        public string $reason
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $myCommunitiesUrl = route('community.my');

        return (new MailMessage)
            ->subject('Pengajuan Komunitas Ditolak — IndoTech')
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Pengajuan komunitas \"{$this->community->name}\" belum dapat disetujui oleh Super Admin.")
            ->line("Alasan penolakan: {$this->reason}")
            ->line('Jangan berkecil hati! Anda dapat memperbaiki informasi komunitas Anda dan mengajukannya kembali.')
            ->action('Buka Komunitas Saya', $myCommunitiesUrl)
            ->line('Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi tim kami.');
    }
}
