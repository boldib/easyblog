@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <div style="background-color: #ffffffa6;">
                <div class="p-2" class="card-header"><h5 class="mt-2">Latest Posts</h5></div>

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

            <div class="mb-2">
                <x-search/>
            </div>

            <div class="card mt-2">
                <div class="card-body">
                    {{$allposts}} posts in the database
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-header">New users:</div>

                <div class="card-body">
                    @foreach($users as $user)
                        <a href="/{{$user->profile->slug}}"><img class="rounded-circle m-1" width="20px" height="20px" src="{{$user->profile->image()}}"> {{$user->name}}</a><br>
                    @endforeach
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-header">Latest Tags:</div>

                <div class="card-body">
                    @foreach($tags as $tag)
                        <a href="/tag/{{$tag->slug}}">{{$tag->title}}</a><br>
                    @endforeach
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-header">Latest Comments:</div>

                <div class="card-body">
                    @foreach($comments as $comment)
                        <a href="/{{$comment->post->user->profile->slug}}/{{$comment->post->slug}}">{{$comment->comment}}</a><br>
                    @endforeach
                </div>
            </div>

            
        </div>

    </div>
</div>
@endsection