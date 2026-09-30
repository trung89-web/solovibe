<?php

use App\Models\Order;

it('uses a simplified no-shipping order flow', function () {
    $order = new Order();
    $order->order_status = 'pending';

    expect($order->getAllowedNextStatuses())
        ->toContain('processing')
        ->not->toContain('shipping');

    $order->order_status = 'processing';

    expect($order->getAllowedNextStatuses())
        ->toContain('completed')
        ->not->toContain('shipping');
});
