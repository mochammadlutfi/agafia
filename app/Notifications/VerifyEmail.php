<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;

class VerifyEmail extends VerifyEmailNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
            ->subject('Verifikasi Email')
            ->line('Terima kasih telah mendaftar di AGAFIA ADDA MANDIRI! Untuk menyelesaikan proses pendaftaran, kami perlu memastikan bahwa alamat email ini benar-benar milik Anda.')
            ->action('Verifikasi alamat email', $this->verificationUrl($notifiable))
            ->line('Jika Anda tidak mendaftar di AGAFIA ADDA MANDIRI, abaikan email ini.')
            ->line('Terima kasih telah bergabung dengan kami!');
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
