<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order, public ?string $comment = null)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('orders.' . $this->order->id),
            new Channel('admin.orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'tracking_number' => $this->order->tracking_number,
            'shipping_carrier' => $this->order->shipping_carrier,
            'comment' => $this->comment,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
