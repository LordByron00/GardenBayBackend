<?php

namespace App\Http\Controllers;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KDSController extends Controller
{
    public function getTodayPendingOrders()
    {
        $todayOrders = Order::whereDate('created_at', Carbon::today())
            ->with(['orderItems.menuItem']) // Eager load relationships if needed
            ->get();
        return response()->json($todayOrders);
    }

    public function markAsPreparing($id)
    {
        $item = Order::findOrFail($id);
        $item->status = 'preparing';
        $item->save();

        return response()->json(['message' => 'Item marked as preparing.']);
    }

      public function markAsPriority($id)
    {
        $item = Order::findOrFail($id);
        $item->status = 'priority';
        $item->save();

        return response()->json(['message' => 'Item marked as priority.']);
    }

    public function markAsCompleted($id)
    {
        $item = Order::findOrFail($id);
        $item->status = 'completed';
        $item->save();

        return response()->json(['message' => 'Item completed.']);
    }
}
