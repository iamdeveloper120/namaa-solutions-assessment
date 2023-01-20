@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            @if(isset($blog))
            <img src="{{ URL::asset('/images/'.$blog['image']) }}" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title">{{ $blog->title }}</h5>
                <p class="card-text">{{ $blog->content }}</p>
            </div>
            @endif
        </div>
    </div>
@endsection
