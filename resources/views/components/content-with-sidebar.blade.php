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
            <div class="col-md-8">
                <div class="bg-white">
                    <div class="p-2 card-header">
                        <h5 class="mt-2">{{ $title }}</h5>
                    </div>

                    <div>
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <div class="col-4 d-none d-md-block">
                @if($showSidebarWrapper)
                    <div class="mt-2">
                        <x-dynamic-component :component="$sidebarComponent" />
                    </div>
                @else
                    <x-dynamic-component :component="$sidebarComponent" />
                @endif
            </div>
        </div>
    </div>
@endsection
