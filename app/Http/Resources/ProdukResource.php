<?php

namespace App\Http\Resources;

use App\Produk;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $gambar = "https://api-shop1.herokuapp.com/img/{$this->foto}";
        // $foto = "https://api-shop1.herokuapp.com/img/{$this->user->foto}";
        // $gambar = url('img/'.$this->foto);
        // $foto = url('img/'.$this->user->foto);
        $gambar = $this->foto;
        $foto = $this->user->foto;

        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'harga' => number_format($this->harga,0,',','.'),
            'stok' => $this->stok,
            'berat' => $this->berat,
            'ukuran' => $this->ukuran,
            'gambar' => $gambar,
            'descripsi' => $this->descripsi,
            'created_at' => $this->created_at->format('l, d-M-Y H:i a'),
            'update_at' => $this->updated_at->diffForHumans(),
            'tag' => $this->tag->tag,
            'user_id' => $this->user->id,
            'user' => $this->user->name,
            'email' => $this->user->email,
            'nomer' => $this->user->nomer,
            'alamat' => $this->user->alamat,
            'foto' => $foto,
        ];
        // return parent::toArray($request);
    }
}
