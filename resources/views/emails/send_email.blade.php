@component('mail::message')
    # Body :

    From : {{ $startDate }} To {{ $endDate }}

    Thank you,<br>
    {{ config('app.name') }}
@endcomponent
