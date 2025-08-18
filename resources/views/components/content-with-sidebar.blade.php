{{--
    Reusable Content with Sidebar Layout Component
    
    @param string $title - The title to display in the header
    @param string $sidebarComponent - The sidebar component to display (default: 'sidebar')
    @param bool $showSidebarWrapper - Whether to wrap sidebar in mt-2 div (default: true)
--}}

@props([
    'title',
    'sidebarComponent' => 'sidebar',
    'showSidebarWrapper' => true
])

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <main class="col-md-8" role="main" aria-labelledby="main-heading">
                <section class="bg-white card" aria-labelledby="main-heading">
                    <header class="card-header p-3">
                        <h1 id="main-heading" class="h4 mb-0">{{ $title }}</h1>
                    </header>

                    <div class="card-body" role="region" aria-label="Main content">
                        {{ $slot }}
                    </div>
                </section>
            </main>

            <aside class="col-4 d-none d-md-block" role="complementary" aria-label="Sidebar">
                @if($showSidebarWrapper)
                    <div class="mt-2">
                        <x-dynamic-component :component="$sidebarComponent" />
                    </div>
                @else
                    <x-dynamic-component :component="$sidebarComponent" />
                @endif
            </aside>
        </div>
    </div>
@endsection
