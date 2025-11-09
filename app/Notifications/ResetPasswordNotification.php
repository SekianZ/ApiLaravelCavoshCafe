<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = config('app.frontend_url') . "/password-reset/{$this->token}?email={$notifiable->email}";

        return (new MailMessage)
            ->subject('Restablecer Contraseña - Cavosh Cafe')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Recibiste este correo porque solicitaste restablecer tu contraseña.')
            ->line('Por favor, haz clic en el botón de abajo para restablecer tu contraseña.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no solicitaste restablecer tu contraseña, ignora este mensaje.')
            ->salutation('Saludos, Equipo de Cavosh Cafe');
    }
}
