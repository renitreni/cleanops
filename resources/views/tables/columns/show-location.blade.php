<div>
    @php
        $data = json_decode($getRecord()->location, true);
        $googleMapsUrl = "https://www.google.com/maps?q={$data['lat']},{$data['lng']}";
    @endphp
    <a class="inline-block rounded-sm bg-indigo-600 px-8 py-3 text-sm font-medium text-white transition hover:scale-110 hover:shadow-xl focus:ring-3 focus:outline-hidden"
        href="{{ $googleMapsUrl }}">
        Show Location
    </a>
</div>
