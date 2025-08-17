<x-content-with-sidebar title="Tag: {{ $tag->title }}" sidebar-component="search">
	<x-post-list :posts="$posts" :show-pagination="true" />
</x-content-with-sidebar>