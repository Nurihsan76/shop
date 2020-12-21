<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\User;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

class MessageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//         $id = Auth::id();
//         $users = Message::with(['userFrom', 'userTo'])
//         ->where('messages.to', $id)
//         ->orWhere('messages.from', $id)
//         ->latest();
// // dd($kontak);
//         $keys = [];
//         foreach ($users->get() as $key => $user) {
//             if ($user->userFrom->id == $id) {
//                 $keys[$key] = $user->userTo->id;
//             } else {
//                 $keys[$key] = $user->userFrom->id;
//             }
//         }
//         $keys = array_unique($keys);
//         $ids = implode(',', $keys);
//         $users = User::whereIn('id', $keys);

        // $user_id = Auth::id();
        // $kontak = User::where('id', '!=', $user_id)->get();
        // // $user = $user->id;
        // // $user = User::where('id', '!=', $user_id)->get();
        // $kontak = User::whereHas('message', function ($query) use ($user_id) {
        //     $query->where('from', $user_id);
        // // })->orWhere('message', function ($query) use ($user) {
        // //     $query->where('to', $user->id);
        // })->get();

        $from = DB::table('users')
        ->join('messages', 'users.id', '=', 'messages.from')
        ->where('users.id', '!=', Auth::id())
        ->where('messages.to', '=', Auth::id())
        ->select('users.id', 'users.name', 'users.foto')
        ->distinct()->get()->toArray();

        $to = DB::table('users')
        ->join('messages', 'users.id', '=', 'messages.to')
        ->where('users.id', '!=', Auth::id())
        ->where('messages.from', '=', Auth::id())
        ->select('users.id', 'users.name', 'users.foto')
        ->distinct()->get()->toArray();
        
        $kontak = array_unique(array_merge($from, $to), SORT_REGULAR);

        if (empty($kontak)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'kontak' => $kontak,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMessage($id)
    {
        
        $user_id = Auth::id();
        if ($id == $user_id) {
            return response()->json([
                'status' => 'failed',
                'message' => 'anda tidak data melihat pesan kepada diri anda sendiri',
                'data' => null
            ], 400);
        
        }

        $user = User::where('id', $id)->get();
        $message = Message::where(function ($quey) use ($user_id, $id) {
            $quey->where('from', $user_id)->where('to', $id);
        })->orWhere(function ($quey) use ($user_id, $id) {
            $quey->where('from', $id)->where('to', $user_id);
        })->get();
        // $message = Message::where(['from', $user_id], ['to', $id])->orWhere(['from', $id], ['to', $user_id])->get();

        if (empty($message)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'message' => $message,
            'user' => $user,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, $id)
    {

        $user_id = Auth::id();
        if ($id == $user_id) {
            return response()->json([
                'status' => 'failed',
                'message' => 'anda tidak data mengirim pesan kepada diri anda sendiri',
                'data' => null
            ], 400);
        }

        $message = $request->all();
        $validator = Validator::make($message, [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                $validator->errors(),
                'message' => 'data gagal ditambah',
                'data' => null
            ], 400);
        }
        
        $data = Message::create([
            'from' => $user_id,
            'to' => $id,
            'message' => $request->message,
            'is_read' => 0,
        ]);
        
        // event(new Message($request->message));

        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $pusher = new Pusher(
            '4f37117afb3f1883c957',
            'a6760cd4ea55eb268d97',
            '1113367',
            $options
        );

        // $message = $request->message;

        // $message = ['from' => $from, 'to' => $to, 'message' => $message];
        $pusher->trigger('my-channel', 'my-event', $data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'data behasil ditambah',
            'data' => $data
        ], 200);
    }

    public function cari($user)
    {
        // dd($user);
        $data = User::where("name", "ilike", "%".$user."%")->get();
        // $user = ProdukResource::collection($data);
        // $user = $user->sortByDesc('id');
        // $user = $user->values()->all();
        if (empty($data)) {
            return response()->json([
                'status' => 'failed',
                'message' => "data id $data tidak tersedia",
                'data' => null
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data tersedia',
            'data' => $data
        ], 200);
    }
}
