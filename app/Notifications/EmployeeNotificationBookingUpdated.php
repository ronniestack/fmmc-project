<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class EmployeeNotificationBookingUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;
    public function __construct( $appointment)
    {
        $this->appointment =  $appointment;
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
        ->greeting('Hello '.$this->appointment->employee->user['name'])
        ->subject('Booking Status Updated: ' . $this->appointment['name'])
        ->line('Booking Status Updated for: ' . $this->appointment['name'])
        ->line('Name: '. $this->appointment['name'])
        ->line('Phone: '. $this->appointment['phone'])
        ->line('Service: '. $this->appointment->service['title'])
        ->line('Staff: '. $this->appointment->employee->user['name'])
        ->line('Amount: '. $this->appointment['amount'])
        ->line('Appointment Date : ' . Carbon::parse($this->appointment['booking_date'])->format('d M Y'))
        ->line('Slot Time: '. $this->appointment['booking_time'])
        ->line('Status: '. $this->appointment['status'])
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
