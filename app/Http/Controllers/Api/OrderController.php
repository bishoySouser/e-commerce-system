<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request->all());
            
            return $this->sendResponse(
                $order,
                'Order is being processed.'
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                [],
                442
            );
        }
        
       
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Try to find the order and load its products
            $order = Order::with('products')->findOrFail($id);

            // Return the resource if the order is found
            $order = new OrderResource($order);
            return $this->sendResponse(
                $order,
                'order retrieved successfully.'
            );
        } catch (\Exception $e) {
            // Handle the case where the order is not found
            return response()->json([
                'error' => 'Order not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }



}
