<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    // Create a new order with image upload
    public function store(Request $request)
    {
        $request->validate([
            'orderId' => 'required|unique:orders',
            'userDetails' => 'required|array',
            'cart' => 'required|array',
            'paymentMethod' => 'required|string',
            'selectedBank' => 'nullable|string',
            'subtotal' => 'required|numeric',
            'deliveryCharges' => 'required|numeric',
            'grandTotal' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate the image
        ]);

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('orders', 'public'); // Store image in the 'orders' directory
        }

        // Create the order with the image path
        $order = Order::create([
            'orderId' => $request->orderId,
            'userDetails' => $request->userDetails,
            'cart' => $request->cart,
            'paymentMethod' => $request->paymentMethod,
            'selectedBank' => $request->selectedBank,
            'subtotal' => $request->subtotal,
            'deliveryCharges' => $request->deliveryCharges,
            'grandTotal' => $request->grandTotal,
            'status' => 'pending', // Default status
            'image' => $imagePath, // Save the image path
        ]);

        return response()->json($order, 201);
    }

    // Get all orders
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    // Get a specific order by ID
    public function show($orderId)
    {
        $order = Order::where('orderId', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    // Update the status of an order
    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::where('orderId', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,completed,failed',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json($order);
    }

    // Delete an order by orderId
    public function destroy($orderId)
    {
        $order = Order::where('orderId', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);  // Return 404 if order not found
        }

        // If the order has an image, delete it from storage
        if ($order->image) {
            Storage::disk('public')->delete($order->image);
        }

        // Delete the order from the database
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
