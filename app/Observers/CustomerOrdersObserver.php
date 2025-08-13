<?php

namespace App\Observers;

use App\Models\CustomerOrders;
use Illuminate\Support\Facades\Log;

class CustomerOrdersObserver
{
    /**
     * Handle the CustomerOrders "updated" event.
     */
    public function updated(CustomerOrders $customerOrders): void
    {
        Log::info('Customer order updated', [
            'id' => $customerOrder->id,
            'changed' => $customerOrder->getChanges(),
            'old' => $customerOrder->getOriginal()
        ]);

        $this->handleOrderUpdate($customerOrder);
    }

    private function handleOrderUpdate($customerOrder)
    {
        $customerOrderDetail = CustomerOrders::find($customerOrder->id);

        $response = [
            'order_id' => $customerOrderDetail->order_id,
            'tracking_number' => $customerOrderDetail->tracking_number,
            'status' => $customerOrderDetail->status,
        ];

        Log::info('Order update response', $response);

        return response()->json($response);
    }
}
