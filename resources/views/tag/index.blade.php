@extends('layouts.app')

@section('content')
<div class="container">


  <a href="tag/create" class="btn btn-primary my-3">Tambah Kategori</a>

@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

</div>


<div class="container mt-3">
    @foreach ($tag->chunk(3) as $adminchunk)
    <div class="row">
        @foreach ($adminchunk as $item)
        <div class="col card mb-2 ml-2 mr-2" style="width: 18rem;">
            <div class="card-body">
                <h3 class="card-text">{{ $item['tag'] }}</h3>
                
                <a href="/tag/{{$item['id']}}/edit" class="btn btn-success float-left">edit</a>
                <form action="/tag/{{ $item['id'] }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" name="submit" class="btn btn-danger" onclick="return confirm('yakin?')">delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
      @endforeach
      
      {{ $tag->links() }}

</div>




@endsection