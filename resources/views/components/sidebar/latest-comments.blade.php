<section class="card mt-2" aria-labelledby="latest-comments-heading">
    <header class="card-header">
        <h3 id="latest-comments-heading" class="h6 mb-0">Latest Comments</h3>
    </header>
    <div class="card-body">
        <ul class="list-unstyled mb-0" role="list">
            @forelse($comments as $comment)
                <li class="mb-2 border-bottom pb-1" role="listitem">
                    <article>
                        <header>
                            <time class="text-muted small d-block" datetime="{{ $comment->created_at->toISOString() }}">
                                {{ $comment->created_at->diffForHumans() }}
                            </time>
                        </header>
                        
                        <div class="comment-content mb-1">
                            @if($comment->post && $comment->post->user && $comment->post->user->profile)
                                <a href="/{{ $comment->post->user->profile->slug }}/{{ $comment->post->slug }}" 
                                   class="text-decoration-none"
                                   aria-label="Read full comment on post '{{ $comment->post->title }}' by {{ $comment->user->name }}">
                                    <div class="text-truncate">{{ Str::limit($comment->comment, 80) }}</div>
                                </a>
                            @else
                                <div class="text-truncate" aria-label="Comment content">{{ Str::limit($comment->comment, 80) }}</div>
                            @endif
                        </div>
                        
                        <footer class="small text-muted">
                            <span>by </span>
                            @if($comment->user && $comment->user->profile)
                                <a href="/{{ $comment->user->profile->slug }}" 
                                   class="text-decoration-none"
                                   aria-label="View profile of {{ $comment->user->name }}">
                                    <strong>{{ $comment->user->name }}</strong>
                                </a>
                            @else
                                <strong>Unknown User</strong>
                            @endif
                            <span> on </span>
                            @if($comment->post && $comment->post->user && $comment->post->user->profile)
                                <a href="/{{ $comment->post->user->profile->slug }}/{{ $comment->post->slug }}" 
                                   class="text-decoration-none"
                                   aria-label="Read post '{{ $comment->post->title }}'">
                                    <em>{{ $comment->post->title }}</em>
                                </a>
                            @else
                                <em>deleted post</em>
                            @endif
                        </footer>
                    </article>
                </li>
            @empty
                <li role="status">
                    <p class="text-muted mb-0">No comments found.</p>
                </li>
            @endforelse
        </ul>
    </div>
</section>
