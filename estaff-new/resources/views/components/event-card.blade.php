<div class="bg-white rounded shadow p-4 hover:shadow-lg transition">
    <h3 class="font-semibold text-lg mb-1">{{ $event->title }}</h3>

    <p class="text-sm text-gray-600 mb-2">
        {{ $event->description }}
    </p>

    <div class="text-sm text-gray-500">
        📍 {{ $event->location }}<br>
        🗓 {{ $event->start_date->format('M d, Y') }}
    </div>
</div>
