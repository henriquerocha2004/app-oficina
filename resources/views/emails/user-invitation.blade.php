<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convite para {{ $tenantName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 200px;
            height: auto;
        }
        h1 {
            color: #1a1a1a;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            font-size: 16px;
        }
        .invitation-details {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .invitation-details p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .expiration-notice {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($tenantLogo)
        <div class="logo">
            <img src="{{ $tenantLogo }}" alt="{{ $tenantName }}">
        </div>
        @endif

        <h1>Você foi convidado!</h1>

        <div class="message">
            <p>Olá!</p>
            <p><strong>{{ $invitedByName }}</strong> convidou você para fazer parte da equipe de <strong>{{ $tenantName }}</strong>.</p>
        </div>

        <div class="invitation-details">
            <p><strong>Perfil de Acesso:</strong> {{ $roleName }}</p>
            <p><strong>Email:</strong> {{ $invitation->email }}</p>
        </div>

        <div class="message">
            <p>Para aceitar este convite e criar sua conta, clique no botão abaixo:</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ $acceptUrl }}" class="button">Aceitar Convite</a>
        </div>

        <p class="expiration-notice">
            ⏰ <strong>Atenção:</strong> Este convite expira em {{ $expiresAt->format('d/m/Y \à\s H:i') }}
        </p>

        <div class="footer">
            <p>Se você não esperava receber este convite, pode ignorar este email com segurança.</p>
            <p>Se o botão acima não funcionar, copie e cole este link no seu navegador:</p>
            <p style="word-break: break-all; color: #007bff;">{{ $acceptUrl }}</p>
        </div>
    </div>
</body>
</html>
