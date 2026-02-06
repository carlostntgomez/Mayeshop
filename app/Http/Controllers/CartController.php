<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Import the Product model

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cart = session()->get('cart', []);

                    if (isset($cart[$productId])) {
                        return response()->json(['message' => 'Este producto ya está en tu carrito.'], 409); // 409 Conflict
                    } else {
                        $product = Product::find($productId);
                        if (!$product) {
                            return response()->json(['message' => 'Producto no encontrado'], 404);
                        }
                        $cart[$productId] = [
                            "product_id" => $productId,
                            "name" => $product->name,
                            "price" => $product->sale_price ?? $product->price,
                            "quantity" => 1, // Always add 1 unit
                            "image" => $product->main_image
                        ];
                    }
        session()->put('cart', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'message' => 'Producto añadido al carrito exitosamente!',
            'cartCount' => $cartCount
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->input('product_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            $cartCount = array_sum(array_column($cart, 'quantity'));
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $total = $subtotal; // Assuming no taxes or shipping for now

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito.',
                'cartCount' => $cartCount,
                'subtotal' => $subtotal,
                'total' => $total
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado en el carrito.'
        ], 404);
    }
}
