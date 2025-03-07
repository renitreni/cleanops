<div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-8">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            <div class="">
                @if (Str::contains($item, '.mp4'))
                    <video width='420' height='240' controls>
                        <source src='{{ $item }}' type=''>
                        Your browser does not support the video tag.
                    </video>
                @elseif (Str::contains($item, '.docx'))
                    <a class="group relative inline-block text-sm font-medium text-indigo-600 focus:ring-3 focus:outline-hidden"
                        href="{{ $item }}">
                        <span
                            class="absolute inset-0 translate-x-0 translate-y-0 bg-indigo-600 transition-transform group-hover:translate-x-0.5 group-hover:translate-y-0.5"></span>

                        <span class="relative block border border-current bg-white px-8 py-3"> Download </span>
                    </a>
                @else
                    <img src='{{ $item }}' target='_blank' width='420' height='240' />
                @endif
            </div>
        @endif
    @endforeach
</div>
