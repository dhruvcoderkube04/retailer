@extends('layouts.email-base')

@section('content')
    <h2>New Retailer Store Request</h2>

    <p><strong>Name:</strong> {{ $user->firstname }} {{ @$user->lastname }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Company:</strong> {{ $user->userDetail->company_name ?? 'N/A' }}</p>
    <p><strong>Requested Subdomain:</strong> {{ $subdomain }}.trendmart.in</p>
    <p><strong>Requested At:</strong> {{ now() }}</p>
@endsection
