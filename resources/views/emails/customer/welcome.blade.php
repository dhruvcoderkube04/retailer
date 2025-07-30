@component('mail::message')
# Welcome {{ $name }}

Thanks for registering! Please verify your email address to activate your account.

@component('mail::button', ['url' => $verificationUrl])
Verify Email
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
