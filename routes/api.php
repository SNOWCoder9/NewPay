<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Routing\Registrar as RouteContract;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\\V1', 'prefix' => 'v1'], function (RouteContract $api) {
    $api->match(['get', 'post'], 'site/buy_shop/{payment?}', 'NotifyController@contractPayment')->name('contract');
    $api->match(['get', 'post'], 'notify/{type}/{payment?}', 'NotifyController@PayNotify')->name('notify');
    $api->post('tron', 'GatewayController@tron');
    $api->post('tronDirect', 'GatewayController@tronDirect');
    $api->get('order/check', 'OrderController@checkOrderStatus');
    $api->get('pay/detail', 'PayController@getPaymentList');
    $api->get('pay/url', 'PayController@getPaymentUrl');

    $api->post('send_mail', 'CommonController@sendMail');
    $api->post('register', 'AuthController@register');
    $api->post('login', 'AuthController@login');
    $api->post('forget', 'AuthController@forget');
    $api->post('telegram_login', 'AuthController@TelegramLogin');
    $api->post('logout', 'AuthController@logout');
    $api->get('get_config', 'CommonController@getConfig');
    $api->get('getLoginConfig', 'AuthController@getLoginConfig');

    Route::group(['middleware' => 'auth:api'], function (RouteContract $api) {
        $api->get('user/info', 'UserController@getUserInfo');
        $api->post('user/password/update', 'UserController@updatePassword');
        $api->post('user/address/update', 'UserController@updateAddress');
        $api->get('user/account', 'UserController@getData');
        $api->post('user/delete', 'UserController@delete');
        $api->get('announcements', 'UserController@getAnn');
        $api->get('dashboard/table', 'UserController@getDashboardTable');
        $api->get('dashboard/echarts', 'UserController@getDataChart');
        $api->get('order/list', 'OrderController@getList');
        $api->post('order/notify', 'OrderController@notify');
        $api->post('order/refund', 'OrderController@refund');
        $api->get('tutorial/list', 'TutorialController@getList');
        $api->post('tutorial/reset', 'TutorialController@resetSecret');
        $api->get('contract/list', 'ContractController@getList');
        $api->post('contract/submit', 'ContractController@submit');
        $api->post('contract/address/update', 'ContractController@addressUpdate');
        $api->get('settlement/list', 'OrderController@settlement');
        $api->get('domain/list', 'DomainController@getList');
        $api->post('domain/submit', 'DomainController@submit');
        $api->post('domain/delete', 'DomainController@delete');
    });
});
