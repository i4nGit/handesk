<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketCreated extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct($ticket) {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ( method_exists($notifiable, 'routeNotificationForSlack' ) && $notifiable->routeNotificationForSlack() != null) ? ['slack'] : ['mail'];
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
                    ->subject("Ticket created: {$this->ticket->requester->name}")
                    ->greeting(" ")
                    ->line(":: Reply above this line ::")
                    ->line($this->ticket->title)
                    ->line($this->ticket->body)
                    ->action('See the ticket', route("tickets.show", $this->ticket))
                    ->line('Thank you for using our application!')
                    ->line("ticket-id:{$this->ticket->id}.");
    }

    public function toSlack($notifiable)
    {
        return (new BaseTicketSlackMessage($this->ticket))
                ->content('Ticket created');
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
