<?php

namespace App\Events;

use App\Listeners\EventListener;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;

class OAuthEventListener extends EventListener
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function handle(WeChatUserAuthorized $event)
    {
        parent::handle($event);
        $user = $event->getUser();
        \Log::info(var_export($user,true));
        dd(var_export($user,true));
    }
}
