@extends( 'layouts.app' )

@section( 'content' )
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<header class="card-header">
						<h1 class="h4 mb-0">Create New Blog Post</h1>
					</header>

					<form class="p-4" method="POST" action="{{ route( 'post.store' ) }}" enctype="multipart/form-data" 
						  aria-label="Create new blog post form" novalidate>
						@csrf

						<fieldset class="mb-4">
							<legend class="visually-hidden">Post Information</legend>
							
							<!-- Title -->
							<div class="form-group row mb-3">
								<label for="title" class="col-md-4 col-form-label">
									Post Title
									<span class="text-danger" aria-label="required">*</span>
								</label>
								<div class="col-md-8">
									<input id="title" 
										   type="text" 
										   class="form-control @error( 'title' ) is-invalid @enderror"
										   name="title" 
										   value="{{ old( 'title' ) }}" 
										   required 
										   aria-required="true"
										   aria-describedby="title-help @error('title') title-error @enderror"
										   autocomplete="title" 
										   autofocus>
									<div id="title-help" class="form-text">Enter a descriptive title for your blog post</div>
									@error( 'title' )
										<div id="title-error" class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</div>
									@enderror
								</div>
							</div>

							<!-- Content -->
							<div class="form-group row mb-3">
								<label for="content" class="col-md-4 col-form-label">
									Content
									<span class="text-danger" aria-label="required">*</span>
								</label>
								<div class="col-md-8">
									<textarea id="content" 
											  rows="10" 
											  class="form-control @error( 'content' ) is-invalid @enderror"
											  name="content" 
											  required
											  aria-required="true"
											  aria-describedby="content-help @error('content') content-error @enderror"
											  autocomplete="off">{{ old( 'content' ) }}</textarea>
									<div id="content-help" class="form-text">Write the main content of your blog post</div>
									@error( 'content' )
										<div id="content-error" class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</div>
									@enderror
								</div>
							</div>
						</fieldset>

						<fieldset class="mb-4">
							<legend class="visually-hidden">Optional Media and Tags</legend>
							
							<!-- Image -->
							<div class="form-group row mb-3">
								<label for="image" class="col-md-4 col-form-label">
									Featured Image
									<span class="text-muted">(optional)</span>
								</label>
								<div class="col-md-8">
									<input type="file" 
										   class="form-control @error( 'image' ) is-invalid @enderror" 
										   id="image" 
										   name="image"
										   accept="image/*"
										   aria-describedby="image-help @error('image') image-error @enderror">
									<div id="image-help" class="form-text">Upload a featured image for your post (JPG, PNG, GIF)</div>
									@error( 'image' )
										<div id="image-error" class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</div>
									@enderror
								</div>
							</div>

							<!-- Tags -->
							<div class="form-group row mb-3">
								<label for="tags" class="col-md-4 col-form-label">
									Tags
									<span class="text-muted">(optional)</span>
								</label>
								<div class="col-md-8">
									<input id="tags" 
										   type="text" 
										   pattern="^[^,]+(?:,[^,]+){0,4}$"
										   class="form-control @error( 'tags' ) is-invalid @enderror" 
										   name="tags"
										   placeholder="web development, laravel, php"
										   value="{{ old( 'tags' ) }}" 
										   aria-describedby="tags-help @error('tags') tags-error @enderror"
										   autocomplete="off">
									<div id="tags-help" class="form-text">Separate tags with commas (maximum 5 tags)</div>
									@error( 'tags' )
										<div id="tags-error" class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</div>
									@enderror
								</div>
							</div>
						</fieldset>

						<div class="d-grid gap-2 d-md-flex justify-content-md-end">
							<button type="submit" class="btn btn-primary btn-lg">
								Publish Post
							</button>
						</div>

					</form>

				</div>
			</div>
		</div>
	</div>
@endsection