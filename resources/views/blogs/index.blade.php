@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach(array_chunk((array) $blogs, 4) as $chunks)
            <div class="row gy-2">
                @foreach($chunks as $blog)
                    <div class="col-sm-3">
                        <div class="card">
                            <img src="http://via.placeholder.com/200x100" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">{{ \Illuminate\Support\Str::limit($blog['title'], 26) }}...</h5>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($blog['content'], 200) }}...</p>
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
