<?php

namespace App\Mail;

use App\Models\Mentor;
use App\Models\SessaoMentoria;
use App\Models\SolicitacaoMentoria;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SessaoMentoriaAgendada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private Mentor $mentor,
        private User $aluno,
        private SessaoMentoria $sessao
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo agendamento de Mentoria',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agendamento-mentoria',
            with: [
                'nomeMentor' => $this->mentor->user->name,
                'nomeAluno' => $this->aluno->name,
                'dataSessao' => $this->sessao->data_hora_inicio->format("d/m/Y H:i")
            ]
        );
    }
}
