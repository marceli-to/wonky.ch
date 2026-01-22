<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter-Anmeldung bestätigen</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 40px;
        }
        h1 {
            color: #111;
            margin-top: 0;
            font-size: 24px;
        }
        .button {
            display: inline-block;
            background: #111;
            color: #ffffff !important;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .button:hover {
            background: #333;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }
        .link {
            word-break: break-all;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Newsletter-Anmeldung bestätigen</h1>

        <p>Vielen Dank für Ihr Interesse an unserem Newsletter!</p>

        <p>Bitte klicken Sie auf den folgenden Button, um Ihre Anmeldung zu bestätigen:</p>

        <a href="{{ route('newsletter.confirm', $subscriber->token) }}" class="button">
            Anmeldung bestätigen
        </a>

        <p class="footer">
            Falls der Button nicht funktioniert, kopieren Sie diesen Link in Ihren Browser:<br>
            <span class="link">{{ route('newsletter.confirm', $subscriber->token) }}</span>
        </p>

        <p class="footer">
            Falls Sie diese Anmeldung nicht angefordert haben, können Sie diese E-Mail ignorieren.
        </p>
    </div>
</body>
</html>
