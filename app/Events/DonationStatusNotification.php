<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Models\Donation;

class DonationStatusNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $donation;
    public $receiverId;
    public $status;

    /**
     * Create a new event instance.
     *
     * @param Donation $donation
     * @param int $receiverId
     * @param string $status
     */
    public function __construct(Donation $donation, $receiverId, $status)
    {
        $this->donation = $donation;
        $this->receiverId = $receiverId;
        $this->status = $status; // "approved" or "rejected"
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications-channel.' . $this->receiverId);
    }

    /**
     * The event name to broadcast as.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'donation.status.notification';
    }

    /**
     * The data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // Create a friendly message based on status
        $message = $this->status === 'approved' 
            ? "Your donation {$this->donation->name} has been approved" 
            : "Your donation {$this->donation->name} has been rejected";

        return [
            'id'         => $this->donation->id,
            'name'       => $this->donation->name,
            'status'     => $this->status,
            'message'    => $message,
            'from_user'  => 'Thrift-IT',
            'created_at' => $this->donation->created_at->diffForHumans(),
        ];
    }
}

