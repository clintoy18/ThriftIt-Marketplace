<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AppointmentStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $appointment;
    public $userId;
    public $message;

    public function __construct(Appointment $appointment, $userId, $message)
    {
        $this->appointment = $appointment;
        $this->userId = $userId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->appstatus,
            'message' => $this->message,
        ];
    }
}
