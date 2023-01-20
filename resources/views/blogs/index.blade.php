@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach(array_chunk((array) $blogs, 4) as $chunks)
            <div class="row">
                @foreach($chunks as $blog)
                    <div class="col-sm-3">
                        <div class="card custom-card">
                            <img src="{{ URL::asset('/images/'.$blog['image']) }}" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">{{ \Illuminate\Support\Str::limit($blog['title'], 26) }}...</h5>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($blog['content'], 140) }}...</p>
                                <a href="blog-detail-page/{{ $blog['id'] }}" class="stretched-link">Read More...</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    {{--    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}--}}
@endpush
