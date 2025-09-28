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

class RespostaSolicitacaoMentoria extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private Mentor $mentor,
        private User $aluno,
        private bool $aceita,
        private ?string $justificativa = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resposta da Solicitação de Mentoria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.resposta-solicitacao-mentoria',
            with: [
                'nomeMentor' => $this->mentor->user->name,
                'nomeAluno' => $this->aluno->name,
                'aceita' => $this->aceita,
                'justificativa' => $this->justificativa
            ]
        );
    }
}
