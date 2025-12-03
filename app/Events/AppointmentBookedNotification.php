<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Models\Appointment;
use Illuminate\Support\Facades\Route;

class AppointmentBookedNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;
    public $receiverId;

    public function __construct(Appointment $appointment, $receiverId)
    {
        $this->appointment = $appointment->relationLoaded('user') ? $appointment : $appointment->load('user');
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications-channel.' . $this->receiverId);
    }

    public function broadcastAs()
    {
        return 'appointment.booked.notification';
    }

    public function broadcastWith()
    {
        return [
            'id'            => $this->appointment->appointmentid,
            'from_user'     => $this->appointment->user->fname . ' ' . $this->appointment->user->lname,
            'apptype'       => $this->appointment->apptype,
            'appdate'       => optional($this->appointment->appdate)->format('M d, Y g:i A'),
            'message'       => "{$this->appointment->user->fname} {$this->appointment->user->lname} booked a new appointment.",
            'link'          => route('appointments.myAppointments'),
            'created_at'    => $this->appointment->created_at->diffForHumans(),
        ];
    }
}


