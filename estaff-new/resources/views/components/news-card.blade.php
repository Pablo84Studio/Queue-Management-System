<div class="bg-white rounded shadow hover:shadow-lg transition">
    <img src="{{ asset('storage/' . $news->image) }}"
         class="w-full h-40 object-cover rounded-t">

    <div class="p-4">
        <h3 class="font-semibold text-lg mb-2">
            {{ $news->title }}
        </h3>

        <p class="text-sm text-gray-600 line-clamp-3">
            {{ $news->content }}
        </p>

        <div class="mt-3 text-xs text-gray-500">
            {{ $news->published_at->format('M d, Y') }}
        </div>
    </div>
</div>
