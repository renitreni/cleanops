<div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-8">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            <div class="">
                @if (Str::contains($item, '.mp4'))
                    <video width='420' height='240' controls>
                        <source src='{{ $item }}' type=''>
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src='{{ $item }}' target='_blank' width='420' height='240'/>
                @endif
            </div>
        @endif
    @endforeach
</div>
