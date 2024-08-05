<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\TrackOrderRequest; // Import the Form Request
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function ordertrack(Request $request)
{
    $userId = auth()->id();

    $order = Order::where('user_id', $userId)
                  ->with('items', 'transaction', 'billinginfo')
                  ->first();

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    return response()->json([
        'order' => $order,
        'message' => 'Order tracked successfully'
    ]);
}

}
