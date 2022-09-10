@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <div style="background-color: #ffffffa6;">
                <div class="p-2" class="card-header"><h5 class="mt-2">Search Results</h5></div>

                <div style="background-color: #ffffffa6;">

                    @foreach($posts as $post)
                        <div class="row">
                            <div class="d-flex m-2">
                                <div class="col-sm-2"><a href="{{$post->user->profile->slug}}/{{$post->slug}}"><img class="w-100 p-1" src="{{$post->image()}}"></a></div>
                                <div class="col-sm-9 p-1">
                                    <strong><a href="{{$post->user->profile->slug}}/{{$post->slug}}">{{$post->title}}</a></strong> posted by <span style="background-color: #b5b5b5;border-radius: 6px;padding: 1px 5px;"><a class="nametag" href="{{$post->user->profile->slug}}">{{$post->user->name}}</a></span>
                                    <p class="mt-1">{!! Str::words($post->content, 50, '...') !!}</p>
                                </div>
                            </div>
                        </div>
                        
                    @endforeach

                    <div class="col d-flex justify-content-center mb-5 mt-3 pb-3">
                        <div class="row list10">{{ $posts->links('pagination::bootstrap-4') }}</div>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="mt-2">
                <x-search/>
            </div>            
        </div>

    </div>
</div>
@endsection
