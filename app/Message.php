<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['from', 'to', 'message', 'is_read'];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
   
    // public function userFrom()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
    // public function userTo()
    // {
    //     return $this->belongsTo(User::class, 'to');
    // }
}