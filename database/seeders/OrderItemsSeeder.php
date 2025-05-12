<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;

class OrderItemsSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $menuItems = MenuItem::all();

        if ($menuItems->count() === 0 || $orders->count() === 0) {
            $this->command->warn("No menu items or orders found. Run MenuItemsSeeder and OrdersSeeder first.");
            return;
        }

        foreach ($orders as $order) {
            // Add 1 to 3 items per order
            $itemsCount = rand(1, 3);
            for ($i = 0; $i < $itemsCount; $i++) {
                $menuItem = $menuItems->random();
                $quantity = rand(1, 5);
                $price = $menuItem->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'price' => number_format($price, 2, '.', ''),
                    'created_at' => $order->created_at, // match order time
                    'updated_at' => $order->created_at,
                ]);
            }
        }
    }
}

