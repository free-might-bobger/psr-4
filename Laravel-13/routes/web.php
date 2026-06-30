<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ {
    CategoryController,
    CustomerTransactionController,
    DeliveryController,
    DeliveryChargeController,
    ImageController,
    InterConnectedCityController,
    ItemController,
    ItemPriceController,
    ListingApiController,
    MobileActivationCodeController,
    MyStoreController,
    MyStoreTransactionController,
    PaymentMethodController,
    ProfileController,
    PublicItemController,
    PublicStoreController,
    PublicStoreItemController,
    ReceiveMethodController,
    RegisterController,
    RoleController,
    RoleUserController,
    StatusController,
    StoreController,
    StoreMenuAccessController,
    TransactionController,
    UserController,
    UserValidationController,
    StoreUserController
};

/* Route resouce */
Route::resource('public_stores', PublicStoreController::class);
Route::resource('public_items', PublicItemController::class);
Route::resource('categories', CategoryController::class);
Route::resource('interconnected_cities', InterConnectedCityController::class);
Route::resource('receive_methods', ReceiveMethodController::class)->only(['index']);
Route::resource('all_stores', StoreController::class);
Route::resource('delivery_charges', DeliveryChargeController::class)->only(['index']);
Route::resource('public_store_items', PublicStoreItemController::class)->only(['index', 'show']);

/* Route group */
Route::group(['middleware' => 'auth:api', 'myTransactionMiddleware'], function () {
  Route::patch('my-transactions-marked-as-received/{transactionId}', [MyStoreTransactionController::class, 'markedAsReceived']);
});

Route::group(['middleware' => 'auth:api'], function () {
  Route::resource('store-users', StoreUserController::class);
  Route::resource('store-menu-access', StoreMenuAccessController::class);
});

Route::group(['middleware' => 'auth:api', 'itemMiddleware'], function () {
  Route::resource('items', ItemController::class);
  Route::delete('images/{id}', [ImageController::class, 'destroy']);
  Route::post('update-primary-image', [ImageController::class, 'updatePrimaryImage']);
  Route::resource('item-prices', ItemPriceController::class);
  Route::post('item-update', [ItemController::class, 'itemUpdate']);
  Route::post('item-update/{id}', [ItemController::class, 'itemUpdate']);
});

Route::group( [ 'middleware' => 'auth:api' ], function () {
  /* Route resouce */
  Route::resource('deliveries', DeliveryController::class)->middleware('deliveryMiddleware');
  Route::resource('all-transactions', TransactionController::class)->middleware(['superAdminMiddleware', 'allTransactionsMiddleware']);
  Route::resource('my-transactions', CustomerTransactionController::class)->middleware(['customerMiddleware', 'myTransactionsMiddleware']);
  Route::resource('my-store-transactions', MyStoreTransactionController::class)->middleware(['storeAdminMiddleware', 'myStoreMiddleware']);
  Route::resource('my-stores', MyStoreController::class)->middleware(['storeAdminMiddleware', 'myStoreMiddleware']);
  Route::resource('users', UserController::class)->middleware('superAdminMiddleware');
  Route::resource('roles', RoleController::class)->middleware('superAdminMiddleware');
  Route::resource('store-menu-access', StoreMenuAccessController::class);
  Route::resource('statuses', StatusController::class);
  /* Route get */
  Route::get('profile', [ProfileController::class, 'show']);
  Route::delete('role-user', [RoleUserController::class, 'destroyByPair'])->middleware('superAdminMiddleware');
  Route::patch('profile/{id}', [ProfileController::class, 'update'])->middleware('superAdminMiddleware');
  Route::post('role-user', [RoleUserController::class, 'store'])->middleware('superAdminMiddleware');
  Route::post('logout', [RegisterController::class, 'logout']);

 
});

/* Route get */
Route::get('/auth/facebook', [RegisterController::class, 'facebook']);
Route::get('/auth/facebook/callback', [RegisterController::class, 'facebookCallback']);
Route::get('/auth/google', [RegisterController::class, 'google']);
Route::get('/auth/google/callback', [RegisterController::class, 'googleCallback']);
Route::get( 'user/validator/mobile', [ UserValidationController::class, 'mobile' ] );
Route::get( 'user/validator/is-mobile-a-user', [ UserValidationController::class, 'isMobileAUser' ] );
Route::get('listing_api', [ListingApiController::class, 'index']);
Route::get('user/validator/email', 'App\Http\Controllers\Api\User\UserValidationController@email');
Route::get('user/validator/mobile', 'App\Http\Controllers\Api\User\UserValidationController@mobile');
Route::get('payment_methods', [PaymentMethodController::class, 'index' ] );
Route::get('user/is-mobile-exist', [UserValidationController::class, 'isMobileExist']);
Route::get('use-old-activation-code', [MobileActivationCodeController::class, 'useOldActivationcode']);

/* Route post */
Route::post('register/{activation_code}', 'App\Http\Controllers\Api\User\UserController@activationCode');
Route::post('register', [RegisterController::class, 'register']);
Route::post('send-email-invitation', [UserController::class, 'inviteByEmail']);
Route::post('create-new-activation-code', [MobileActivationCodeController::class, 'createNewActivationcode']);
Route::get('verify/activation_code/{activation_code}', [RegisterController::class, 'verifyActivationCode']);
Route::get('login', [RegisterController::class, 'loginPageRedirect']);
Route::post('login', [RegisterController::class, 'login'])->middleware('throttle:5,10');

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});



