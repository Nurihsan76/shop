@extends('layouts.app')

@section('content')
<div class="container">

            
    <form class="form-inline my-2 my-lg-0" action="/produk/kategori " method="post">
        @csrf
      <select class="form-control" name="tag_id">
        @foreach ($tag as $item)
        <option value="{{$item->id}}">{{$item->tag}}</option>
        @endforeach
      </select>
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
  
  {{-- <a href="/produk/create" class="btn btn-primary my-3">tambah article</a> --}}

@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

</div>


<div class="container mt-3">
    @foreach ($produk->chunk(3) as $adminchunk)
    <div class="row">
        @foreach ($adminchunk as $item)
        <div class="col card mb-2 ml-2 mr-2" style="width: 18rem;">
            <img src="{{$item['foto']}}" class="card-img-top" alt="">
            <div class="card-body">
                <h3 class="card-text">{{ $item['nama'] }}</h3>
                
                <a href="/produk/{{ $item['id'] }}" class="btn btn-primary float-left">detail</a>
                {{-- <a href="/produk/{{$item['id']}}/edit" class="btn btn-success float-left">edit</a> --}}
                <form action="/produk/{{ $item['id'] }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" name="submit" class="btn btn-danger" onclick="return confirm('yakin?')">delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
      @endforeach
      
      {{ $produk->links() }}

</div>




@endsection