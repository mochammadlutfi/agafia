<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegisterNotification extends Notification
{
    use Queueable;

    protected string $status;
    protected ?string $catatan;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $status, ?string $catatan = null)
    {
        $this->status = $status;
        $this->catatan = $catatan;
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
        $statusUpper = strtoupper($this->status);

        $mail = (new MailMessage)
            ->subject('Hasil Screening Pendaftaran')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftar')
            ->line("Hasil screening profil Anda: **{$statusUpper}**");

        if ($this->status === 'ditolak') {
            $mail->line('Mohon untuk memperbaiki profil Anda sesuai dengan catatan berikut:');

            if ($this->catatan) {
                $mail->line("> " . $this->catatan);
            }

            $mail->line('Silakan login ke aplikasi dan lakukan perbaikan.');
        } elseif ($this->status === 'diterima') {
            $mail->line('Selamat! Anda dapat melanjutkan ke tahap selanjutnya.');
            $mail->line('Kami akan segera menginformasikan jadwal interview dan tes psikologi.');
        }

        return $mail->line('Terima kasih atas perhatian Anda.');
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
