@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div style="background-color: #ffffffa6;">
            
                <div class="p-2" class="card-header">
                    <h5 class="mt-2">{{$post->title}}</h5>
                </div>

                <div style="background-color: #ffffffa6;">


                    <div class="row">
                        <div class="d-flex m-2">
                            <div class="mt-1 p-2 text-justify" style="padding: 10px 30px 10px 20px;">
                                <p><img class="p-4" style="float: left; max-width:350px; max-height:350px;" src="{{$post->image()}}"></p>
                                <p class="p-4">{{$post->content}}</p>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="tags col d-flex justify-content-center mb-5">

                    <span class="m-1">tags:</span>
                    @foreach($post->tags as $tag)
                    <a class="p-1" href="/tag/{{$tag->slug}}">{{$tag->title}}</a>
                    @endforeach

                </div>

            </div>

            <div class="p-3 bg-white">
                <div>
                    <form method="post" action="/comment/{{ $post->id }}">
                        @csrf
                        <div class="form-group">
                            <textarea style="resize: none;" rows="3" class="form-control" name="comment"></textarea>
                            <input type="hidden" name="post_id" value="{{ $post->id }}" />
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" class="btn btn-secondary" value="Add Comment" />
                        </div>
                    </form>
                </div>
            </div>

            <div class="comments mt-2">

                <p class="bg-light p-2 mt-3">Comments:</p>

                @foreach($post->comments->sortByDesc('id'); as $comment)
                <p class="bg-light pt-2 pb-2"><span class="bg-warning p-2">{{$comment->created_at->format('d-m-Y H:m')}}</span><span class="bg-secondary p-2"><a class="nametag" href="/{{$comment->user->profile->slug}}">{{$comment->user->name}}</a></span><span class="bg-light p-2">{{$comment->comment}}</p>
                @endforeach

            </div>

        </div>


        <div class="col-4 d-none d-md-block">
            <div class="card">

                <div class="card-header p-2">About the Author:</div>

                <div class="text-center">
                    <p class="mt-4"><img class="w-50 rounded-circle" src="{{$post->user->profile->image()}}"></p>
                    <p class="pb-4"><strong><a href="/{{$post->user->profile->slug}}">{{$post->user->name}}</a></strong><br>
                        <strong>Posts: </strong>{{$post->user->posts->count()}}
                    </p>
                    <p>This article was posted on {{$post->created_at->format('d-m-Y')}}</p>
                </div>
            </div>

            @if(Auth::id() == $post->user->id)
            <div class="card mt-2">
                <div class="card-header p-2">Manage post:</div>
                    <div class="card-body">
                            <form action="/post/delete/{{$post->id}}" method="post">
                            @csrf
                            @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                    </div>
            </div>
            @endif

            <div class="mt-2">
                <x-search />
            </div>

        </div>

    </div>



</div>
</div>
@endsection