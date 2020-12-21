<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tag'];

    public function produk()
    {
        return $this->hasMany(Produk::class);
    }

    public function member()
    {
        return $this->hasMany(Member::class);
    }
}
