{{--
    Reusable Post List Component
    
    @param Collection $posts - Collection of posts to display
    @param int $wordLimit - Number of words to show in excerpt (default: 50)
    @param bool $showPagination - Whether to show pagination links (default: false)
    @param string $paginationView - Pagination view to use (default: 'pagination::bootstrap-4')
--}}

@props([
    'posts',
    'wordLimit' => 50,
    'showPagination' => false,
    'paginationView' => 'pagination::bootstrap-4'
])

@if($posts && $posts->count() > 0)
    @foreach($posts as $post)
        <div class="row">
            <div class="d-flex m-2">
                <div class="col-sm-2">
                    <p>
                        <a href="{{ url($post->user->profile->slug . '/' . $post->slug) }}">
                            <img class="w-100 p-1" src="{{ $post->image() }}" alt="{{ $post->title }}">
                        </a>
                    </p>
                </div>
                <div class="col-sm-10 p-1">
                    <p class="mt-1" style="padding-right: 15px;">
                        <strong>
                            <a href="{{ url($post->user->profile->slug . '/' . $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </strong>
                        posted by 
                        <span style="background-color: #b5b5b5;border-radius: 6px;padding: 1px 5px;">
                            <a class="profile-link" href="{{ url($post->user->profile->slug) }}">
                                {{ $post->user->name }}
                            </a>
                        </span>
                    </p>
                    <p class="mt-1" style="padding-right: 15px;">
                        {{ Str::words($post->content, $wordLimit, '...') }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    @if($showPagination && method_exists($posts, 'links'))
        <div class="col d-flex justify-content-center mb-5 mt-3 pb-3">
            <div class="row list10">
                {{ method_exists($posts, 'withQueryString') ? $posts->withQueryString()->links($paginationView) : $posts->links($paginationView) }}
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
