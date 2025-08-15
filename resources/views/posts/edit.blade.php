@extends( 'layouts.app' )

@section( 'content' )
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">Edit blog post</div>

					<form class="p-4" method="POST" action="/post/update/{{$post->id}}" enctype="multipart/form-data">
						@csrf
						@method('PATCH')

						<!-- Title -->
						<div class="form-group row">
							<label for="title" class="col-md-4 col-form-label"><span>Post Title</span> <span
									class="required"></span></label>

							<input id="title" type="text" class="form-control @error( 'title' ) is-invalid @enderror"
								name="title" value="{{ old( 'title', $post->title ) }}" required autocomplete="title" autofocus>

							@error( 'title' )
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror

						</div>

						<!-- content -->
						<div class="form-group row">
							<label for="content" class="col-md-4 col-form-label"><span>Content</span></label>

							<textarea id="content" rows="10" class="form-control @error( 'content' ) is-invalid @enderror"
								name="content" autocomplete="content" autofocus>{{ old( 'content', $post->content ) }}</textarea>
							@error( 'content' )
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<!-- image -->
						<div class="row">
							<label for="image" class="col-md-4 col-form-label"><span>Image</span> <span
									class="optional">(optional)</span></label>
							<input type="file" class="form-control-file mb-4" id="image" name="image">
							
							@if($post->image)
								<div class="mb-2">
									<small class="text-muted">Current image:</small><br>
									<img src="{{$post->image()}}" alt="Current post image" style="max-width: 200px; max-height: 150px;">
								</div>
							@endif

							@error( 'image' )
								<strong>{{ $message }}</strong>
							@enderror

						</div>

						<!-- Tags -->
						<div class="form-group row">
							<label for="tags" class="col-md-4 col-form-label"><span>Tags</span> <span
									style="font-size: x-small;"> (separate by comma)<span> <span
											class="optional">(optional)</span></label>

							<input id="tags" type="text" pattern="^[^,]+(?:,[^,]+){0,4}$"
								class="form-control @error( 'tags' ) is-invalid @enderror" name="tags"
								placeholder="input example, for, tags" value="{{ old( 'tags', $post->tags->pluck('title')->implode(', ') ) }}" autocomplete="tags"
								autofocus>

							@error( 'tags' )
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror

						</div>

						<div class="submit-button mt-3">
							<button class="btn btn-primary">Update Post</button>
							<a href="/{{$post->user->profile->slug}}/{{$post->slug}}" class="btn btn-secondary ml-2 text-light">Cancel</a>
						</div>

					</form>

				</div>
			</div>
		</div>
	</div>
@endsection
