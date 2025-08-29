<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'TrendMart Notification' }}</title>
    @yield('style')
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0" width="600"
                    style="background-color: #ffffff; border-radius: 5px; padding: 30px; text-align: left;">
                    <!-- Header -->
                    <tr>
                        <td style="text-align: center;">
                            <h1 style="color: #2c3e50;">TrendMart</h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td>
                            @yield('content')
                            <hr style="margin-top: 40px;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top: 30px; text-align: center; font-size: 12px; color: #7f8c8d;">
                            <p>This is an automated message. Please do not reply.</p>
                            <p>&copy; {{ date('Y') }} TrendMart. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
