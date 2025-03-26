<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\SmallCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('users', [AuthController::class, 'getAllUsers']);
    Route::get('users/{id}', [AuthController::class, 'getUserById']);
    Route::delete('users/{id}', [AuthController::class, 'deleteUser']);
    
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::patch('user/update', [AuthController::class, 'updateUser']);
});

// Slider Routes 
Route::post('slider', [SliderController::class, 'store']);  
Route::get('sliders', [SliderController::class, 'index']); 
Route::get('slider/{id}', [SliderController::class, 'show']);  
Route::patch('/sliders/{id}', [SliderController::class, 'update']);
Route::delete('slider/{id}', [SliderController::class, 'destroy']);  

// Banner Routes
Route::post('banner', [BannerController::class, 'store']);
Route::get('banners', [BannerController::class, 'index']);
Route::get('banner/{id}', [BannerController::class, 'show']);
Route::patch('banner/{id}', [BannerController::class, 'update']);
Route::delete('banner/{id}', [BannerController::class, 'destroy']);
// Category Routes
Route::post('category', [CategoryController::class, 'store']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('category/{id}', [CategoryController::class, 'show']);
Route::patch('category/{id}', [CategoryController::class, 'update']);
Route::delete('category/{id}', [CategoryController::class, 'destroy']);

// Subcategory Routes
Route::post('subcategory', [SubcategoryController::class, 'store']);
Route::get('subcategories', [SubcategoryController::class, 'index']);
Route::get('subcategory/{id}', [SubcategoryController::class, 'show']);
Route::patch('subcategory/{id}', [SubcategoryController::class, 'update']);
Route::delete('subcategory/{id}', [SubcategoryController::class, 'destroy']);
// Small Category Routes
Route::post('small-category', [SmallCategoryController::class, 'store']);
Route::get('small-categories', [SmallCategoryController::class, 'index']);
Route::get('small-category/{id}', [SmallCategoryController::class, 'show']);
Route::patch('small-category/{id}', [SmallCategoryController::class, 'update']);
Route::delete('small-category/{id}', [SmallCategoryController::class, 'destroy']);

// Product Routes
Route::post('product', [ProductController::class, 'store']);  // Create Product
Route::get('products', [ProductController::class, 'index']);  // Get All Products
Route::get('product/{id}', [ProductController::class, 'show']);  // Get Product by ID
Route::patch('product/{id}', [ProductController::class, 'update']);  // Update Product
Route::delete('product/{id}', [ProductController::class, 'destroy']);  // Delete Product

// Order Routes
Route::post('order', [OrderController::class, 'store']);  // Create Order
Route::get('orders', [OrderController::class, 'index']);  // Get All Orders
Route::get('order/{orderId}', [OrderController::class, 'show']);  // Get Order by OrderId
Route::patch('order/{orderId}/status', [OrderController::class, 'updateStatus']); 
Route::delete('order/{orderId}', [OrderController::class, 'destroy']);
Route::get('metrics', [OrderController::class, 'metrics']);

// Contact Routes
Route::post('contact', [ContactController::class, 'store']);  
Route::get('contacts', [ContactController::class, 'index']);  
Route::get('contact/{id}', [ContactController::class, 'show']);  
Route::patch('contact/{id}', [ContactController::class, 'update']);  
Route::delete('contact/{id}', [ContactController::class, 'destroy']);