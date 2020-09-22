@component('mail::message')
<div align="center" style="background: #000; padding: 40px">
    <img src="{{ asset('images/logo.png') }}" align="center" width="180">
</div>
<br>
<br>

# Hi {{ $user->first_name }},

@if($data->type)

@if($data->transaction_status === 'pending')
Your payment for Eko Electricity {{ $data->type }} is pending.
@else
Your payment for Eko Electricity {{ $data->type }} was Successful.
@endif


# Transaction Information:
| | |
|-|-|
| **Amount:** | #{{number_format($data->token_amount, 2)}} |
| **Reference:** | {{$data->reference}} |
| **Token:** | {{$data->token_code ?? 'N/A'}} |
| **Meter Number:** | {{$data->meter_number}} |

@if($data->transaction_status === 'pending')

@component('mail::button', ['url' => config('app.main_url').'/transactions/requery/'.$data->reference])
Requery Transaction
@endcomponent

@else

@component('mail::button', ['url' => config('app.main_url').'/transactions/'.$data->reference])
View Transaction
@endcomponent

@endif

@else

Your payment for DSTV subscription was Successful.

# Transaction Information:
| | |
|-|-|
| **Amount:** | #{{number_format($data->amount, 2)}} |
| **Reference:** | {{$data->reference}} |
| **Month:** | {{$data->product_months_paid_for}} Months |
| **Smartcard Number:** | {{$data->smartcard_number}} |

@if($data->transaction_status === 'pending')

@component('mail::button', ['url' => config('main_url').'/transactions/requery/'.$data->reference])
Requery Transaction
@endcomponent

@else

@component('mail::button', ['url' => config('main_url').'/transactions/'.$data->reference])
View Transaction
@endcomponent

@endif


@endif
Thanks,<br>
{{ config('app.name') }}
@endcomponent