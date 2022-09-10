@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <div style="background-color: #ffffffa6;">
                <div class="p-2 d-flex" class="card-header"><span class="m-2">Edit profile:</span><h5 class="mt-2">{{$profile->user->name}}</h5></div>

                <div style="background-color: #ffffffa6;">

                   
                <form action="/profile/edit/{{ Auth::user()->profile->slug }}/save" enctype="multipart/form-data" method="post">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row">
                    <div class="col-8 offset-2">
                        


                        <!-- Name -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label">Name</label>
                        
                        <input id="title" 
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name') ?? $profile->user->name }}" required
                                autocomplete="title" autofocus>

                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        
                        </div>
                        
                        
                        <!-- description -->
                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label">Description</label>
                        
                        <input id="description" 
                                type="text"
                                class="form-control @error('description') is-invalid @enderror"
                                name="description"
                                value="{{ $profile->description }}" 
                                autocomplete="description" autofocus>

                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        
                        </div>
                        
                        
                        <!-- url-slug -->
                        <div class="form-group row">
                            <label for="slug" class="col-md-4 col-form-label">Profile Url</label>
                        
                        <input id="slug" 
                                type="text"
                                class="form-control @error('slug') is-invalid @enderror"
                                name="slug"
                                value="{{ old('slug') ?? $profile->slug }}" required
                                autocomplete="slug" autofocus>

                                            @error('slug')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        
                        </div>
                        
                        <!-- image -->
                        <div class="row">
                            <label for="image" class="col-md-4 col-form-label">Profile Image</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/png, image/jpeg, image/webp">
                            
                            @error('image')
                                    <strong>{{ $message }}</strong>
                            @enderror
                            
                        </div>
                        
                        <div class="row pt-4">
                            <button class="btn btn-success">Save Profile</button>
                        </div>
                        
                        

                        </div>
                    </div>
                </form>
                
                <div class="row" style="position:relative; margin-top:50px; text-align: right;">
                    <div class="col-8 offset-2">
                        @if($profile->user->id != 1)@endif
                        <form class="delete_form mb-4" method="POST" action="/profile/delete/{{$profile->id}}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                            <button class="btn btn-danger">Delete Profile</button>
                        </form>
                    </div>
                </div>
                    

                </div>
            </div>
        </div>

       
</div>
@endsection
