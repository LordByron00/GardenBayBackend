<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * GET /api/analytics?period=day|week|month|year|all
     */
    public function analytics(Request $request)
    {
        $period = $request->query('period', 'all');
        $now    = Carbon::now();

        // 1. Determine date ranges, grouping and label formats
        switch ($period) {
            case 'day':
                $startCurrent = $now->copy()->startOfDay();
                $startPrev    = $startCurrent->copy()->subDay();
                $groupBy      = "strftime('%H', oi.created_at)";
                $labelExpr    = "strftime('%H:00', oi.created_at)";
                break;

            case 'week':
                $startCurrent = $now->copy()->startOfWeek();
                $startPrev    = $startCurrent->copy()->subWeek();
                $groupBy      = "strftime('%Y-%m-%d', oi.created_at)";
                $labelExpr    = "strftime('%a', oi.created_at)";
                break;

            case 'month':
                $startCurrent = $now->copy()->startOfMonth();
                $startPrev    = $startCurrent->copy()->subMonth();
                $groupBy      = "strftime('%Y-%m-%d', oi.created_at)";
                $labelExpr    = "strftime('%d', oi.created_at)";
                break;

            case 'year':
                $startCurrent = $now->copy()->startOfYear();
                $startPrev    = $startCurrent->copy()->subYear();
                $groupBy      = "strftime('%Y-%m', oi.created_at)";
                $labelExpr    = "strftime('%b', oi.created_at)";
                break;

            case 'all':
            default:
                $startCurrent = null;
                $startPrev    = null;
                $groupBy      = "strftime('%Y-%m', oi.created_at)";
                $labelExpr    = "strftime('%Y-%m', oi.created_at)";
                break;
        }

        // Helper to fetch KPIs for any given range:
            // Helper to fetch KPIs for any given range:
        $fetchKPIs = function (?Carbon $start, Carbon $end) {
            $q = DB::table('order_items as oi');
            
            if ($start) {
                $q->whereBetween('oi.created_at', [$start, $end]);
            }
            
            // Total sales revenue
            $revenue = (float) $q->selectRaw('SUM(price * quantity)')->value('SUM(price * quantity)') ?? 0;
            
            // Total number of items sold
            $items = (int) $q->sum('quantity');
            
            // Average sale per order (order = group by order_id)
            $orderSales = DB::table('order_items as oi')
                ->selectRaw('order_id, SUM(price * quantity) as order_total')
                ->when($start, fn($q) => $q->whereBetween('created_at', [$start, $end]))
                ->groupBy('order_id')
                ->pluck('order_total');
            $avg = $orderSales->count() > 0 ? $orderSales->avg() : 0;
            
            // Calculate net income as 15% of total revenue
            $net = $revenue * 0.15; // Assuming 15% net income margin

            return [
                'revenue' => $revenue,
                'items' => $items,
                'avg' => $avg,
                'net' => $net,
            ];
        };

        // 2. Fetch current & previous KPIs
        $currentKPIs = $fetchKPIs($startCurrent, $now);
        $prevEnd     = $startCurrent ? $startCurrent->copy()->subSecond() : null;
        $previousKPIs= $fetchKPIs($startPrev, $prevEnd ?? $now);

        // 3. Compute deltas and percentages
        $delta = [
            'revenue' => $currentKPIs['revenue'] - $previousKPIs['revenue'],
            'items'   => $currentKPIs['items']   - $previousKPIs['items'],
            'avg'     => $currentKPIs['avg']     - $previousKPIs['avg'],
            'net'     => $currentKPIs['net']     - $previousKPIs['net'],
        ];
        
        $pct = [
            'revenue' => $previousKPIs['revenue'] > 0 ? ($delta['revenue'] / $previousKPIs['revenue']) * 100 : null,
            'items'   => $previousKPIs['items'] > 0   ? ($delta['items'] / $previousKPIs['items']) * 100     : null,
            'avg'     => $previousKPIs['avg'] > 0     ? ($delta['avg'] / $previousKPIs['avg']) * 100         : null,
            'net'     => $previousKPIs['net'] > 0     ? ($delta['net'] / $previousKPIs['net']) * 100         : null,
        ];

        // Bar chart: top products with image and price (for Bar Chart)
        $topProductsDetailed = DB::table('order_items as oi')
        ->when($startCurrent, fn($q) => $q->whereBetween('oi.created_at', [$startCurrent, $now]))
        ->join('menu_items as m', 'oi.menu_item_id', '=', 'm.id')
        ->select('m.id', 'm.name', 'm.image', 'm.price', DB::raw('SUM(oi.quantity) as total_sold'))
        ->groupBy('m.id', 'm.name', 'm.image', 'm.price')
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();


        // Line chart: revenue or sales over time
        $salesOverTime = DB::table('order_items as oi')
        ->when($startCurrent, fn($q) => $q->whereBetween('oi.created_at', [$startCurrent, $now]))
        ->selectRaw("$groupBy as grp, SUM(price * quantity) as revenue, SUM(quantity) as quantity, $labelExpr as label")
        ->groupBy('grp')
        ->orderBy('grp')
        ->get()
        ->map(fn($r) => [
            'label'    => $r->label,
            'revenue'  => (float) $r->revenue,
            'quantity' => (int) $r->quantity,
        ]);
    
        // 5. Revenue trend labels & values (current period)
        $trend = DB::table('order_items as oi')
            ->when($startCurrent, fn($q) => $q->whereBetween('oi.created_at', [$startCurrent, $now]))
            ->selectRaw("$groupBy as grp, SUM(price * quantity) as total, $labelExpr as label")
            ->groupBy('grp')
            ->orderBy('grp')
            ->get()
            ->map(fn($r) => ['label' => $r->label, 'value' => (float) $r->total]);



        // 6. Return everything
        return response()->json([
            'period'            => $period,
            'current' => [
                'overall_sales' => round($currentKPIs['revenue'], 2),
                'total_items'   => $currentKPIs['items'],
                'average_sale'  => round($currentKPIs['avg'], 2),
                'net_income'    => round($currentKPIs['net'], 2),
            ],
            'previous' => [
                'overall_sales' => round($previousKPIs['revenue'], 2),
                'total_items'   => $previousKPIs['items'],
                'average_sale'  => round($previousKPIs['avg'], 2),
                'net_income'    => round($previousKPIs['net'], 2),
            ],
            'change' => [
                'absolute'   => array_map(fn($v) => round($v, 2), $delta),
                'percentage' => array_map(fn($v) => is_null($v) ? null : round($v, 2), $pct),
            ],
            'top_products_detailed' => $topProductsDetailed, // for bar chart with images
            'sales_over_time'  => $salesOverTime,           // full line chart data
        ]);
    }
}
