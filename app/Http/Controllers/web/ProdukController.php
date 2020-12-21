<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\PesananDetail;
use App\Produk;
// use App\Produk;
use App\Tag;
use Illuminate\Http\Request;

class ProdukController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $produk = Produk::with('tag')->get();
        // $produk = Produk::all();
        $produk = Produk::orderBy('id', 'desc')->paginate(6);
        $tag = Tag::all();
        // $produk = ProdukResource::collection(Produk::all());
        // $produk = $produk->sortByDesc('id');
        // $produk = $produk->values()->all();
        return view('produk/index', compact('produk', 'tag'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Produk $produk)
    {
        return view('produk/detail', compact('produk'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produk $produk)
    {

        // $barang = Produk::where('tag_id', $produk['id'])->first();
        // if(!empty($barang)){        
        PesananDetail::where('produk_id', $produk->id)->delete();
        // }
        Produk::destroy($produk['id']);
        return redirect('/produk')->with('status', 'data article berhasil dihapus');
    }

    public function kategori(Request $request)
    {
        $tag_id = $request['tag_id'];
        // dd($tag_id);
        $produk = Produk::where('tag_id', $tag_id)->get();
        $tag = Tag::all();
        return view('produk/kategori', compact('produk', 'tag'));
        // var_dump($isi);
    }
}