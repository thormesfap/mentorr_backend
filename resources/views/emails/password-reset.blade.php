<!DOCTYPE html>
<html>

<head>
    <title>Recuperação de Senha</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50; text-align: center;">Recuperação de Senha</h1>

        <p>Você solicitou a recuperação de senha para sua conta no MindLink Mentorr.</p>

        <p>Para redefinir sua senha, clique no botão abaixo:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}"
                style="background-color: #3498db;
                      color: white;
                      padding: 12px 24px;
                      text-decoration: none;
                      border-radius: 4px;
                      display: inline-block;">
                Redefinir Senha
            </a>
        </div>

        <p>Se você não solicitou a recuperação de senha, por favor ignore este email.</p>

        <p>O link de recuperação expirará em 60 minutos.</p>

        <hr style="border: 1px solid #eee; margin: 30px 0;">

        <p style="color: #7f8c8d; font-size: 12px; text-align: center;">
            Este é um email automático, por favor não responda.
            <br>
            MindLink Mentorr © {{ date('Y') }}
        </p>
    </div>
</body>

</html>