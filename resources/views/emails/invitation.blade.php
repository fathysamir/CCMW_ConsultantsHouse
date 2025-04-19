<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CCMW Invitation</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
      background-color: #f6f8fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border: 1px solid #d0d7de;
      border-radius: 6px;
      text-align: center;
    }

    .logo {
      margin-bottom: 30px;
    }

    

    .avatars {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
    }

    .avatars img {
      width: 150px;
      
      border-radius: 8px;
      margin: 0 8px;
    }

    .text {
      font-size: 16px;
      color: #24292f;
      margin-bottom: 20px;
    }

    .text a {
      color: #0969da;
      text-decoration: none;
    }

    .text a:hover {
      text-decoration: underline;
    }

    .button {
      display: inline-block;
      background-color: #2da44e;
      color: white;
      padding: 12px 24px;
      font-size: 14px;
      font-weight: 600;
      border-radius: 6px;
      text-decoration: none;
    }

    .button:hover {
      background-color: #218639;
    }

    .footer {
      font-size: 13px;
      color: #57606a;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">
        <h2>CCMW</h2>
    </div>
    <div class="avatars">
      <img src="https://ccmw.app/dashboard/assets/images/logo.png" alt="User 1" />
    </div>
    <div class="text">
      <strong>@<span>{{ $sender_name }}</span></strong> has invited you to collaborate on the <strong>CCMW/{{ $account_name }}</strong> account
    </div>
    <div class="text">
      You can accept or decline this invitation.
    </div>
    <div class="footer">
      This invitation will expire in 7 days.
    </div>
    <br />
    <a class="button" href="{{ url('/register?invitation=' . $code) }}">Accept invitation</a>
  </div>
</body>
</html>
