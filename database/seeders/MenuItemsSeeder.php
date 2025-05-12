<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemsSeeder extends Seeder
{
    public function run(): void
    {
        $menuItems = [
            ['name' => 'Burger', 'category' => 'Food', 'description' => 'Juicy beef burger with cheese', 'price' => 8.99, 'archived' => false],
            ['name' => 'Pizza', 'category' => 'Food', 'description' => 'Delicious pizza with mozzarella', 'price' => 12.99, 'archived' => false],
            ['name' => 'Pasta', 'category' => 'Food', 'description' => 'Pasta with creamy alfredo sauce', 'price' => 10.99, 'archived' => false],
            ['name' => 'Salad', 'category' => 'Food', 'description' => 'Fresh garden salad', 'price' => 6.99, 'archived' => false],
            ['name' => 'Coke', 'category' => 'Drink', 'description' => 'Refreshing Coca Cola', 'price' => 1.99, 'archived' => false],
            ['name' => 'Water', 'category' => 'Drink', 'description' => 'Bottled water', 'price' => 0.99, 'archived' => false],
            ['name' => 'Coffee', 'category' => 'Drink', 'description' => 'Fresh brewed coffee', 'price' => 2.99, 'archived' => false],
        ];

        foreach ($menuItems as $item) {
            DB::table('menu_items')->insert([
                'name' => $item['name'],
                'category' => $item['category'],
                'description' => $item['description'],
                'price' => $item['price'],
                'archived' => $item['archived'],
                'image' => 'https://via.placeholder.com/150', // You can replace with actual image URLs
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
