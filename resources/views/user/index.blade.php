@extends('layouts.app')

@section('content')
<div class="container">

{{-- 
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        kategori
      </a>
      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="#">Action</a>
        <a class="dropdown-item" href="#">Another action</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">Something else here</a>
      </div> --}}

{{--             
    <form class="form-inline my-2 my-lg-0" action="/user/kategori " method="post">
        @csrf
      <select class="form-control" name="tag_id">
        <option value="1">kesehatan</option>
        <option value="2">kecantikan</option>
        <option value="3">otomotif</option>
      </select>
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form> --}}
  
  {{-- <a href="/user/create" class="btn btn-primary my-3">tambah article</a> --}}

@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

</div>


<div class="container mt-3">
    @foreach ($user->chunk(3) as $adminchunk)
    <div class="row">
        @foreach ($adminchunk as $item)
        <div class="col card mb-2 ml-2 mr-2" style="width: 18rem;">
            <img src="{{$item['foto']}}" class="card-img-top" alt="">
            <div class="card-body">
                <h3 class="card-text">{{ $item['name'] }}</h3>
                
                <a href="/user/{{ $item['id'] }}" class="btn btn-primary float-left">detail</a>
                <a href="/user/{{$item['id']}}/edit" class="btn btn-success float-left">edit</a>
                <form action="/user/{{ $item['id'] }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" name="submit" class="btn btn-danger" onclick="return confirm('yakin?')">delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
      @endforeach
      
      {{ $user->links() }}

</div>




@endsection