<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Keranjang extends Model
{
    protected $table = 'keranjang';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_product', 'id_user', 'jumlah', 'status', 'id_pembayaran'
    ];
}
