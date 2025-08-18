<section class="card" aria-labelledby="search-heading">
    <div class="card-body">
        <h3 id="search-heading" class="visually-hidden">Search Posts</h3>
        <form action="{{route('search')}}" role="search" aria-label="Search blog posts">
            <div class="d-flex">
                <label for="search-input" class="visually-hidden">Search for posts</label>
                <input 
                    id="search-input"
                    class="form-control me-2" 
                    type="search" 
                    placeholder="Search posts..." 
                    name="s" 
                    aria-label="Search for posts"
                    aria-describedby="search-help"
                >
                <button 
                    class="btn btn-outline-secondary" 
                    type="submit"
                    aria-label="Submit search"
                >
                    <span aria-hidden="true">🔎</span>
                    <span class="visually-hidden">Search</span>
                </button>
            </div>
            <div id="search-help" class="visually-hidden">
                Enter keywords to search for blog posts
            </div>
        </form>
    </div>
</section>