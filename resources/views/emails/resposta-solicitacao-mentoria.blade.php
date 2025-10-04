<!DOCTYPE html>
<html>

<head>
    <title>Resposta da Solicitação de Mentoria</title>
</head>

<body>
    <h2>Resposta da Solicitação de Mentoria</h2>
    <p>Olá {{ $nomeAluno }},</p>

    @if($aceita)
    <p>O mentor {{ $nomeMentor }} aceitou sua solicitação de mentoria!</p>
    <p>Aguarde o mentor agendar sua primeira sessão.</p>
    @else
    <p>Infelizmente o mentor {{ $nomeMentor }} não poderá aceitar sua solicitação de mentoria no momento.</p>
    @if($justificativa)
    <p>Justificativa: {{ $justificativa }}</p>
    @endif
    @endif

    <br>
    <p>Atenciosamente,</p>
    <p>Equipe Mentorr</p>
</body>

</html>
