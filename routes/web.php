<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartAjaxController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminInquiryController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\AdminFinancialController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerShipmentController;
use App\Http\Controllers\SellerApprovalController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\Admin\AdminDriverController;
use App\Http\Controllers\NotificationController;

// 1. Routes accessible to GUESTS (Non-logged-in users)

Route::middleware(['auth', 'track.activity'])->get('/presence/{user}', function (\App\Models\User $user) {
    return response()->json([
        'status' => $user->isOnline() ? 'online' : 'away',
        'away_minutes' => $user->awayMinutes(),
    ]);
})->name('presence.show');

// --- Root Route: Dashboard Landing Page (Requires Login) - NOT for admins ---
Route::middleware(['auth', 'prevent-admin', 'track.activity'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
});

// --- Catalog Route (ACCESSIBLE TO ALL) ---
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/reviews', [ProductController::class, 'storeReview'])
    ->middleware('auth')
    ->name('products.reviews.store');

// --- Communication Routes ---
Route::get('/contact', [InquiryController::class, 'create'])->name('inquiries.create');
Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
Route::delete('/inquiries/conversations/{conversation}', [InquiryController::class, 'destroyConversation'])
    ->middleware('auth')
    ->name('inquiries.conversations.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});


// --- Cart Routes (ACCESSIBLE TO ALL - Guests can add items) ---
Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {

    // Viewing the cart page is accessible to guests.
    Route::get('/', 'show')->name('show');

    // Adding, updating, and removing items is accessible to guests (data stored in session).
    Route::post('/add/{product}', 'add')->name('add');
    Route::put('/update/{product}', 'update')->name('update');
    Route::delete('/remove/{product}', 'remove')->name('remove');
    Route::post('/clear', 'clear')->name('clear');

    // Cart AJAX endpoint
    Route::get('/api/items', [CartAjaxController::class, 'getItems'])->name('api.items');
});

// --- Public Tracking Route (Accessible to EVERYONE - GUESTS can track without login) ---
Route::post('/track-package', [TrackingController::class, 'publicTrack'])->name('tracking.public');
Route::get('/track-package', function () {
    return view('tracking.search');
})->name('tracking.search');
Route::get('/tracking/{order}', [TrackingController::class, 'show'])->name('tracking.show');
Route::get('/tracking/{order}/data', [TrackingController::class, 'getTrackingData'])->name('tracking.getTrackingData');


// --- Authentication Routes (Login/Register) ---
Route::middleware('guest')->group(function () {
    // User Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Admin Login (Separate)
    Route::get('admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');

    Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    Route::get('auth/facebook', [SocialAuthController::class, 'redirectToFacebook']);
    Route::get('auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

    // Registration
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});


// 2. Routes ONLY accessible to AUTHENTICATED USERS (Must log in) - NOT for admins

Route::middleware(['auth', 'prevent-admin', 'track.activity'])->group(function () {

    // --- Dashboard Route (USER LANDING PAGE) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/driver', [DriverController::class, 'dashboard'])->name('driver.dashboard');
    Route::post('/driver/application', [DriverController::class, 'submit'])->name('driver.application.submit');
    Route::get('/driver/analytics', [DriverController::class, 'analytics'])->name('driver.analytics');
    Route::get('/driver/schedule', [DriverController::class, 'schedule'])->name('driver.schedule');
    Route::get('/driver/history', [DriverController::class, 'history'])->name('driver.history');
    Route::get('/driver/messages', [DriverController::class, 'messages'])->name('driver.messages');
    Route::get('/driver/profile', [DriverController::class, 'profile'])->name('driver.profile');
    Route::patch('/driver/profile', [DriverController::class, 'updateProfile'])->name('driver.profile.update');
    Route::patch('/driver/availability', [DriverController::class, 'availability'])->name('driver.availability');
    Route::post('/driver/shipments/{shipment}/claim', [DriverController::class, 'claim'])->name('driver.shipments.claim');
    Route::patch('/driver/shipments/{shipment}', [DriverController::class, 'updateDelivery'])->name('driver.shipments.update');
    Route::post('/driver/shipments/{shipment}/location', [DriverController::class, 'updateLocation'])->name('driver.shipments.location');

    // --- Checkout Routes (REQUIRES AUTHENTICATION) ---
    // User must be logged in to access the checkout and place an order.
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/direct-buy/{product}', [CheckoutController::class, 'directBuy'])->name('checkout.direct-buy');

    // --- User Profile Routes ---
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    Route::get('/profile/vouchers', [ProfileController::class, 'vouchers'])->name('profile.vouchers');
    Route::get('/profile/banks', [ProfileController::class, 'banks'])->name('profile.banks');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change_password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // --- Payment Method Routes ---
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
    Route::post('/payment-methods/{paymentMethod}/set-default', [PaymentMethodController::class, 'setDefault'])->name('payment-methods.setDefault');

    // --- Order Routes (User order management) ---
    // AJAX: return order details for modal (shipping, items, tracking, map)
    Route::get('/orders/{order}/details', [OrderController::class, 'ajaxDetails'])->name('order.details');

    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    // Buy again: add items from a previous order back into the cart
    Route::post('/orders/{order}/buy-again', [OrderController::class, 'buyAgain'])->name('order.buy_again');
    // Request return for delivered orders
    Route::post('/orders/{order}/request-return', [OrderController::class, 'requestReturn'])->name('order.request_return');

});

// Shared logout for any authenticated user (including admins)
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


// ==========================================================
// 3. --- ADMIN ROUTES (Protected by the 'access-admin' Gate) ---
// ==========================================================
Route::middleware(['auth', 'can:access-admin', 'track.activity'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard landing page
    Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'profile'])->name('profile');
    Route::post('profile', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/change-password', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'updatePassword'])->name('profile.change_password');

    // Admin notification actions
    Route::post('notifications/read-all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::patch('notifications/{notification}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead'])->name('notifications.read');

    // Product Management (Resource routes for CRUD)
    // Routes: admin/products, admin/products/create, admin/products/{id}/edit, etc.
    Route::resource('products', AdminProductController::class);

    // Order Management (Index and Update/Show routes)
    // Routes: admin/orders, admin/orders/{id}, etc.
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{id}', [AdminOrderController::class, 'update'])->name('orders.update');

    // Inquiry Management
    // Routes: admin/inquiries, admin/inquiries/{id}, etc.
    Route::get('inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
    Route::get('inquiries/{id}', [AdminInquiryController::class, 'show'])->name('inquiries.show');
    Route::patch('inquiries/{id}', [AdminInquiryController::class, 'update'])->name('inquiries.update');
    Route::delete('inquiries/{id}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');

    Route::get('messages', function () {
        return view('admin.messages');
    })->name('messages');

    // Stock Management
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [AdminStockController::class, 'index'])->name('index');
        Route::post('/{product}/update', [AdminStockController::class, 'update'])->name('update');
    });

    // Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [AdminCustomerController::class, 'index'])->name('index');
        Route::get('/export', [AdminCustomerController::class, 'export'])->name('export');
        Route::get('/{user}/edit', [AdminCustomerController::class, 'edit'])->name('edit');
        Route::get('/{user}', [AdminCustomerController::class, 'show'])->name('show');
        Route::patch('/{user}', [AdminCustomerController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminCustomerController::class, 'destroy'])->name('destroy');
    });

    // Seller Management
    Route::prefix('sellers')->name('sellers.')->group(function () {
        Route::get('/', [AdminSellerController::class, 'index'])->name('index');
        Route::get('/export', [AdminSellerController::class, 'export'])->name('export');
        Route::get('/history', [AdminSellerController::class, 'history'])->name('history');
        Route::get('/{seller}/edit', [AdminSellerController::class, 'edit'])->name('edit');
        Route::get('/{seller}', [AdminSellerController::class, 'show'])->name('show');
        Route::patch('/{seller}', [AdminSellerController::class, 'update'])->name('update');
        Route::patch('/{seller}/approve', [AdminSellerController::class, 'approve'])->name('approve');
        Route::patch('/{seller}/reject', [AdminSellerController::class, 'reject'])->name('reject');
        Route::patch('/{seller}/restore', [AdminSellerController::class, 'restore'])->name('restore');
        Route::delete('/{seller}', [AdminSellerController::class, 'destroy'])->name('destroy');
    });

    // Driver Hiring
    Route::get('drivers', [AdminDriverController::class, 'index'])->name('drivers.index');
    Route::patch('drivers/{application}', [AdminDriverController::class, 'update'])->name('drivers.update');

    // Arindo Verification (admin)
    Route::prefix('arindo')->name('arindo.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminArindoController::class, 'index'])->name('index');
        Route::get('{product}', [\App\Http\Controllers\Admin\AdminArindoController::class, 'show'])->name('show');
        Route::patch('{product}/verify', [\App\Http\Controllers\Admin\AdminArindoController::class, 'verify'])->name('verify');
        Route::patch('{product}/reject', [\App\Http\Controllers\Admin\AdminArindoController::class, 'reject'])->name('reject');
    });

    // Financial & Revenue
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [AdminFinancialController::class, 'index'])->name('index');
        Route::get('/revenue', [AdminFinancialController::class, 'revenue'])->name('revenue');
        Route::get('/commissions', [AdminFinancialController::class, 'commissions'])->name('commissions');
    });

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
        Route::post('/update', [AdminSettingsController::class, 'update'])->name('update');
    });
});

// ==========================================================
// 4. --- SELLER ROUTES ---
// ==========================================================
Route::middleware(['auth', 'seller.approved'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('start', [SellerDashboardController::class, 'start'])->name('start');
    Route::get('onboarding', [SellerDashboardController::class, 'onboarding'])->name('onboarding');
    Route::post('onboarding', [SellerDashboardController::class, 'processOnboarding'])->name('onboarding.process');
    Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');

    Route::get('income', [SellerDashboardController::class, 'myIncome'])->name('income');
    Route::post('withdraw', [SellerDashboardController::class, 'withdraw'])->name('withdraw');

    Route::get('products', [SellerDashboardController::class, 'products'])->name('products');
    Route::get('products/add', [SellerDashboardController::class, 'addProduct'])->name('products.add');
    Route::post('products', [SellerDashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('products/{id}/edit', [SellerDashboardController::class, 'editProduct'])->name('products.edit');
    Route::patch('products/{id}', [SellerDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('products/{id}', [SellerDashboardController::class, 'deleteProduct'])->name('products.delete');

    Route::get('arindo', [SellerDashboardController::class, 'arindoProperties'])->name('arindo.properties');
    Route::get('arindo/create', [SellerDashboardController::class, 'addArindoProduct'])->name('arindo.properties.create');

    Route::get('orders', [SellerDashboardController::class, 'orders'])->name('orders');
    Route::get('orders/{id}', [SellerDashboardController::class, 'orderDetail'])->name('orders.detail');
    Route::patch('orders/{id}/status', [SellerDashboardController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('orders/{order}/ship', [SellerShipmentController::class, 'store'])->name('orders.ship');
    Route::get('orders/{order}/track', [SellerShipmentController::class, 'show'])->name('orders.track');

    Route::get('shipments', [SellerShipmentController::class, 'index'])->name('shipments');

    Route::get('approval', [SellerApprovalController::class, 'show'])->name('approval.show');
    Route::post('approval', [SellerApprovalController::class, 'store'])->name('approval.store');

    Route::get('bank-accounts', function () {
        $paymentMethods = Auth::user()->paymentMethods()->latest()->get();
        return view('seller.bank_accounts', compact('paymentMethods'));
    })->name('bank_accounts');

    Route::get('shop', function () {
        return view('seller.shop');
    })->name('shop');

    Route::get('setting', function () {
        return view('seller.setting');
    })->name('setting');

    Route::get('marketing', function () {
        return view('seller.marketing');
    })->name('marketing');

    Route::get('messages', function () {
        return view('seller.messages');
    })->name('messages');
});
