<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        
        // Optional email if configured
        if (config('mail.default') && config('mail.mailers.smtp.host')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('تأكيد طلب جديد #' . $this->order->id)
                    ->line('تم تأكيد طلب جديد في النظام.')
                    ->line('رقم الطلب: ' . $this->order->id)
                    ->line('قيمة الطلب: ' . $this->order->total_value . ' ريال')
                    ->action('عرض الطلب', route('orders.show', $this->order->id))
                    ->line('شكراً لاستخدامك نظامنا.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'تم تأكيد طلب جديد #' . $this->order->id,
            'order_id' => $this->order->id,
            'action_url' => route('orders.show', $this->order->id),
            'icon' => 'shopping-cart',
            'color' => 'green',
        ];
    }
}
