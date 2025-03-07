{{-- DOWNLOADABLES --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-8 mb-3">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            @if (Str::contains($item, '.docx'))
                <div class="">
                    <a class="group relative inline-block text-sm font-medium text-indigo-600 focus:ring-3 focus:outline-hidden"
                        href="{{ $item }}">
                        <span
                            class="absolute inset-0 translate-x-0 translate-y-0 bg-indigo-600 transition-transform group-hover:translate-x-0.5 group-hover:translate-y-0.5"></span>

                        <span class="relative block border border-current bg-white px-8 py-3"> Download File </span>
                    </a>
                </div>
            @endif
        @endif
    @endforeach
</div>

{{-- WATCHABLES --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8 mb-3">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            @if (Str::contains($item, '.mp4'))
                <div class="">
                    <video
                        style="
                    width: 420px !important;
                    height: 350px !important;
                "
                        controls>
                        <source src='{{ $item }}' type=''>
                        Your browser does not support the video tag.
                    </video>
                </div>
            @elseif (Str::contains($item, '.docx'))
            @endif
        @endif
    @endforeach
</div>

{{-- IMAGES --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-8 mb-3">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            @if (Str::contains($item, '.mp4'))
            @elseif (Str::contains($item, '.docx'))
            @else
                <div class="">
                    <img src='{{ $item }}' target='_blank'
                        style="
                    width: 420px !important;
                    height: 350px !important;
                " />
                </div>
            @endif
        @endif
    @endforeach
</div>
