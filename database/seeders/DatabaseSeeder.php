<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Useful for hashing passwords
use Nette\Utils\Random;

use Database\Seeders\MenuItemsSeeder;
use Database\Seeders\OrderItemsSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // for ($i = 1; $i <= 5; $i++) {
        //     MenuItem::create([
        //         'name' => "Menu {$i}",
        //         'image' => "Menu {$i}.jpg",
        //         'category' => 'main',
        //         'description' => "Description {$i}",
        //         'price' => 25,
        //         'archived' => false,
        //     ]);
        // }
        
        $this->call([
            OrdersSeeder::class,
            MenuItemsSeeder::class,
            OrderItemsSeeder::class,
        ]);
        
    }
}
