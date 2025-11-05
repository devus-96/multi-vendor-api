<x-mail::message>

    # {{ __('password-reset.subject', ['app' => config('app.name')]) }}

    {{ __('password-reset.greeting', ['name' => $user->firstname]) }}

    {{ __('password-reset.body') }}

    @php
        $url = $user->link;
    @endphp

    <x-mail::button :url="$url" color="primary">
        {{ __('password-reset.button') }}
    </x-mail::button>

    {{ __('password-reset.footer') }}

    {{ __('password-reset.salutation') }}<br>
    <span style="color: #e91e63;">{{ config('app.name') }}</span>

</x-mail::message>
