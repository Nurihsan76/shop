<?php

namespace App\Http\Controllers;

use App\Http\Resources\PesananResource;
use App\Http\Resources\ProdukResource;
use App\Pesanan;
use App\PesananDetail;
use App\Produk;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PesananController extends Controller
{
    protected $pesanan;
    protected $pesanan_detail = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $produk = Produk::where('stok', 0)->first();
        // $pesanan_detail = PesananDetail::where('produk_id', $produk->id)->delete();
        $id = Auth::id();
        $pesanan = Pesanan::where('pembeli_id', $id)->where('status', 0)->first();
        PesananDetail::where('pesanan_id', $pesanan->id)->whereHas('produk', function ($query) {
            $query->where('stok', 0);
        })->delete();
        // $pesanan = Pesanan::all();
        // dd($pesanan);
        $pesanan_detail = PesananDetail::where('pesanan_id', $pesanan->id)->get();
        $pesanan_detail = PesananResource::collection($pesanan_detail);
        $pesanan_detail = $pesanan_detail->sortByDesc('id');
        $pesanan_detail = $pesanan_detail->values()->all();

        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'pesanan_detail' => $pesanan_detail,
            // 'pesanan' => $pesanan,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function keranjang(Request $request, $id)
    {
        $pesanan = $request->all();
        $validator = Validator::make($pesanan, [
            'jumlah_produk' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                $validator->errors(),
                'message' => 'data gagal ditambah',
                'data' => null
            ], 400);
        }

        $user_id = Auth::id();
        $produk = Produk::find($id);

        if ($produk->user_id == $user_id) {
            return response()->json([
                'status' => 'failed',
                'message' => "anda tidak dapat membeli barang yang anda jual",
                'data' => null
            ], 400);
        }

        if ($request->jumlah_produk > $produk->stok) {
            return response()->json([
                'status' => 'failed',
                'message' => "pesanan melebihi stok",
                'data' => null
            ], 400);
        }

        // cek pesanan
        $cek_pesanan = Pesanan::where('pembeli_id', $user_id)->where('status', 0)->first();
        if (empty($cek_pesanan)) {
            $pesanan = new Pesanan();
            $pesanan->pembeli_id = $user_id;
            $pesanan->status = 0;
            $pesanan->kode = mt_rand(100, 999);
            $pesanan->jumlah_harga = 0;
            $pesanan->save();
        }

        // sama kek $cek_pesanan
        $pesanan_baru = Pesanan::where('pembeli_id', $user_id)->where('status', 0)->first();
        // cek pesanan detail
        $cek_pesanan_detail = PesananDetail::where('produk_id', $produk->id)->where('pesanan_id', $pesanan_baru->id)->first();
        // dd($produk);

        if (empty($cek_pesanan_detail)) {
            $pesanan_detail = new PesananDetail();
            $pesanan_detail->produk_id = $produk->id;
            $pesanan_detail->pesanan_id = $pesanan_baru->id;
            $pesanan_detail->penjual_id = $produk->user_id;
            $pesanan_detail->status = 0;
            $pesanan_detail->jumlah_produk = $request->jumlah_produk;
            $pesanan_detail->jumlah_harga_produk = $produk->harga * $request->jumlah_produk;
            $pesanan_detail->save();
        } elseif ($cek_pesanan_detail->jumlah_produk + $request->jumlah_produk > $produk->stok) {
            return response()->json([
                'status' => 'failed',
                'message' => "pesanan anda melebihi stok barang",
                'data' => null
            ], 400);
        } else {
            // sama kek $cek_pesanan_detail
            $pesanan_detail = PesananDetail::where('produk_id', $produk->id)->where('pesanan_id', $pesanan_baru->id)->first();

            $pesanan_detail->jumlah_produk = $pesanan_detail->jumlah_produk + $request->jumlah_produk;

            // harga sekarang
            $harga_pesanan_detail_baru = $produk->harga * $request->jumlah_produk;
            $pesanan_detail->jumlah_harga_produk = $pesanan_detail->jumlah_harga_produk + $harga_pesanan_detail_baru;
            $pesanan_detail->update();
        }
        // jumlah total
        $pesanan = Pesanan::where('pembeli_id', $user_id)->where('status', 0)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga + $produk->harga * $request->jumlah_produk;
        $pesanan->update();

        if (empty($pesanan)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'data' => $pesanan,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function hapus($id)
    {
        $pesanan_detail = PesananDetail::find($id);
        $pesanan = Pesanan::where('id', $pesanan_detail->pesanan_id)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga - $pesanan_detail->jumlah_harga_produk;
        $pesanan->update();

        $pesanan_detail = PesananDetail::destroy($pesanan_detail->id);

        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data berhasil dihapus',
            'data' => $pesanan_detail
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function checkout()
    {
        $user_id = Auth::id();

        $pesanan = Pesanan::where('pembeli_id', $user_id)->where('status', 0)->first();

        // $pesanan_detail = PesananDetail::all();

        $pesanan_detail = PesananDetail::where('pesanan_id', $pesanan->id)->get();
        $pesanan_detail = PesananResource::collection($pesanan_detail);
        $pesanan_detail = $pesanan_detail->sortByDesc('id');
        $pesanan_detail = $pesanan_detail->values()->all();

        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            // 'pesanan' => $pesanan,
            'pesanan_detail' => $pesanan_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function konfirmasi(Request $request)
    {
        $user_id = Auth::id();

        $pesanan = $request->all();
        $validator = Validator::make($pesanan, [
            'alamat' => 'required',
            'nomer' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                $validator->errors(),
                'message' => 'data gagal ditambah',
                'data' => null
            ], 400);
        }

        $pesanan = Pesanan::where('pembeli_id', $user_id)->where('status', 0)->first();
        $pesanan_id = $pesanan->id;
        $pesanan->alamat = $request->alamat;
        $pesanan->nomer = $request->nomer;
        $pesanan->status = 1;
        $pesanan->update();

        $pesanan_details = PesananDetail::where('pesanan_id', $pesanan_id)->where('status', 0)->get();
        foreach ($pesanan_details as $pesanan_detail) {
            $pesanan_detail->status = 1;
            $pesanan_detail->update();

            $produk = Produk::where('id', $pesanan_detail->produk_id)->first();
            $produk->stok = $produk->stok - $pesanan_detail->jumlah_produk;
            $produk->update();
        }

        if (empty($pesanan_details)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            // 'pesanan' => $pesanan,
            'pesanan_detail' => $pesanan_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function showKonfirmasiPenjual()
    {
        $user_id = Auth::id();

        $pesanan_detail = PesananDetail::where('penjual_id', $user_id)->where('status', '!=', 0)->get();
        $pesanan_detail = PesananResource::collection($pesanan_detail);
        $pesanan_detail = $pesanan_detail->sortByDesc('id');
        $pesanan_detail = $pesanan_detail->values()->all();



        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            // 'pesanan' => $pesanan,
            'pesanan_detail' => $pesanan_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function konfirmasiPenjual($id)
    {
        // $user_id = Auth::id();

        // $produk = Produk::find($id);
        // $produk_id = $produk->id;
        $pesanan_detail = PesananDetail::where('id', $id)->where('status', 1)->first();
        // dd($pesanan_details);
        // foreach ($pesanan_details as $pesanan_detail) {
        $pesanan_detail->status = 2;
        $pesanan_detail->update();
        // }

        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            // 'pesanan' => $pesanan,
            'pesanan_detail' => $pesanan_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function konfirmasiPembeli($id)
    {
        $pesanan_detail = PesananDetail::where('id', $id)->where('status', 2)->first();
        $pesanan_detail->status = 3;
        $pesanan_detail->update();

        // $produk = Produk::find($id);
        // $produk_id = $produk->id;
        // $pesanan_details = PesananDetail::where('produk_id', $produk_id)->where('status', 2)->get();
        // // dd($pesanan_details);
        // foreach ($pesanan_details as $pesanan_detail) {
        //     $pesanan_detail->status = 3;
        //     $pesanan_detail->update();
        // }

        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            // 'pesanan' => $pesanan,
            'pesanan_detail' => $pesanan_detail
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        $user_id = Auth::id();
        // $pesanan = Pesanan::where('pembeli_id', $id)->where('status', 1)->with('pesanan_detail')->get();
        // $pesanan = Pesanan::where('pembeli_id', $id)->where('status', 1)->first();

        // if (!empty($pesanan)) {
        $pesanan_detail = PesananDetail::whereHas('pesanan', function ($query) use ($user_id) {
            $query->where('pembeli_id', $user_id);
        })->where('status', '!=', 0)->get();
        $pesanan_detail = PesananResource::collection($pesanan_detail);
        $pesanan_detail = $pesanan_detail->sortByDesc('id');
        $pesanan_detail = $pesanan_detail->values()->all();
        // }
        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'pesanan_detail' => $pesanan_detail,
            // 'pesanan' => $pesanan,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function showhistory($id)
    {
        $pesanan_detail = PesananDetail::where('id', $id)->get();
        $pesanan_detail = PesananResource::collection($pesanan_detail);
        $pesanan_detail = $pesanan_detail->sortByDesc('id');
        $pesanan_detail = $pesanan_detail->values()->all();

        if (empty($pesanan_detail)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'pesanan_detail' => $pesanan_detail,
            // 'pesanan' => $pesanan,
        ], 200);
    }
}
