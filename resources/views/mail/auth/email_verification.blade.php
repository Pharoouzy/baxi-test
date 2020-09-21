@component('mail::message')
<div align="center" style="background: #000; padding: 40px">
    <img src="{{ asset('images/logo.png') }}" align="center" width="180">
</div>
<br>
<br>

# Hi {{ $user->first_name }}

Thank you for taking your time to sign up with {{ config('app.name') }}! To get things going we need to verify that this is a valid email address. Use this verification code <strong>{{ $user->verification_code }}</strong>. Please click the button below to confirm your account.

@component('mail::button', ['url' => $url])
Confirm Account
@endcomponent

Best Regards,<br>
{{ config('app.name') }}
@endcomponent
