<?php

namespace App\Mail;

use App\Models\Mentor;
use App\Models\SolicitacaoMentoria;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacaoSolicitacaoMentoria extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private Mentor $mentor,
        private User $aluno
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova Solicitação de Mentoria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitacao-mentoria',
            with: [
                'nomeMentor' => $this->mentor->user->name,
                'nomeAluno' => $this->aluno->name
            ]
        );
    }
}
