<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_user', 'refrence', 'total','status'
    ];
}
