<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Product;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembayaranController extends BaseController
{


    public function store(Request $request)
    {
        $id_user = $request->id_user;
        $keranjang = DB::table('keranjang')->where('id_user', $id_user)->where('status', 1)->get();
        $data = [
            'id_user' => $request->id_user,
            'refrence' => $request->reference,
            'total' => $request->total,
            'status' => 1
        ];

        $insert = Pembayaran::create($data);

        foreach ($keranjang as $ke) {
            $keranjang1 = Keranjang::find($ke->id);
            $keranjang1->id_pembayaran = 0;
            $keranjang1->status = 1;
            $keranjang1->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $keranjang
        ], 200);
    }


    public function index($id)
    {
        $pembayaran = DB::table('pembayaran')->where('id_user', $id)->orderBy('id', 'desc')
            ->first();
        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $pembayaran
        ], 200);
    }
}
