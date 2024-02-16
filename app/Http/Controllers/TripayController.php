<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class TripayController extends Controller
{
    public function getPaymentChennels()
    {
        $apkey = env('TRIPAY_API_KEY');
        // dd($apkey);

        $payload = [
            'code' => 'BRIVA'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => "https://tripay.co.id/api-sandbox/merchant/payment-channel",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => array(
                "Authorization: Bearer " . $apkey
            ),
            CURLOPT_FAILONERROR    => false

        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response)->data;
        return $response ? $response :  $err;
    }

    public function requestTransaction(Request $request)
    {
        $method = $method = 'BCAVA';
        $apikey = env('TRIPAY_API_KEY');
        $privatekey =  env('TRIPAY_PRIVATE_KEY');
        $marchentCode =  env('TRIPAY_MERCHANT_CODE');
        // dd($apikey,$privatekey,$marchentCode);
        $marchentRef = 'Px-' . time();

        $amount       = 1000000;

        $data = [
            'method'         => $method,
            'merchant_ref'   => $marchentRef,
            'amount'         => $amount,
            'customer_name'  => 'Nama Pelanggan',
            'customer_email' => 'emailpelanggan@domain.com',
            'customer_phone' => '081234567890',
            'order_items'    => [
                [
                    'sku'         => 'FB-06',
                    'name'        => 'Nama Produk 1',
                    'price'       => 500000,
                    'quantity'    => 1,
                    'product_url' => 'https://tokokamu.com/product/nama-produk-1',
                    'image_url'   => 'https://tokokamu.com/product/nama-produk-1.jpg',
                ],
                [
                    'sku'         => 'FB-07',
                    'name'        => 'Nama Produk 2',
                    'price'       => 500000,
                    'quantity'    => 1,
                    'product_url' => 'https://tokokamu.com/product/nama-produk-2',
                    'image_url'   => 'https://tokokamu.com/product/nama-produk-2.jpg',
                ]
            ],
            'return_url'   => 'https://domainanda.com/redirect',
            'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
            'signature'    => hash_hmac('sha256', $marchentCode . $marchentRef . $amount, $privatekey)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apikey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $data_p = [
            'id_user' => 9,
            'refrence' => $marchentRef,
            'total' => 10
        ];
        $insert = Pembayaran::create($data_p);
        return $insert ?: $error;
    }

    public function detailTransaction($refrence)
    {
        $apiKey = env('TRIPAY_API_KEY');

        $payload = ['reference'    => $refrence];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/detail?' . http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);
        // Or set multiple headers at once
        $response = response($response)->withHeaders([
            'Content-Type' => 'application/json',
            'X-Custom-Header' => 'value',
        ]);
        return $response;
    }
}
