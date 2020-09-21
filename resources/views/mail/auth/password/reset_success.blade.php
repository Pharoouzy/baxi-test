@component('mail::message')
<div align="center" style="background: #000; padding: 40px">
    <img src="{{ asset('images/logo.png') }}" align="center" width="180">
</div>
<br>
<br>

# Hi {{ $user->first_name }},

Your password has been reset successfully. Kindly proceed to your dashboard.

@component('mail::button', ['url' => config('app.main_url').'/login'])
    Login to Dashboard
@endcomponent

If you did not request a password reset, please ignore this email or reply to let us know.<br>


Thanks,<br>
{{ config('app.name') }}
___
###### If you received this email but didn't register for {{ config('app.name') }} Account, something's gone haywire. Click [here]({{ config('app.main_url') }}) to de-activate and close this account.
@endcomponent