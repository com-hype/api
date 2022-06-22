<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class ReceiveMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $message;
    public $user;

    public function __construct(String $title, User $user, $message)
    {
        $this->title = $title;
        $this->message = $message;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        $channelName = 'user.' . $this->user->id;
        return [$channelName];
    }

    public function broadcastAs()
    {
        return 'user.receive.message';
    }
}
