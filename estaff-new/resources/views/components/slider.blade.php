@if($sliders->count())
    <div 
        x-data="{ current: 0, total: {{ $sliders->count() }} }"
        class="relative overflow-hidden"
    >
        <div class="flex transition-transform duration-700"
             :style="`transform: translateX(-${current * 100}%)`">

            @foreach($sliders as $slide)
                <div class="min-w-full h-[400px] relative">
                    <img src="{{ asset('storage/' . $slide->image) }}"
                         class="w-full h-full object-cover">

                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <h2 class="text-white text-3xl font-bold">
                            {{ $slide->caption }}
                        </h2>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Controls --}}
        <button
            @click="current = (current - 1 + total) % total"
            class="absolute left-4 top-1/2 bg-white px-3 py-1 rounded shadow"
        >
            ‹
        </button>

        <button
            @click="current = (current + 1) % total"
            class="absolute right-4 top-1/2 bg-white px-3 py-1 rounded shadow"
        >
            ›
        </button>
    </div>
@else
    <p class="text-center text-gray-500 py-10">
        No sliders available
    </p>
@endif
