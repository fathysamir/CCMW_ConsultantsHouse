<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to CMW</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #ffffff; padding: 20px; color: #333;">
    <table style="max-width: 1000px; margin: auto; border: 1px solid #eee; padding: 20px;">
        <tr>
            <td>
                <p>Dear : {{ $email }},</p>

                <p>
                    Please follow the link below to sign in to the<img src="https://ccmw.app/dashboard/assets/images/image_111.png" alt="CMW Logo" style="height: 20px; vertical-align: middle;">application
                </p>

                <p>
                    <a href="{{ url('/register?invitation=' . $code) }}" style="color: #007bff; text-decoration: underline; font-weight: bold;">Accept invitation</a>
                </p>

                <p>Once redirected to the application, you will need to enter your details and create a password.</p>

                <p>
                    For any technical issues, feel free to contact us at 
                    <a href="mailto:technicalsupport@ccmw.app" style="color: #007bff; text-decoration: underline;">technicalsupport@ccmw.app</a>.
                </p>

                <p>
                    For training, consultancy, or administrative inquiries, please reach out to 
                    <a href="mailto:customercare@ccmw.app" style="color: #007bff; text-decoration: underline;">customercare@ccmw.app</a>.
                </p>

                <p>Welcome to<img src="https://ccmw.app/dashboard/assets/images/image_111.png" alt="CMW Logo" style="height: 20px; vertical-align: middle;">— we hope you enjoy using our platform!
                </p>

                <p>Best Regards,</p>

                <p style="font-weight: bold;">
                    <img src="https://ccmw.app/dashboard/assets/images/image_111.png" alt="CMW Logo" style="height: 20px; vertical-align: middle;">Team
                </p>

                <div style="margin-top: 0px;max-width: 200px;">
                    <div style="text-align: center;">
                        <img src="https://ccmw.app/dashboard/assets/images/image_112.png" alt="CMW Full Logo" style="max-width: 200px;">
                        <p style="margin-top: 0px;"><a href="https://www.ccmw.app" style="color: #007bff; text-decoration: underline;margin-top:-10px">www.ccmw.app</a></p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
