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
        $id_product = $request->id_product;
        $keranjang = DB::table('keranjang')->where('id_user', $id_user)->where('status', 1)->get();
        $data = [
            'id_user' => $request->id_user,
            'refrensi' => 1,
            'total' => $request->total
        ];

        $insert = Pembayaran::create($data);

        foreach ($keranjang as $ke) {
            $keranjang1 = Keranjang::find($ke->id);
            $keranjang1->id_pembayaran = $insert->id;
            $keranjang1->status = 2;
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
        $keranjang = DB::table('keranjang')->join('product', 'product.id', '=', 'keranjang.id_product')
            ->where('id_user', $id)
            ->select(
                'keranjang.id',
                'jumlah',
                'name',
                'image',
                'category',
                'new_price',
                'old_price'
            )
            ->where('jumlah', '>', 0)
            ->get();
        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $keranjang
        ], 200);
    }
}
