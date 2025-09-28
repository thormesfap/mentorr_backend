<!DOCTYPE html>
<html>

<head>
    <title>Verificação de Email</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50; text-align: center;">Verificação de Email</h1>

        <p>Bem-vindo ao MindLink Mentorr! Por favor, verifique seu endereço de email para ativar sua conta.</p>

        <p>Para verificar seu email, clique no botão abaixo:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}"
                style="background-color: #3498db; 
                      color: white; 
                      padding: 12px 24px; 
                      text-decoration: none; 
                      border-radius: 4px;
                      display: inline-block;">
                Verificar Email
            </a>
        </div>

        <p>Se você não criou uma conta no MindLink Mentorr, por favor ignore este email.</p>

        <hr style="border: 1px solid #eee; margin: 30px 0;">

        <p style="color: #7f8c8d; font-size: 12px; text-align: center;">
            Este é um email automático, por favor não responda.
            <br>
            MindLink Mentorr © {{ date('Y') }}
        </p>
    </div>
</body>

</html>