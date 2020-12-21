@extends('layouts.app')

@section('content')


<div class="container">

<div class="col card" style="width: 50%;">
    <img src="{{$produk['foto']}}" class="card-img-top" alt="">
    <div class="card-body">
      <h1 class="card-text">{{ $produk['nama'] }}</h1>
      <p>harga : {{ $produk['harga'] }}</p>
      <p>stok : {{ $produk['stok'] }}</p>
      <p>berat : {{ $produk['berat'] }}</p>
      <p>ukuran : {{ $produk['ukuran'] }}</p>
      <p>kategori : {{ $produk->tag['tag'] }}</p>
      <p>descripsi : {{ $produk['descripsi'] }}</p>
      <a href="/produk" class="btn btn-primary">kembali</a>
    </div>
  </div>
</div>

@endsection