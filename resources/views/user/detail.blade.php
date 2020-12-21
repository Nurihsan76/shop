@extends('layouts.app')

@section('content')


<div class="container">

<div class="col card" style="width: 50%;">
    <img src="{{$user['foto']}}" class="card-img-top" alt="">
    <div class="card-body">
      <h1 class="card-text">{{ $user['name'] }}</h1>
      <p>email : {{ $user['email'] }}</p>
      <p>nomer : {{ $user['nomer'] }}</p>
      <p>alamat : {{ $user['alamat'] }}</p>
      <a href="/user" class="btn btn-primary">kembali</a>
    </div>
  </div>
</div>

@endsection