@extends('layouts.email-base')

@section('content')
    <h2>New Retailer Registration</h2>
    <p>A new Retailer has registered on the platform.</p>
    <ul>
        <li><strong>Name:</strong> {{ $user->firstname }} {{ $user->lastname }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Phone:</strong> {{ $user->phone_number ?? 'N/A' }}</li>
    </ul>
@endsection
