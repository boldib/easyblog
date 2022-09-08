@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create new post</div>

                <form class="p-4" method="POST" action="{{ route('post.store') }}" enctype="multipart/form-data">
                    @csrf
                
                    <!-- Title -->
                    <div class="form-group row">
                        <label for="title" class="col-md-4 col-form-label"><span>Post Title</span> <span class="required"></span></label>
                    
                    <input id="title" 
                            type="text"
                            class="form-control @error('title') is-invalid @enderror"
                            name="title"
                            value="{{ old('title') }}" required
                            autocomplete="title" autofocus>

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                    
                    </div>

                    <!-- content -->
                    <div class="form-group row">
                        <label for="content" class="col-md-4 col-form-label"><span>Content</span></label>
                    
                    <textarea id="content" 
                            rows="10"
                            class="form-control @error('content') is-invalid @enderror"
                            name="content"
                            value="{{ old('content') }}" 
                            autocomplete="content" autofocus></textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror        
                    </div>

                    <!-- image -->
                    <div class="row">
                        <label for="image" class="col-md-4 col-form-label"><span>Image</span> <span class="optional">(optional)</span></label>
                        <input type="file" class="form-control-file mb-4" id="image" name="image">
                        
                        @error('image')
                                <strong>{{ $message }}</strong>
                        @enderror
                        
                    </div>

                    <button  class="button btn-primary">Submit</button>
                    
                </form>
                
            </div>
        </div>
    </div>
</div>
@endsection
