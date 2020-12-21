<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource;
use App\Pesanan;
use App\PesananDetail;
use App\Produk;
use App\User;
// use App\User;
use App\Tag;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
        // $users = User::->get();
        $user = User::where('id', '!=', Auth::id())->where('id', '!=', 1)->orderBy('id', 'desc')->paginate(6);
        return view('user/index', compact('user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('user/detail', compact('user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function showAdmin(User $user)
    {
        return view('admin/detail', compact('user'));
    }

    public function edit(User $user)
    {
        return view('user/edit', compact('user'));
    }


    public function update(Request $request, User $user)
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

        // $user_id = Auth::id();

        $user = User::find($user->id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->get('password'));
        $user->nomer = $request->nomer;
        $user->alamat = $request->alamat;
        $user->kode = $request->kode;
        $user->foto = $foto;
        $user->save();

        return redirect('/user')->with('status', 'data article berhasil diubah');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $pesanan = Pesanan::where('pembeli_id', $user['id'])->first();
        if(!empty($pesanan)) {
            PesananDetail::where('pesanan_id', $pesanan->id)->delete();
        }
        Pesanan::where('pembeli_id', $user['id'])->delete();
        Produk::where('user_id', $user['id'])->delete();
        User::destroy($user['id']);
        return redirect('/user')->with('status', 'data user berhasil dihapus');
    }
}