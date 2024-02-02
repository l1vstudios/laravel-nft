<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

header_remove('X-Powered-By');

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CartItem;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Notification;

class ProductController extends Controller
{

    public function store(Request $request)
    {
        Log::info('Trying to store multiple data.');
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'source' => 'required|string',
        ]);
        Log::info('Data stored successfully.');
        $product = Product::create($validatedData);


        return response()->json(['message' => 'Produk NFT Berhasil Di Publikasi', 'data' => $product], 200);
    }

    public function deleteById($item_id)
    {
        try {
            $product = Product::where('item_id', $item_id)->firstOrFail(); // Update this line
            $product->delete();

            return response()->json(['message' => "Produk NFT dengan ITEM_ID {$item_id} berhasil dihapus"], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Produk NFT tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan internal saat menghapus produk'], 500);
        }
    }

    public function addToCart(Request $request)
    {
        try {
            // Sanctum authentication
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                // User is not authenticated, return 401 Unauthorized response
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,item_id',
                'quantity' => 'required|integer|min:1',
            ]);

            // Fetch product details based on product_id
            $product = Product::where('item_id', $validatedData['product_id'])->first();

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            // Validasi tambahan untuk memastikan 'source' tidak kosong
            $additionalValidations = [
                'source' => 'required', // Menambahkan validasi bahwa 'source' tidak boleh kosong
            ];

            // Menggabungkan data yang telah divalidasi sebelumnya dengan data tambahan
            $validatedData = array_merge($validatedData, $additionalValidations);

            // Fetch product details based on product_id


            // Fetch the user's cart item
            $cartItem = $user->cartItems()->where('product_id', $validatedData['product_id'])->first();

            if ($cartItem) {
                // If the item is already in the cart, update the quantity
                $cartItem->quantity += $validatedData['quantity'];
                $cartItem->save();
            } else {
                // If the item is not in the cart, create a new cart item
                $user->cartItems()->create([
                    'product_id' => $validatedData['product_id'],
                    'quantity' => $validatedData['quantity'],
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'user_active' => $user->name,
                    'source' => $product->source, // tambahkan source dari product info
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Item added to the cart successfully']);
        } catch (\Exception $e) {
            \Log::error('Error adding item to cart: ' . $e->getMessage());

            // Return a more detailed response for debugging in a development environment
            if (config('app.env') === 'local') {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function getCartItems()
    {
        // Sanctum authentication
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            // User is not authenticated, return 401 Unauthorized response
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Retrieve all cart items for the authenticated user
        $cartItems = $user->cartItems;

        return response()->json(['data' => $cartItems], 200);
    }

    public function createPaymentLink(Request $request)
    {
        try {
            // Set your Midtrans API endpoint and key
            $midtransApiUrl = 'https://api.sandbox.midtrans.com/v1/payment-links';
            $midtransApiKey = env('MIDTRANS_API_KEY');

            // Set your request payload
            $requestData = $request->all();

            // Make a request to the Midtrans API using Laravel's HTTP client
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($midtransApiKey . ':'),
            ])->post($midtransApiUrl, $requestData);

            // Parse and return the API response
            $responseData = $response->json();
            return response()->json($responseData, $response->status());
        } catch (\Exception $e) {
            // Handle errors
            \Log::error('Error creating payment link: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating payment link'], 500);
        }
    }


    public function handleSettlementCallback(Request $request)
    {
        try {
            // Validate the request using Midtrans library
            $notification = new Notification();

            // Check if the settlement is valid
            if ($notification->isSucceed()) {
                // Extract relevant information from the callback payload
                $orderId = $notification->order_id;
                $settlementStatus = $notification->settlement_status;
                $settlementTime = $notification->settlement_time;

                // Process the settlement status and update your system accordingly
                // You can refer to the Midtrans documentation for specific status handling.

                // Respond to Midtrans
                return response(['status' => 'Settlement callback received successfully']);
            } else {
                // Handle invalid settlement callback
                return response(['status' => 'Invalid settlement callback'], 400);
            }
        } catch (\Exception $e) {
            // Handle errors
            \Log::error('Error handling settlement callback: ' . $e->getMessage());
            return response()->json(['error' => 'Error handling settlement callback'], 500);
        }
    }

    public function getProductInfo($productId)
    {
        // Lakukan logika untuk mendapatkan informasi produk berdasarkan $productId
        // Misalnya, menggunakan Eloquent atau DB Query Builder

        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product], 200);
    }


    public function deleteCartItem($product_id)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $cartItem = CartItem::where('product_id', $product_id)->where('user_id', $user->id)->firstOrFail();
            $cartItem->delete();

            return response()->json(['message' => "Product with ID {$product_id} deleted from the cart"], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found in the cart'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }



    public function index()
    {
        $products = Product::all();


        return response()->json(['data' => $products], 200);
    }
}
