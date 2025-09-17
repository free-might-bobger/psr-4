<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ {
    PublicStoreController,
    UserController,
    ImageController,
    ProfileController,
    MenuController,
    UserValidationController,
    RegisterController,
    ListingApiController,
    RoleController,
    PaymentOptionController,
    PaymentController,
    CategoryController,
    ItemController,
    InterConnectedCityController,
    ReceiveMethodController,
    StoreController,
    DeliveryChargeController,
    MobileActivationCodeController,
    PaymentMethodController,
    TransactionController,
    CustomerTransactionController,
    MyStoreController,
    ItemPriceController,
    StoreMenuAccessController
}
;

Route::get( 'user/validator/mobile', [ UserValidationController::class, 'mobile' ] );
Route::get( 'user/validator/is-mobile-a-user', [ UserValidationController::class, 'isMobileAUser' ] );

Route::post( 'login', [ RegisterController::class, 'login' ] );

Route::group( [ 'middleware' => 'auth:api' ], function () {
    /**
    * Updated 10-19-2024
    */
    //todo Limit transaction to it's role as an admin
    Route::resource('transactions', TransactionController::class);
    Route::resource('customer-transactions', CustomerTransactionController::class);

    Route::resource('my-stores', MyStoreController::class)->middleware('myStoreMiddleware');
    /**
     * Updated 10-19-2024
     */
    Route::get('profile', [ProfileController::class, 'show']);
    Route::patch('profile/ {
    id}
    ', [ProfileController::class, 'update']);
    Route::post('logout', [RegisterController::class, 'logout']);
    Route::post('item-update/ {
        id}
        ', [ItemController::class, 'itemUpdate'])->middleware('itemMiddleware');
    Route::resource('items', ItemController::class)->middleware('itemMiddleware');
    Route::resource('item-prices', ItemPriceController::class);
    Route::resource('store-menu-access', StoreMenuAccessController::class);
});

 Route::get('listing_api', [ListingApiController::class, 'index']);

 /**
  * UPDATED 08-20-2024
  */

Route::get('user/validator/email', 'App\Http\Controllers\Api\User\UserValidationController@email');
Route::get('user/validator/mobile', 'App\Http\Controllers\Api\User\UserValidationController@mobile');
Route::post('register/ {
            activation_code}
            ', 'App\Http\Controllers\Api\User\UserController@activationCode');
Route::post('register', [RegisterController::class, 'register']);
Route::post('send-email-invitation', [UserController::class, 'inviteByEmail']);

  Route::get('user/is-mobile-exist', [UserValidationController::class, 'isMobileExist']);
  Route::resource('public_stores', PublicStoreController::class);
  Route::resource('categories', CategoryController::class);
  Route::resource('interconnected_cities', InterConnectedCityController::class);
  Route::get('google-api-text-search', [GoogleApiController::class, 'textSearch']);
  Route::get('google-api-nearby-search', [GoogleApiController::class, 'nearBySearch']);
  Route::resource('receive_methods', ReceiveMethodController::class)->only(['index']);
  Route::resource('stores', StoreController::class);
  Route::resource('delivery_charges', DeliveryChargeController::class)->only(['index']);
  Route::post('create-new-activation-code', [MobileActivationCodeController::class, 'createNewActivationcode']);
  Route::get('use-old-activation-code', [MobileActivationCodeController::class, 'useOldActivationcode']);
  Route::post('verify-passcode', [MobileActivationCodeController::class, 'verifyPasscode']);
  Route::post('login', [RegisterController::class, 'login']);
  Route::get('payment_methods', [PaymentMethodController::class, 'index' ] );

            /**
            * END UPDATED 08-20-2024
            */

