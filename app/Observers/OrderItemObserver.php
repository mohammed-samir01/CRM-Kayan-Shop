<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    public function created(OrderItem $orderItem): void
    {
        $this->recalcTotals($orderItem);
    }

    public function updated(OrderItem $orderItem): void
    {
        $this->recalcTotals($orderItem);
    }

    public function deleted(OrderItem $orderItem): void
    {
        $this->recalcTotals($orderItem);
    }

    protected function recalcTotals(OrderItem $orderItem): void
    {
        $order = $orderItem->order;
        $lead = $order->lead;

        $orderTotal = $order->items()->sum('line_total');
        $order->total_value = $orderTotal;
        $order->saveQuietly();

        $leadExpectedValue = $lead->orders()->sum('total_value');
        $lead->expected_value = $leadExpectedValue;
        $lead->saveQuietly();
    }
}
