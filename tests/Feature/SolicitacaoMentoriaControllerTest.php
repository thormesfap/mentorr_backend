<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\SolicitacaoMentoria;
use App\Models\Mentoria;
use App\Models\Mentor;
use App\Mail\RespostaSolicitacaoMentoria;
use App\Mail\NotificacaoSolicitacaoMentoria;
use App\Jobs\SendEmail;
use App\Events\MentoriaRespondida;
use App\Events\MatriculaAluno;

class SolicitacaoMentoriaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $mentorUser;
    protected $mentor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->mentorUser = User::factory()->create();
        $this->mentor = Mentor::factory()->create([
            'user_id' => $this->mentorUser->id,
            'preco' => 10000,
            'quantidade_chamadas' => 4
        ]);
    }

    #[Test]
    public function usuario_pode_criar_solicitacao_de_mentoria()
    {
        Event::fake([MatriculaAluno::class]);
        Queue::fake();

        $data = [
            'mentor_id' => $this->mentor->id,
            'expectativa' => 'Quero aprender desenvolvimento web',
            'user_id' => $this->user->id
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/solicitacao_mentoria', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'mentor_id',
                'expectativa',
                'mentor',
                'user'
            ]);

        $this->assertDatabaseHas('solicitacao_mentorias', [
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id,
            'expectativa' => 'Quero aprender desenvolvimento web'
        ]);

        Event::assertDispatched(MatriculaAluno::class);
        Queue::assertPushed(SendEmail::class);
    }

    #[Test]
    public function mentor_nao_pode_solicitar_mentoria_para_si_mesmo()
    {
        $data = [
            'mentor_id' => $this->mentor->id,
            'user_id' => $this->user->id,
            'expectativa' => 'Teste'
        ];

        $response = $this->actingAs($this->mentorUser, 'api')
            ->postJson('/api/solicitacao_mentoria', $data);

        $response->assertStatus(400)
            ->assertJson([
                'sucess' => false,
                'message' => 'Não pode solicitar mentoria para si mesmo'
            ]);
    }

    #[Test]
    public function nao_pode_criar_solicitacao_duplicada_pendente()
    {
        SolicitacaoMentoria::factory()->create([
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id,
            'data_hora_resposta' => null
        ]);

        $data = [
            'mentor_id' => $this->mentor->id,
            'expectativa' => 'Nova solicitação'
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/solicitacao_mentoria', $data);

        $response->assertStatus(400)
            ->assertJson([
                'sucess' => false,
                'message' => 'Já há solicitação pendente para este mentor, aguarde resposta'
            ]);
    }

    #[Test]
    public function pode_criar_nova_solicitacao_apos_resposta_anterior()
    {
        Queue::fake();
        Event::fake();

        // Criar solicitação já respondida
        SolicitacaoMentoria::factory()->create([
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id,
            'data_hora_resposta' => now(),
            'aceita' => false
        ]);

        $data = [
            'mentor_id' => $this->mentor->id,
            'expectativa' => 'Nova tentativa'
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/solicitacao_mentoria', $data);

        $response->assertStatus(201);
    }

    #[Test]
    public function mentor_pode_aceitar_solicitacao()
    {
        Event::fake([MentoriaRespondida::class]);
        Queue::fake();

        $solicitacao = SolicitacaoMentoria::factory()->create([
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id,
            'data_hora_resposta' => null
        ]);

        $data = [
            'aceita' => true,
            'justificativa' => 'Será um prazer mentorar você'
        ];

        $response = $this->actingAs($this->mentorUser, 'api')
            ->patchJson("/api/solicitacao_mentoria/{$solicitacao->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'aceita' => true,
                'justificativa' => 'Será um prazer mentorar você'
            ]);

        $solicitacao->refresh();
        $this->assertNotNull($solicitacao->data_hora_resposta);

        // Verificar que mentoria foi criada
        $this->assertDatabaseHas('mentorias', [
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id
        ]);

        Event::assertDispatched(MentoriaRespondida::class);
        Queue::assertPushed(SendEmail::class);
    }

    #[Test]
    public function mentor_pode_recusar_solicitacao()
    {
        Event::fake([MentoriaRespondida::class]);
        Queue::fake();

        $solicitacao = SolicitacaoMentoria::factory()->create([
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id,
            'data_hora_resposta' => null
        ]);

        $data = [
            'aceita' => false,
            'justificativa' => 'Agenda lotada no momento'
        ];

        $response = $this->actingAs($this->mentorUser, 'api')
            ->patchJson("/api/solicitacao_mentoria/{$solicitacao->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'aceita' => false,
                'justificativa' => 'Agenda lotada no momento'
            ]);

        // Verificar que mentoria NÃO foi criada
        $this->assertDatabaseMissing('mentorias', [
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id
        ]);

        Event::assertDispatched(MentoriaRespondida::class);
        Queue::assertPushed(SendEmail::class);
    }

    #[Test]
    public function somente_mentor_dono_pode_responder_solicitacao()
    {
        User::factory(25)->create();
        $outroMentor = Mentor::factory()->create();
        $solicitacao = SolicitacaoMentoria::factory()->create([
            'mentor_id' => $this->mentor->id,
            'user_id' => $this->user->id
        ]);

        $data = [
            'aceita' => true,
            'justificativa' => 'Teste'
        ];

        $response = $this->actingAs($outroMentor->user, 'api')
            ->patchJson("/api/solicitacao_mentoria/{$solicitacao->id}", $data);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Não autorizado']);
    }

    #[Test]
    public function nao_pode_responder_solicitacao_ja_respondida()
    {
        $solicitacao = SolicitacaoMentoria::factory()->create([
            'mentor_id' => $this->mentor->id,
            'data_hora_resposta' => now(),
            'aceita' => true,
            'user_id' => $this->user->id

        ]);

        $data = [
            'aceita' => false,
            'justificativa' => 'Mudei de ideia'
        ];

        $response = $this->actingAs($this->mentorUser, 'api')
            ->patchJson("/api/solicitacao_mentoria/{$solicitacao->id}", $data);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Esta solicitação já foi respondida']);
    }

    #[Test]
    public function mentor_pode_listar_solicitacoes_recebidas()
    {
        SolicitacaoMentoria::factory()->count(3)->create([
            'data_hora_resposta' => null,
            'mentor_id' => $this->mentor->id,
            'user_id' => $this->user->id
        ]);

        // Solicitações já respondidas (não devem aparecer)
        SolicitacaoMentoria::factory()->count(2)->create([
            'mentor_id' => $this->mentor->id,
            'data_hora_resposta' => now(),
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->mentorUser, 'api')
            ->getJson('/api/solicitacao_mentoria/mentor');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'user_id',
                    'mentor_id',
                    'expectativa',
                    'user',
                    'mentor'
                ]
            ]);
    }

    #[Test]
    public function usuario_pode_listar_suas_solicitacoes()
    {
        SolicitacaoMentoria::factory()->count(4)->create([
            'user_id' => $this->user->id,
            'data_hora_resposta' => null,
            'mentor_id' => $this->mentor->id
        ]);

        // Solicitações já respondidas (não devem aparecer)
        SolicitacaoMentoria::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'data_hora_resposta' => now(),
            'mentor_id' => $this->mentor->id
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/solicitacao_mentoria/usuario');

        $response->assertStatus(200)
            ->assertJsonCount(4);
    }

    #[Test]
    public function solicitacoes_sao_ordenadas_por_data_criacao_desc()
    {
        $primeira = SolicitacaoMentoria::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(3),
            'data_hora_resposta' => null,
            'mentor_id' => $this->mentor->id
        ]);

        $segunda = SolicitacaoMentoria::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(1),
            'data_hora_resposta' => null,
            'mentor_id' => $this->mentor->id
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/solicitacao_mentoria/usuario');

        $response->assertStatus(200);

        $solicitacoes = $response->json();
        $this->assertEquals($segunda->id, $solicitacoes[0]['id']);
        $this->assertEquals($primeira->id, $solicitacoes[1]['id']);
    }

    #[Test]
    public function mentoria_criada_com_dados_corretos_ao_aceitar()
    {
        Queue::fake();
        Event::fake();

        $solicitacao = SolicitacaoMentoria::factory()->create([
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id,
            'expectativa' => 'Aprender Laravel',
            'data_hora_resposta' => null
        ]);

        $data = [
            'aceita' => true,
            'justificativa' => 'Aceito'
        ];

        $this->actingAs($this->mentorUser, 'api')
            ->patchJson("/api/solicitacao_mentoria/{$solicitacao->id}", $data);

        $this->assertDatabaseHas('mentorias', [
            'user_id' => $this->user->id,
            'mentor_id' => $this->mentor->id,
            'expectativa' => 'Aprender Laravel',
            'valor' => $this->mentor->preco,
            'quantidade_sessoes' => $this->mentor->quantidade_chamadas
        ]);
    }
}
