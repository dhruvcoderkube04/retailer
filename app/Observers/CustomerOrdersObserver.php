<?php

namespace App\Observers;

use App\Models\CustomerOrders;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

        try {
            $extenal_url = env('SHOPIFY_URL');
            $response = Http::post($extenal_url.'/webhook/external/order/update', $responseData);

            Log::info('Webhook:-Response from external endpoint', [

                'status' => $response->status(),

                'body'   => $response->body()

            ]);
        } catch (\Exception $e) {

            Log::error('Webhook:-Failed to send order update', [

                'error' => $e->getMessage()

            ]);
        }
    }
}
