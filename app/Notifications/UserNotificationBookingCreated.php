<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Carbon\Carbon;

class UserNotificationBookingCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {


        return (new MailMessage)
        ->greeting('Hello '.$this->appointment['name'])
        ->line('Thanks for your booking with us')
        ->subject('New Booking Created' )
        ->line('**Appointment Details:**')  // make content strong
        ->line('Name: '. $this->appointment['name'])
        ->line('Phone: '. $this->appointment['phone'])
        // ->line('Category: '. $this->appointment->service->category['title'])
        ->line('Service: '. $this->appointment->service['title'])
        ->line('Amount: '. $this->appointment['amount'])
        ->line('Appointment Date : ' . Carbon::parse($this->appointment['booking_date'])->format('d M Y'))
        ->line('Slot Time: '. $this->appointment['booking_time'])
        ->line('Thank you for using our application !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
