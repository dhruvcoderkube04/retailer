@extends('layouts.email-base')

@section('style')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(190, 190, 190);
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .email-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            padding-bottom: 20px;
        }

        .email-body {
            text-align: center;
            font-size: 16px;
            color: #555;
            margin-top: 20px;
        }

        .password-reset {
            display: inline-block;
            background-color: black;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .password-reset:hover {
            background-color: black;
            /* Same as default state */
            color: white;
            /* Same as default state */
        }

        .email-footer {
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="email-header">Reset Password</div>
    <div class="email-container">
        <div class="email-body">
            <h3 style="text-align: left; padding-left:18px;">Hello,</h3>
            <p>You have requested to reset your password. Click the button below to reset your password:</p>
            <a href="{{ route('retailer.password.reset', ['token' => $token]) }}" class="password-reset">Reset Password</a>
            <p>If you did not request this, please ignore this email.</p>
        </div>
    </div>
@endsection
