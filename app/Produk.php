<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['nama', 'harga', 'stok', 'berat', 'ukuran', 'foto', 'descripsi', 'tag_id', 'user_id'];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function pesanan_detail()
    {
        return $this->belongsTo(PesananDetail::class);
    }
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

}
