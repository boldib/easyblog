{{--
    Reusable Post List Component
    
    @param Collection $posts - Collection of posts to display
    @param int $wordLimit - Number of words to show in excerpt (default: 50)
    @param bool $showPagination - Whether to show pagination links (default: false)
    @param string $paginationView - Pagination view to use (default: 'pagination::bootstrap-4')
--}}

@props( [ 
	'posts',
	'wordLimit' => 50,
	'showPagination' => false,
	'paginationView' => 'pagination::bootstrap-4'
] )

@if( $posts && $posts->count() > 0 )
	<section aria-label="Blog posts listing">
		@foreach( $posts as $post )
			<article class="post-item border-bottom pb-3 mb-3" role="article">
				<div class="row">
					<div class="col-sm-3 col-md-2">
						<figure class="post-image mb-0">
							<a href="{{ url( $post->user->profile->slug . '/' . $post->slug ) }}" 
							   aria-label="Read full post: {{ $post->title }}">
								<img class="img-fluid rounded" 
									 src="{{ $post->image() }}" 
									 alt="Featured image for {{ $post->title }}"
									 loading="lazy">
							</a>
						</figure>
					</div>
					<div class="col-sm-9 col-md-10">
						<header class="post-header">
							<h2 class="post-title h5 mb-2">
								<a class="text-black text-decoration-none" href="{{ url( $post->user->profile->slug . '/' . $post->slug ) }}" 
								   class="text-decoration-none">
									{{ $post->title }}
								</a>
							</h2>
							<div class="post-meta text-muted small mb-2" role="group" aria-label="Post metadata">
								<span>Posted by</span>
								<a href="{{ url( $post->user->profile->slug ) }}" 
								   class="text-white badge bg-secondary text-decoration-none ms-1"
								   aria-label="View profile of {{ $post->user->name }}">
									{{ $post->user->name }}
								</a>
								@if( $post->created_at )
									<time datetime="{{ $post->created_at->toISOString() }}" 
										  class="ms-2"
										  title="{{ $post->created_at->format( 'F j, Y \a\t g:i A' ) }}">
										{{ $post->created_at->diffForHumans() }}
									</time>
								@endif
							</div>
						</header>
						<div class="post-excerpt">
							<p class="mb-0" aria-describedby="post-{{ $post->id }}-excerpt">
								<span id="post-{{ $post->id }}-excerpt">
									{{ Str::words( $post->content, $wordLimit, '...' ) }}
								</span>
							</p>
						</div>
					</div>
				</div>
			</article>
		@endforeach
	</section>

	@if( $showPagination && method_exists( $posts, 'links' ) )
		<div class="col d-flex justify-content-center mb-5 mt-3 pb-3">
			<div class="row list10">
				{{ method_exists( $posts, 'withQueryString' ) ? $posts->withQueryString()->links( $paginationView ) : $posts->links( $paginationView ) }}
			</div>
		</div>
	@endif
@else
    <div class="row">
        <div class="col-12 text-center py-4">
            <p class="text-muted">No posts found.</p>
        </div>
    </div>
@endif
