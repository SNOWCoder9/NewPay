<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->resource('/user', 'UserController');
    $router->resource('/order', 'OrderController');
    $router->get('/config', 'ConfigController@systemSetting');
    $router->resource('/announcement', 'AnnouncementController');
    $router->resource('/shop', 'ShopController');
    $router->resource('/tutorial', 'TutorialController');
    $router->resource('/contract', 'ContractController');
    $router->resource('/settlement', 'SettlementController');
    $router->resource('/payment', 'PaymentController');
    $router->resource('/domain', 'DomainController');
    $router->get('/dataclear', 'DataController@dataCleaning');

    $router->post('/settlement/order', 'SettlementController@settlement');
    $router->get('/components/config/explain', 'SettlementController@settlement');
});
