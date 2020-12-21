<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PesananResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $gambar = $this->foto;
        // $foto = $this->user->foto;

        return [
            'id' => $this->id,
            'produk_id' => $this->produk->id,
            'jumlah_produk' => $this->jumlah_produk,
            'pesanan_id' => $this->pesanan_id,
            'status' => $this->status,
            'jumlah_harga_produk' => number_format($this->jumlah_harga_produk,0,',','.'),
            'jumlah_harga' => $this->pesanan->jumlah_harga,
            'kode' => $this->pesanan->kode,
            'alamat' => $this->pesanan->alamat,
            'nomer' => $this->pesanan->nomer,
            'nama' => $this->produk->nama,
            'harga' => $this->produk->harga,
            'stok' => number_format($this->produk->stok,0,',','.'),
            'berat' => $this->produk->berat,
            'ukuran' => $this->produk->ukuran,
            'gambar' => $this->produk->foto,
            'descripsi' => $this->produk->descripsi,
            'tanggal_bayar' => $this->created_at->format('l, d-M-Y H:i a'),
            'penjual_id' => $this->penjual_id,
            'name' => $this->user->name,
            'foto' => $this->user->foto,
        ];

        // return parent::toArray($request);
    }
}
