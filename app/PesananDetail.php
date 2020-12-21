<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $fillable = ['produk_id', 'pesanan_id', 'penjual_id', 'jumlah_produk', 'jumlah_harga_produk'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'penjual_id');
    }
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
