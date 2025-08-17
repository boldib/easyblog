<section class="card mt-2" aria-labelledby="latest-tags-heading">
    <header class="card-header">
        <h3 id="latest-tags-heading" class="h6 mb-0">Latest Tags</h3>
    </header>
    <div class="card-body">
        <ul class="list-unstyled mb-0" role="list">
            @forelse($tags as $tag)
                <li class="mb-1" role="listitem">
                    <a href="/tag/{{ $tag->slug }}" 
                       class="text-decoration-none" 
                       aria-label="View posts tagged with {{ $tag->title }}, created {{ $tag->created_at->diffForHumans() }}">
                        <span class="badge bg-secondary me-1">{{ $tag->title }}</span>
                    </a>
                    <time class="text-muted small" datetime="{{ $tag->created_at->toISOString() }}">
                        {{ $tag->created_at->diffForHumans() }}
                    </time>
                </li>
            @empty
                <li role="status">
                    <p class="text-muted mb-0">No tags found.</p>
                </li>
            @endforelse
        </ul>
    </div>
</section>
