<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Password Change OTP - CCMW</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; color: #333;">
    <table
        style="max-width: 600px; margin: auto; background: #fff; border: 1px solid #eee; padding: 20px; border-radius: 8px;">
        <tr>
            <td>
                <p>Dear {{ $name }},</p>

                <p>
                    We received a request to change the password for your 
                    <span style="font-size: 12px; font-weight: bold; color: #000;">
                        C<span style="color: red; font-weight: bold;">M</span>W
                    </span> account.
                </p>

                <p>Please use the following OTP (One-Time Password) to continue:</p>

                <p style="text-align: center; margin: 30px 0;">
                    <span
                        style="font-size: 24px; font-weight: bold; letter-spacing: 4px; background: #f0f0f0; padding: 10px 20px; border-radius: 5px; display: inline-block;">
                        {{ $otp }}
                    </span>
                </p>

                <p>
                    ⚠️ This OTP is valid for <strong>10 minutes</strong>.
                    Do not share it with anyone for your account security.
                </p>

                <p>If you didn’t request a password change, you can safely ignore this email.</p>

                <p>Best Regards,</p>

                <p style="font-weight: bold;">
                    <span style="font-size: 12px; font-weight: bold; color: #000;">
                        C<span style="color: red; font-weight: bold;">M</span>W
                    </span> Team
                </p>

                <div style="margin-top: 20px; max-width: 200px;">
                    <div style="text-align: center;">
                        <img src="https://ccmw.app/dashboard/assets/images/image_112.png" alt="CMW Full Logo"
                            style="max-width: 200px;">
                        <p style="margin-top: 0px;">
                            <a href="https://ccmw.app"
                                style="color: #007bff; text-decoration: underline;">www.ccmw.app</a>
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
