<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProdukResource;
use App\Produk;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::id();
        $user = User::where('id', $id)->get();
        $data = Produk::where('user_id', $id)->get();
        $produk = ProdukResource::collection($data);
        $produk = $produk->sortByDesc('id');
        $produk = $produk->values()->all();
        if ($user == false) {
            return response()->json([
                'status' => 'failed',
                'message' => "data id $id tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'user' => $user,
            'data' => $produk
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $foto = null;
        
        if ($request->foto) {
            // $foto = $request->foto->getClientOriginalName() . '-' . time() . '.' . $request->foto->extension();
            // $request->foto->move(public_path('img'), $foto);

            $img = base64_encode(file_get_contents($request->foto));
            $client = new Client();
            $res = $client->request('POST', 'https://freeimage.host/api/1/upload', [
                'form_params' => [
                    'key' => '6d207e02198a847aa98d0a2a901485a5',
                    'action' => 'upload',
                    'source' => $img,
                    'format' => 'json',
                ]
            ]);
            $array = json_decode($res->getBody()->getContents());
            $foto = $array->image->file->resource->chain->image;
        }
        // dd($foto);

        $user_id = Auth::id();
        $produk = $request->all();
        $validator = Validator::make($produk, [
            'nama' => 'required',
            'harga' => 'required',
            'stok' => 'required',
            'berat' => 'required',
            'ukuran' => 'required',
            'descripsi' => 'required',
            'foto' => 'required',
            'tag_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                $validator->errors(),
                'message' => 'data gagal ditambah',
                'data' => null
            ], 400);
        }

        $produk = Produk::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'berat' => $request->berat,
            'ukuran' => $request->ukuran,
            'foto' => $foto,
            'descripsi' => $request->descripsi,
            'tag_id' => $request->tag_id,
            'user_id' => $user_id,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'data behasil ditambah',
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
        if ($data == false) {
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $foto = null;

        if ($request->foto) {
            $img = base64_encode(file_get_contents($request->foto));
            $client = new Client();
            $res = $client->request('POST', 'https://freeimage.host/api/1/upload', [
                'form_params' => [
                    'key' => '6d207e02198a847aa98d0a2a901485a5',
                    'action' => 'upload',
                    'source' => $img,
                    'format' => 'json',
                ]
            ]);
            $array = json_decode($res->getBody()->getContents());
            $foto = $array->image->file->resource->chain->image;
        }

        $user_id = Auth::id();
        $produk = $request->all();
        $validator = Validator::make($produk, [
            'nama' => 'required',
            'harga' => 'required',
            'stok' => 'required',
            'berat' => 'required',
            'ukuran' => 'required',
            'descripsi' => 'required',
            'foto' => 'required',
            'tag_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                $validator->errors(),
                'message' => 'data gagal ditambah',
                'data' => null
            ], 400);
        }
        // dd($user_id);

        $produk = Produk::find($id);
        // File::delete(public_path("img/{$produk->foto}"));

        $produk->nama = $request->nama;
        $produk->harga = $request->harga;
        $produk->stok = $request->stok;
        $produk->berat = $request->berat;
        $produk->ukuran = $request->ukuran;
        $produk->foto = $foto;
        $produk->descripsi = $request->descripsi;
        $produk->tag_id = $request->tag_id;
        $produk->user_id = $user_id;
        $produk->save();

        return response()->json([
            'status' => 'success',
            'message' => 'data behasil ditambah',
            'data' => $produk
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        // File::delete(public_path("img/{$produk->foto}"));
        $produk = Produk::destroy($produk->id);

        if ($produk == false) {
            return response()->json([
                'status' => 'failed',
                'message' => "data $id tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data berhasil dihapus',
            'data' => $produk
        ], 200);
    }
}
