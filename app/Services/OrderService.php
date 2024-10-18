<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder($data)
    {
        DB::beginTransaction();

        try {
                $total = 0;
                $orderData = [];

                foreach ($data['products'] as $item) {
                    $product = Product::findOrFail($item['id']);
                    
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                    
                    $product->stock_quantity -= $item['quantity'];
                    $product->save();
                    
                    $total += $product->price * $item['quantity'];
                    $orderData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price_at_time' => $product->price
                    ];
                }

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'total_amount' => $total,
                    'status' => 'pending'
                ]);

                $order->products()->attach($orderData);

                event(new OrderPlaced($order));

                return $order;
            } catch (\Exception $e) {
                // Rollback transaction if something goes wrong
                DB::rollBack();
                throw $e;
            }
    }
}
