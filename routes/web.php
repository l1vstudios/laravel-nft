<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegisterController;

use App\Http\Controllers\ApiAuthController;

Route::post('/midtrans_settlement_callback', [ProductController::class, 'handleSettlementCallback']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/data', [ApiController::class, 'getData']);
Route::post('/data', [ApiController::class, 'storeData']);
Route::post('/register', [RegisterController::class, 'customRegister'])->middleware('disablecors');
Route::delete('/products/delete/{item_id}', [ProductController::class, 'deleteById']);
Route::delete('/cart-items/{product_id}', [ProductController::class, 'deleteCartItem']);
Route::get('/user', [ApiAuthController::class, 'getUserByToken'])->middleware('disablecors');
Route::post('/login', [ApiAuthController::class, 'login'])->middleware('disablecors');
Route::post('/add-to-cart', [ProductController::class, 'addToCart']);
Route::post('/payment', [ProductController::class, 'createPaymentLink']);
Route::get('/cart-items', [ProductController::class, 'getCartItems']);
Route::get('/get-product-info/{productId}', [ProductController::class, 'getProductInfo']);
Route::post('/logout', [ApiAuthController::class, 'logoutByToken'])->middleware('disablecors');
// Route::middleware('auth:api')->post('/add-to-cart', [ProductController::class, 'addToCart']);





// default route
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// new route
// Route::post('masuk', [MasukController::class, 'masuk'])->middleware('disablecors');
// Route::post('/masuk', [MasukController::class, 'masuk'])->middleware('disablecors');
// Route::delete('/products/delete/{id}', 'ProductController@deleteById');



    // Route::post('/register', 'RegisterController@customRegister');
