@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <div style="background-color: #ffffffa6;">
                <div class="p-2 d-flex" class="card-header"><span class="m-2">Posts by </span><h5 class="mt-2">{{$profile->user->name}}</h5></div>

                <div style="background-color: #ffffffa6;">

                    @foreach($posts as $post)
                    <div class="row">
                        <div class="d-flex m-2">
                            <div class="col-sm-2"><p><a href="{{$post->user->profile->slug}}/{{$post->slug}}"><img class="w-100 p-1" src="{{$post->image()}}"></a></p></div>
                            <div class="col-sm-10 p-1">
                                <p class="mt-1" style="padding-right: 15px;"><strong><a href="{{$post->user->profile->slug}}/{{$post->slug}}">{{$post->title}}</a></strong> posted by <span style="background-color: #b5b5b5;border-radius: 6px;padding: 1px 5px;"><a class="nametag" href="{{$post->user->profile->slug}}">{{$post->user->name}}</a></span></p>
                                <p class="mt-1" style="padding-right: 15px;">{!! Str::words($post->content, 50, '...') !!}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="col d-flex justify-content-center mb-5">
                        <div class="row list10">{{ $posts->links('pagination::bootstrap-4') }}</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
                <div class="card">

                    <div class="card-header">About the Author:</div>

                    <div class="card-body">
                        {{$profile->description}}
                        @if(!isset($profile->description))
                            {{$profile->user->name}} has not written a debut text yet
                        @endif
                    </div>
                    <p class="text-center">
                        <img class="w-50 rounded-circle" src="{{$profile->image()}}">
                    </p>

                    <p class="text-center">
                        <strong>{{$profile->user->name}}</strong>
                        <br>
                        Posts: {{$profile->user->posts->count()}}
                    </p>

                </div>

                <div class="mt-2">
                    <x-search/>
                </div>

            </div>

        

    </div>
</div>
@endsection
