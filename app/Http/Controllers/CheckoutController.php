<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        try {
            $validatedData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'companyname' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:255',
                'address1' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255', // Should be 'CO'
                'payment-methods' => 'required|string|in:payment-option-1,payment-option-2', // Corresponds to radio button IDs
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        }

        // Map payment method IDs to more descriptive strings
        $paymentMethod = match ($validatedData['payment-methods']) {
            'payment-option-1' => 'transferencia_bancaria',
            'payment-option-2' => 'contra_entrega',
            default => 'unknown',
        };

        // Retrieve cart items from session
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }
        $shippingCost = 0.00; // Shipping is free
        $totalAmount = $subtotal + $shippingCost;

        DB::beginTransaction();
        try {
            // 3. Create a new Order record
            $order = Order::create([
                'customer_name' => $validatedData['firstname'],
                'customer_lastname' => $validatedData['lastname'],
                'customer_email' => $validatedData['email'],
                'customer_phone' => $validatedData['phone'],
                'customer_address' => $validatedData['address1'],
                'customer_city' => $validatedData['city'],
                'customer_state' => $validatedData['state'],
                'customer_country' => $validatedData['country'],
                'company_name' => $validatedData['companyname'] ?? null,
                'payment_method' => $paymentMethod,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // 4. Create OrderItem records for each product in the cart
            foreach ($cart as $productId => $item) {
                $order->items()->create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // 5. Clear the cart session
            session()->forget('cart');

            DB::commit();

            // 6. Return a JSON response with order details for WhatsApp
            $whatsappPhoneNumber = Setting::where('key', 'whatsapp_phone_number')->first()?->value;

            return response()->json([
                'success' => true,
                'message' => 'Tu pedido ha sido realizado con éxito!',
                'orderId' => $order->id,
                'order' => $order->load('items'), // Load items relationship
                'whatsappNumber' => $whatsappPhoneNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Error al crear el pedido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al procesar tu pedido. Por favor, inténtalo de nuevo.'
            ], 500);
        }
    }
}
