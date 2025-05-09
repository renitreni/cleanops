<div>
    @php
        $diff = '-';
        if ($getRecord()->resolved_at) {
            $date1 = \Carbon\Carbon::parse($getRecord()->pending_at);
            $date2 = \Carbon\Carbon::parse($getRecord()->resolved_at);

            $diff = $date1->diff($date2);
        }
        echo $diff;
    @endphp
</div>
