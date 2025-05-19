<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        \Log::info('Order store endpoint hit.');
        \Log::info('Request data:', $request->all());
    
        $validatedData = $request->validate([
            'order_items' => 'required|array|min:1',
            'order_items.*.id' => 'required|exists:menu_items,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = collect($validatedData['order_items'])->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            // Create order
            $order = Order::create([
                'total_price' => $totalPrice,
                'status' => 'new',
            ]);

            // Transform order items to match DB schema
            $orderItems = collect($validatedData['order_items'])->map(function ($item) {
                return [
                    'menu_item_id' => $item['id'], // Rename key
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
            })->toArray();

            // Create order items
            $order->orderItems()->createMany($orderItems);

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

  
    public function index()
    {
        $orderItems = OrderItem::all();
        return response()->json($orderItems);

    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {

        // return response()->json($orderItems);

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
