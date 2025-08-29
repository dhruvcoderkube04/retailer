<!DOCTYPE html>
<html>

<head>
    <title>New Contact Enquiry</title>
</head>

<body>
    <h2>New Enquiry Received</h2>
    <p><strong>Name:</strong> {{ $enquiry->firstname }} {{ $enquiry->lastname }}</p>
    <p><strong>Email:</strong> {{ $enquiry->email }}</p>
    <p><strong>Phone:</strong> {{ $enquiry->phone_number ?? 'N/A' }}</p>
    <p><strong>Subject:</strong> {{ $enquiry->subject }}</p>
    <p><strong>Message:</strong></p>
    <p>{{ $enquiry->message }}</p>
</body>

</html>
