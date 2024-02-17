<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripayCallbackController extends Controller
{

    public function handle(Request $request)
    {
        $privatekey = 'wkhDe-vMOSU-05ZTc-YhXlv-vVoBg';
        $reference = $request->reference;
        $data = [
            'reference' => $reference,
            'status' => $request->status,
            'total_amount' => $request->total_amount
        ];

        $pembayaran = DB::table('pembayaran')->where('refrence', $reference)->first();

        if ($request->status == "PAID") {
            $pembayaran1 = Pembayaran::find($pembayaran->id);
            $pembayaran1->status = 2;
            $pembayaran1->save();
            $user = $pembayaran1->id_user;
            $keranjang = DB::table('keranjang')->where('id_user', $user)->where('status', 1)->get();
            foreach ($keranjang as $ke) {
                $keranjang1 = Keranjang::find($ke->id);
                $keranjang1->id_pembayaran = $pembayaran->id;
                $keranjang1->status = 2;
                $keranjang1->save();
            }
        } else {
            $pembayaran1 = Pembayaran::find($pembayaran->id);
            $pembayaran1->status = 1;
            $pembayaran1->save();
        }
        return response()->json(['status' => 'success', 'data' => $data, 'data1' => $keranjang]);
    }
}
