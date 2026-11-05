<?php

namespace App\Notifications;

use App\Models\Community;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Community $community) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $communityUrl = route('community.show', $this->community->id);

        return (new MailMessage)
            ->subject('Komunitas Anda Disetujui — IndoTech')
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Selamat! Pengajuan komunitas \"{$this->community->name}\" telah disetujui oleh Super Admin.")
            ->line('Komunitas Anda sekarang sudah aktif dan dapat ditemukan oleh pengguna lain di direktori IndoTech Community.')
            ->action('Lihat Komunitas', $communityUrl)
            ->line('Terima kasih telah berkontribusi membangun ekosistem teknologi Indonesia!');
    }
}
