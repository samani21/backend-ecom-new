<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $response = response($response)->withHeaders([
            'Content-Type' => 'application/json',
            'X-Custom-Header' => 'value',
        ]);
        return $response ? $response :  $err;
    }

    public function requestTransaction(Request $request)
    {
        $method =  $request->method;
        $user =  $request->id_user;
        $apikey = env('TRIPAY_API_KEY');
        $privatekey =  env('TRIPAY_PRIVATE_KEY');
        $marchentCode =  env('TRIPAY_MERCHANT_CODE');
        // dd($apikey,$privatekey,$marchentCode);
        $marchentRef = 'Px-' . time();

        $keranjang = DB::table('keranjang')->join('product', 'id_product', '=', 'product.id')->where('id_user', $user)->where('status', 1)
            ->select(
                'keranjang.id as sku',
                'product.name as name',
                'keranjang.jumlah as quantity',
                'product.new_price as price',
            )
            ->get();
        $total_k = DB::table('keranjang')->join('product', 'id_product', '=', 'product.id')->where('id_user', $user)->where('status', 1)
            ->select(DB::raw('sum(new_price*jumlah) as totala'))->first();
        $items = json_decode($keranjang);
        $amount =$total_k->totala;
        $data = [
            'method'         => $method,
            'merchant_ref'   => $marchentRef,
            'amount'         => $amount,
            'customer_name'  => 'Nama Pelanggan',
            'customer_email' => 'emailpelanggan@domain.com',
            'customer_phone' => '081234567890',
            'order_items'    =>  $items,
            'return_url'   => 'https://domainanda.com/redirect',
            'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
            'signature'    => hash_hmac('sha256', $marchentCode . $marchentRef . $amount, $privatekey)
        ];
        // return $data;
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
$response = response($response)->withHeaders([
            'Content-Type' => 'application/json',
            'X-Custom-Header' => 'value',
        ]);;
        // $data_p = [
        //     'id_user' => 9,
        //     'refrence' => $marchentRef,
        //     'total' => 10
        // ];
        // $insert = Pembayaran::create($data_p);
        return $response ?: $error;
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
