<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Interview;
use Carbon\Carbon;

class JadwalInterviewNotification extends Notification
{
    use Queueable;
    protected $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Interview $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Jadwal Interview & Tes Psikologi')
            ->greeting('Halo ' . $notifiable->nama . '!')
            ->line('Kami telah menjadwalkan interview dan tes psikologi untuk Anda sebagai bagian dari proses seleksi.')
            ->line('📅 Tanggal: ' . Carbon::parse($this->data->tanggal)->translatedFormat('l, d F Y'))
            ->line('⏰ Waktu: ' . Carbon::parse($this->data->waktu)->translatedFormat('H:i'))
            ->line('📍 Lokasi: ' . $this->data->lokasi)
            ->line('Pastikan Anda hadir tepat waktu dan membawa dokumen yang diperlukan.')
            ->line('Terima kasih.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
