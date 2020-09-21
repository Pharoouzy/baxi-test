@component('mail::message')
<div align="center" style="background: #000;">
    <img src="{{ asset('images/logo.png') }}" align="center" width="100">
</div>
<br>
<br>

# Hi {{ $user->first_name.' '.$backoffice }},

@if(!$backoffice)

New Transaction Request has been made.

# Transaction Request Information:
| | |
|-|-|
| **Item Name:** | {{$data->item_name}} |
| **Weight:** | {{$data->weight}} |
<br>

#### Pickup Details:
| | |
|-|-|
| **Name:** | {{$data->pickup_details->name}} |
| **Phone Number 1:** | {{$data->pickup_details->first_contact_number}} |
@if($data->pickup_details->second_contact_number)
| **Phone Number 2:** | {{$data->pickup_details->second_contact_number}} |
@endif
| **Address:** | {{$data->pickup_details->address}} |
| **Landmark:** | {{$data->pickup_details->landmark}} |

#### Delivery Details:
@foreach($data->delivery_details as $key => $delivery_detail)

| | |
|-|-|
{{--| #{{ ++$key }}. | |--}}
| **Name:** | {{$delivery_detail->name}} |
| **Phone Number 1:** | {{$delivery_detail->first_contact_number}} |
@if($delivery_detail->second_contact_number)
| **Phone Number 2:** | {{$delivery_detail->second_contact_number}} |
@endif
| **Address:** | {{$delivery_detail->address}} |
| **Landmark:** | {{$delivery_detail->landmark}} |

<br>
@endforeach
{{--<h2>Code: {{$data->verification_code}}</h2>--}}

@component('mail::button', ['url' => env('MAIN_APP_URL')])
    Login to Dashboard
@endcomponent

@else

A new Request has been made on your behalf.

# Transaction Request Information:
| | |
|-|-|
| **Item Name:** | {{$data->item_name}} |
| **Weight:** | {{$data->weight}} |
| **Payment Amount:** | {{$data->price}} |
<br>

#### Pickup Details:
| | |
|-|-|
| **Name:** | {{$data->pickup_details->name}} |
| **Phone Number 1:** | {{$data->pickup_details->first_contact_number}} |
@if($data->pickup_details->second_contact_number)
    | **Phone Number 2:** | {{$data->pickup_details->second_contact_number}} |
@endif
| **Address:** | {{$data->pickup_details->address}} |
| **Landmark:** | {{$data->pickup_details->landmark}} |

#### Delivery Details:
@foreach($data->delivery_details as $key => $delivery_detail)

    | | |
    |-|-|
    | #{{ ++$key }}. | |
    | **Name:** | {{$delivery_detail->name}} |
    | **Phone Number 1:** | {{$delivery_detail->first_contact_number}} |
    @if($delivery_detail->second_contact_number)
        | **Phone Number 2:** | {{$delivery_detail->second_contact_number}} |
    @endif
    | **Address:** | {{$delivery_detail->address}} |
    | **Landmark:** | {{$delivery_detail->landmark}} |

    <br>
@endforeach
{{--<h2>Code: {{$data->verification_code}}</h2>--}}

@component('mail::button', ['url' => $data->transaction->authorization_url])
    Make Payment
@endcomponent
@endif
Thanks,<br>
{{ config('app.name') }}
@endcomponent