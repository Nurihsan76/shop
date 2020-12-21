<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $fillable = ['pembeli_id', 'jumlah_harga', 'status', 'kode'];
    
    public function pesanan_detail()
    {
        return $this->hasMany(PesananDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
