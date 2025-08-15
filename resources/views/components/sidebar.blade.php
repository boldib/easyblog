<div class="mb-2">
    <x-search />
</div>

<div class="card mt-2">
    <div class="card-body">
        {{ App\Services\InfolistService::postscount() }} posts in the database
    </div>
</div>

<div class="card mt-2">
    <div class="card-header">New users:</div>

    <div class="card-body">
        {{ App\Services\InfolistService::get('users', 15) }}
    </div>
</div>

<div class="card mt-2">
    <div class="card-header">Latest Comments:</div>

    <div class="card-body">
        {{ App\Services\InfolistService::get('comments', 10) }}
    </div>
</div>

<div class="card mt-2">
    <div class="card-header">Latest Tags:</div>

    <div class="card-body">
        {{ App\Services\InfolistService::get('tags', 10) }}
    </div>
</div>