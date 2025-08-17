<section class="card mt-2" aria-labelledby="new-users-heading">
    <header class="card-header">
        <h3 id="new-users-heading" class="h6 mb-0">New users</h3>
    </header>
    <div class="card-body">
        @forelse($users as $user)
            <article class="mb-1" role="listitem">
                <time class="text-muted small d-block" datetime="{{ $user->created_at->toISOString() }}">
                    {{ $user->created_at->diffForHumans() }}
                </time>
                <a href="/{{ $user->profile?->slug }}" 
                   class="text-decoration-none" 
                   aria-label="View profile of {{ $user->name }}, joined {{ $user->created_at->diffForHumans() }}">
                    <strong>{{ $user->name }}</strong>
                </a>
            </article>
        @empty
            <p class="text-muted" role="status">No users found.</p>
        @endforelse
    </div>
</section>
