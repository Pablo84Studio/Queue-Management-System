@extends('layouts.app')

@section('content')

{{-- Slider --}}
<x-slider :sliders="$sliders" />

{{-- News Section --}}
<section class="max-w-7xl mx-auto px-6 py-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Latest News & Announcements</h2>
        <a href="/news" class="text-blue-600 font-medium">View all</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($news as $item)
            <h2>{{ $item->title }}</h2>
            <p>{{ $item->content }}</p>
        @endforeach
    </div>
</section>

{{-- Events Section --}}
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-6">Live Events</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if(isset($events) && $events->count())
                @foreach($events as $event)
                    <h3>{{ $event->title }}</h3>
                    <p>{{ $event->description }}</p>
                @endforeach
            @else
                <p class="text-gray-500">No events available</p>
            @endif

        </div>
    </div>
</section>

@endsection
