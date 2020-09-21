@component('mail::message')
<div align="center" style="background: #000; padding: 40px">
    <img src="{{ asset('images/logo.png') }}" align="center" width="180">
</div>
<br>
<br>

# Hi {{ $user->first_name }},

Your {{ config('app.name') }} account has been successfully activated. Visit your dashboard to enjoy all our services.

@component('mail::button', ['url' => $url])
Go to Dashboard
@endcomponent

Best Regards,<br>
{{ config('app.name') }}
@endcomponent
