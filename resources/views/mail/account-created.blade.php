<x-mail::message>

    # {{ __('account-created.subject', ['app' => config('app.name')]) }} 🎉

    {{ __('account-created.greeting', ['name' => $user->name]) }}

    {{ __('account-created.body', ['app' => config('app.name')]) }}

    <x-mail::button :url="$user->link" color="primary">
        {{ __('account-created.button') }}
    </x-mail::button>

    {{ __('account-created.footer') }}

    {{ __('account-created.salutation') }}<br>
    <span style="color: #e91e63;">{{ config('app.name') }}</span>
</x-mail::message>
