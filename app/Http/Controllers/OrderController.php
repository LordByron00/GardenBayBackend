<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function Store(Request $request)
    {
        $quantity = $request->input('quantity');
        $price = $request->input('price');

        $totalPrice = $quantity * $price;

        $order = Order::create(
            ['total_price' => $totalPrice],
        );

        $order->orderItems()->create($request->all());
        return response()->json(['message' => 'Order placed', 'order_id' => $order->id]);
        
        // foreach ($request->input('items') as $item) {
        // $order->orderItems()->create($item);
        // }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

  

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
