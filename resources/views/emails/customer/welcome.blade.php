@component('mail::message')
# Welcome, {{ $name }} 👋

Thank you for registering with **{{ config('app.name') }}**!

Your account has been successfully created. Below are your login credentials:

---

**Email:** {{ $email }}  
**Password:** {{ $randomPassword }}

> We recommend changing your password after your first login for security purposes.

{{-- Uncomment below if you add email verification --}}
{{-- 
@component('mail::button', ['url' => $verificationUrl])
Verify Email Address
@endcomponent 
--}}

If you didn't request this account, please ignore this email.

Thanks again for joining us!  
**– The {{ config('app.name') }} Team**

@endcomponent
