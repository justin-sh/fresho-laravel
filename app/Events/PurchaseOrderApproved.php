<?php

namespace App\Events;

use App\Models\PurchaseOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderApproved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('HoC.Stock'),
            new PrivateChannel('HoC.Stock2'), // Not Working,maybe as not login logic
        ];
    }

    public function broadcastWith():array
    {
        return ['title' => $this->purchaseOrder->title , 'id'=>$this->purchaseOrder->id];
    }

    public function broadcastAs():string
    {
        return 'PoApproved';
    }
}
