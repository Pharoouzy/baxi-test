@component('mail::message')
<div align="center" style="background: #000; padding: 40px">
    <img src="{{ asset('images/logo.png') }}" align="center" width="180">
</div>
<br>
<br>

# Hi {{ $data['user']['first_name'] }},

You recently requested to reset your password for your {{ config('app.name') }} account. Click the button below to reset it.

@component('mail::button', ['url' => $url])
    Reset your password
@endcomponent

If you did not request a password reset, please ignore this email or reply to let us know. The password reset is only valid for the next 30 minutes.<br>


Thanks,<br>
{{ config('app.name') }}
___
###### If you are having trouble clicking the password reset button, copy and paste the URL below into your browser.

###### [{{ $url }}]({{ $url }})
@endcomponent