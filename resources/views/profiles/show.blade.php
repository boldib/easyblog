@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <div class="bg-white">
                <div class="p-2 d-flex" class="card-header"><span class="m-2">Posts by </span><h5 class="mt-2">{{$profile->user->name}}</h5></div>

                <div>
                    <x-post-list :posts="$posts" :show-pagination="true" />
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
</div>
@endsection
