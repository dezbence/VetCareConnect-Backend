<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Üdvözli a VetCare Connect csapata!')
@endif
@endif

{{-- Intro Lines --}}
Kérjük kattintson az alábbi gombra az e-mail cím megerősítéséhez!

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
E-mail megerősítése
</x-mail::button>
@endisset

{{-- Outro Lines --}}
Ha nem Ön hozta létre a fiókot, akkor nincs több teendője az email-el!

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Tisztelettel'),<br>
VetCare Connect
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "Ha az 'Email megerősítése' gombbal probléma adódna, másolja ki és illessze be az alábbi URL-t \n".
    'a böngészőjébe:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
