<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ config('app.name') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: Arial, Helvetica, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f8; padding: 32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 10px; overflow: hidden;">

                {{-- Header --}}
                <tr>
                    <td style="padding: 28px 32px; background-color: #4f46e5;">
                        <h1 style="margin: 0; color: #ffffff; font-size: 22px; line-height: 1.3;">
                            Welcome, {{ $user->name }}!
                        </h1>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding: 32px;">
                        <p style="margin: 0 0 16px; font-size: 16px; line-height: 1.6; color: #374151;">
                            Your account has been created successfully.
                        </p>
                        <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #374151;">
                            You can now sign in with your email address:
                            <strong style="color: #111827;">{{ $user->email }}</strong>
                        </p>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding: 20px 32px; border-top: 1px solid #e5e7eb; font-size: 13px; line-height: 1.6; color: #9ca3af;">
                        Thanks,<br>
                        {{ config('app.name') }}
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
