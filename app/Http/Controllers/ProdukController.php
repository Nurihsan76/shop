<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProdukResource;
use App\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data = Produk::all();
        $produk = ProdukResource::collection(Produk::all());
        $produk = $produk->sortByDesc('id');
        $produk = $produk->values()->all();
        if ($produk == false) {
            return response()->json([
                'status' => 'failed',
                'message' => 'data tidak tersedia',
                'data' => null
            ], 400);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'data' => $produk
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Produk::find($id);
        $produk = new ProdukResource($data);
        if (empty($data)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data id $id tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'data' => $produk
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function getbytag_id($id)
    {
        $data = Produk::where('tag_id', $id)->get();
        $produk = ProdukResource::collection($data);
        $produk = $produk->sortByDesc('id');
        $produk = $produk->values()->all();
        if (empty($data)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data id $id tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'data' => $produk
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function cari($produk)
    {
        // dd($produk);
        // $produk = Produk::whereHas('user', function($query) use ($produk) {
        //     $query->where("name", "ilike", "%".$produk."%");
        // })->orWhere("nama", "ilike", "%".$produk."%")->get();
        $data = Produk::where("nama", "ilike", "%".$produk."%")->get();
        $produk = ProdukResource::collection($data);
        $produk = $produk->sortByDesc('id');
        $produk = $produk->values()->all();
        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'data' => $produk
        ], 200);
    }
}