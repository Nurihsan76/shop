<?php

namespace App\Http\Controllers;

use App\Pesanan;
use App\PesananDetail;
use App\Produk;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'nomer' => 'required',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
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

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'nomer' => $request->nomer,
            'alamat' => $request->alamat,
            'foto' => $foto

        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function update(Request $request)
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

        $user_id = Auth::id();
        $user = $request->all();
        $validator = Validator::make($user, [
            // 'name' => 'required|string',
            // 'email' => 'required|string|email',
            'password' => 'required|string|min:6|confirmed',
            'nomer' => 'required',
            'alamat' => 'required|string',
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

        $user = User::find($user_id);
        // $data = $request->all();
        // $result = array_filter($data);
        // // File::delete(public_path("img/{$user->foto}"));

        // $user->name = $request->name;
        // $user->email = $request->email;
        $user->password = Hash::make($request->get('password'));
        $user->nomer = $request->nomer;
        $user->alamat = $request->alamat;
        $user->foto = $foto;
        $user->update();
        // $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'data behasil ditambah',
            'data' => $user
        ], 200);
    }


    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

      /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user_id = Auth::id();
        $pesanan = Pesanan::where('pembeli_id', $user_id)->first();
        PesananDetail::where('pesanan_id', $pesanan->id)->delete();
        Pesanan::where('pembeli_id', $user_id)->delete();
        Produk::where('user_id', $user_id)->delete();
        $user = User::destroy($user_id);
        return response()->json([
            'status' => 'success',
            'message' => 'data behasil dihapus',
            'data' => $user
        ], 200);
    }

    public function logout(Request $request){
        try{
            $this->validate($request,['token'=> 'required']);
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['sukses' => true,'pesan'=>'Berhasil Log Out']);
        }catch(\Exception $e){
            return response()->json(['sukses'=>false, 'pesan'=>'Gagal Logout'], $e->getStatusCode());
        }
    }
}

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class UserController extends Controller
// {
//     //
// }
