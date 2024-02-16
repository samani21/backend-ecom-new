<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->post('api/login', 'AuthController@login');
$router->post('api/register', 'AuthController@register');

$router->get('/product', 'ProductController@index');
$router->get('/new-collection', 'ProductController@collection');
$router->get('/women', 'ProductController@women');
$router->post('/product/upload', 'ProductController@create');
$router->post('/product/add', 'ProductController@store');
$router->delete('/product/{id}/delete', 'ProductController@destroy');
$router->get('/image', 'ProductController@get_image');

$router->post('/keranjang/upload', 'KeranjangController@store');
$router->get('/keranjang/total/{id}', 'KeranjangController@total');
$router->get('/keranjang/{id}', 'KeranjangController@index');
$router->get('/keranjang/checkout/{id}', 'KeranjangController@checkout');
$router->post('/keranjang/delete/{id}', 'KeranjangController@distroy');

$router->post('/pembayaran/bayar', 'pembayaranController@store');

$router->get('tripay', 'TripayController@getPaymentChennels');
$router->post('transaksi', 'TripayController@requestTransaction');
$router->get('detail-transaksi/{refrence}', 'TripayController@detailTransaction');
