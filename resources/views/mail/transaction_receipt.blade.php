@component('mail::message')
<div align="center" style="background: #000; padding: 40px">
    <img src="{{ asset('images/logo.png') }}" align="center" width="180">
</div>
<br>
<br>

# Hi {{ $user->first_name.' '.$backoffice }},

@if($type == 'electricity')

Transaction Successful.

# Transaction Information:
| | |
|-|-|
| **Item Name:** | {{$data->item_name}} |
| **Weight:** | {{$data->weight}} |

@component('mail::button', ['url' => config('main_url')])
    Login to Dashboard
@endcomponent

@else

Transaction Successful.

# Transaction Information:
| | |
|-|-|
| **Item Name:** | {{$data->item_name}} |
| **Weight:** | {{$data->weight}} |
| **Payment Amount:** | {{$data->price}} |

@component('mail::button', ['url' => config('main_url')])
    Login to Dashboard
@endcomponent

@endif
Thanks,<br>
{{ config('app.name') }}
@endcomponent