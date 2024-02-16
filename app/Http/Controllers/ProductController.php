<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends BaseController
{
    public function index()
    {
        $product = Product::all();

        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $product
        ], 200);
    }

    public function collection()
    {
        $product = DB::table('product')->orderBy('id', 'desc')->limit(8)->get();

        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $product
        ], 200);
    }

    public function women()
    {
        $product = DB::table('product')->where('category', 'women')->limit(5)->get();

        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $product
        ], 200);
    }

    public function create(Request $request)
    {
        $name_file = strtotime(date("Y-d-m H:i:s"));
        $image = $request->file('image');
        $fileName = $image->getClientOriginalExtension();
        $foto = $name_file . "." . $fileName;
        $request->file('image')->move(storage_path('image'), $foto);

        $data = [
            "image" => "http://localhost:8000/image?foto=" . $foto,
        ];

        // $insert = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $data
        ], 200);
    }

    public function store(Request $request)
    {

        $data = [
            'name' => $request->name,
            'image' => $request->image,
            'category' => $request->category,
            'new_price' => $request->new_price,
            'old_price' => $request->old_price,
            'date' => date('Y-m-d'),
            'avilabel' => 1,
        ];

        $insert = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'List Semua Post',
            'data'    => $insert
        ], 200);
    }

    public function get_image(Request $request)
    {
        $file = $request->get('foto');
        $avatar_path = storage_path('image') . '/' . $file;
        if (file_exists($avatar_path)) {
            $file = file_get_contents($avatar_path);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }
        $res['success'] = false;
        $res['message'] = "Avatar not found";

        return $res;
    }

    public function destroy($id)
    {
        Product::find($id)->delete();
        $success_message = "Data Product Berhasil Dihapus";
        return response()->json([
            'success' => true,
            'message' => $success_message,
        ], 200);
    }
}
