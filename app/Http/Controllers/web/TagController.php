<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\PesananDetail;
use App\Produk;
use App\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
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
        $tag = tag::orderBy('id', 'desc')->paginate(6);
        return view('tag/index', compact('tag'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tag/tambah');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Tag::create($request->only('tag'));
        return redirect('/tag')->with('status', 'data tag berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag)
    {
        // return view('user/edit', compact('user'));
        return view('tag/edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {

        Tag::where('id', $tag['id'])
            ->update([
                'tag' => $request['tag'],
            ]);

        return redirect('/tag')->with('status', 'data tag berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $produk = Produk::where('tag_id', $tag['id'])->first();
        if (!empty($produk)) {
            PesananDetail::where('produk_id', $produk->id)->delete();
        }
        Produk::where('tag_id', $tag['id'])->delete();
        Tag::destroy($tag['id']);

        return redirect('/tag')->with('status', 'data berhasil dihapus');
    }
}
