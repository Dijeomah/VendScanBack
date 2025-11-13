<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $recipientRole;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     * @param string $recipientRole (vendor|server)
     * @return void
     */
    public function __construct(Order $order, string $recipientRole)
    {
        $this->order = $order;
        $this->recipientRole = $recipientRole;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'business_name' => $this->order->businessLink->business_data->business_name ?? $this->order->businessLink->business_link,
            'table_name' => $this->order->table?->table_name,
            'customer_name' => $this->order->customer_name,
            'total' => $this->order->total,
            'items_count' => $this->order->orderItems->count(),
            'payment_method' => $this->order->payment_method,
            'payment_status' => $this->order->payment_status,
            'status' => $this->order->status,
            'recipient_role' => $this->recipientRole,
            'message' => $this->getMessage(),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Get notification message based on role
     *
     * @return string
     */
    private function getMessage(): string
    {
        $tableName = $this->order->table?->table_name ?? 'a customer';
        $itemsCount = $this->order->orderItems->count();
        $total = number_format($this->order->total, 2);
        $businessName = $this->order->businessLink->business_data->business_name ?? $this->order->businessLink->business_link;

        if ($this->recipientRole === 'server') {
            return "New order #{$this->order->order_number} from {$tableName} - {$itemsCount} item(s), \${$total}";
        }

        return "New order #{$this->order->order_number} from {$tableName} at {$businessName} - \${$total}";
    }
}
