<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Product;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KeranjangController extends BaseController
{


    public function store(Request $request)
    {
        $id_user = $request->id_user;
        $id_product = $request->id_product;
        $user = DB::table('keranjang')->where('id_user', $id_user)
            ->where('id_product', $id_product)
            ->where('status','<=', 1)->first();
        if (!$user) {
            $data = [
                'id_product' => $request->id_product,
                'id_user' => $request->id_user,
                'jumlah' => 1,
                'id_pembayaran' => 0,
                'status' => 1
            ];

            $insert = Keranjang::create($data);
        } else {
            $invitation = Keranjang::find($user->id);
            $invitation->id_product = $id_product;
            $invitation->jumlah = $user->jumlah + 1;
            $invitation->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $user
        ], 200);
    }

    public function total($id)
    {
        $keranjang = DB::table('keranjang')->where('id_user', $id)->where('status', 1)->where('jumlah', '>', 0)
            ->select(DB::raw('sum(jumlah) as total'))
            ->first();

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
            ->where('status', 1)
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

    public function checkout($id)
    {
        $keranjang = DB::table('keranjang')->join('product', 'product.id', '=', 'keranjang.id_product')
            ->where('id_user', $id)
            ->where('jumlah', '>', 0)
            ->where('status', 1)
            ->select(DB::raw("sum(jumlah*new_price) as total"))
            ->first();
        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $keranjang
        ], 200);
    }

    public function distroy($id)
    {
        // $id_user = $request->id_user;
        // $id_product = $request->id_product;

        $invitation = Keranjang::find($id);
        $invitation->jumlah = $invitation->jumlah - 1;
        $invitation->save();

        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $invitation
        ], 200);
    }
}
