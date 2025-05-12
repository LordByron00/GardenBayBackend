<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use Illuminate\Support\Carbon;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [];

        // 🔵 Today's orders
        for ($i = 0; $i < 10; $i++) {
            $orders[] = [
                'total_price' => rand(50, 500),
                'created_at' => Carbon::today()->addMinutes(rand(0, 1439)),
                'updated_at' => now(),
            ];
        }

        // 🟢 This week's orders (excluding today)
        for ($i = 0; $i < 15; $i++) {
            $orders[] = [
                'total_price' => rand(50, 500),
                'created_at' => now()->startOfWeek()->addDays(rand(1, 5))->addHours(rand(0, 23)),
                'updated_at' => now(),
            ];
        }

        // 🟠 Last few days (within the past 3–6 days)
        for ($i = 0; $i < 20; $i++) {
            $orders[] = [
                'total_price' => rand(50, 500),
                'created_at' => now()->subDays(rand(3, 6))->addHours(rand(0, 23)),
                'updated_at' => now(),
            ];
        }

        // 🔴 Last few weeks (within the last 1–4 weeks)
        for ($i = 0; $i < 20; $i++) {
            $orders[] = [
                'total_price' => rand(50, 500),
                'created_at' => now()->subWeeks(rand(1, 4))->addDays(rand(0, 6)),
                'updated_at' => now(),
            ];
        }

        // 🟣 Older orders (months or years ago)
        for ($i = 0; $i < 30; $i++) {
            $orders[] = [
                'total_price' => rand(50, 500),
                'created_at' => now()
                    ->subYears(rand(0, 2))
                    ->subMonths(rand(0, 11))
                    ->subDays(rand(0, 30))
                    ->subHours(rand(0, 23)),
                'updated_at' => now(),
            ];
        }

        Order::insert($orders);
    }
}
