<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mentoria;
use App\Models\Mentor;
use App\Models\Habilidade;
use App\Models\Empresa;
use App\Models\Cargo;
use App\Models\Area;

class MentorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $mentor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        User::factory(10)->create();
        $this->mentor = Mentor::factory()->create(['user_id' => $this->user->id]);
    }

    #[Test]

    public function pode_listar_todos_os_mentores()
    {
        Mentor::factory()->count(5)->create();

        $response = $this->getJson('/api/mentor');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'preco', 'created_at', 'updated_at']
                ]
            ]);
    }

    #[Test]
    public function pode_filtrar_mentores_por_cargo()
    {
        $cargo = Cargo::factory()->create(['nome' => 'Desenvolvedor']);
        $mentor = Mentor::factory()->create(['cargo_id' => $cargo->id]);

        $response = $this->getJson('/api/mentor?cargo=Desenvolvedor');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $mentor->id]);
    }

    #[Test]
    public function pode_filtrar_mentores_por_empresa()
    {
        $empresa = Empresa::factory()->create(['nome' => 'Tech Corp']);
        $mentor = Mentor::factory()->create(['empresa_id' => $empresa->id]);

        $response = $this->getJson('/api/mentor?empresa=Tech');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $mentor->id]);
    }

    #[Test]
    public function pode_filtrar_mentores_por_area()
    {
        $area = Area::factory()->create(['nome' => 'Tecnologia']);
        $habilidade = Habilidade::factory()->create(['area_id' => $area->id]);
        User::factory()->create();
        $mentor = Mentor::factory()->create();
        $mentor->habilidades()->attach($habilidade);

        $response = $this->getJson('/api/mentor?area=Tecnologia');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $mentor->id]);
    }

    #[Test]
    public function usuario_autenticado_pode_registrar_como_mentor()
    {
        $user = User::factory()->create();
        $cargo = Cargo::factory()->create();
        $empresa = Empresa::factory()->create();

        $data = [
            'preco' => 100.50,
            'descricao' => 'Mentor experiente',
            'cargo_id' => $cargo->id,
            'empresa_id' => $empresa->id,
            'minutos_por_chamada' => 60,
            'quantidade_chamadas' => 4
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/mentor', $data);

        $response->assertStatus(201)
            ->assertJsonPath('user.id', $user->id);

        $this->assertDatabaseHas('mentors', [
            'user_id' => $user->id,
            'preco' => 10050 // Valor em centavos
        ]);
    }

    #[Test]
    public function pode_visualizar_dados_de_um_mentor()
    {
        User::factory(5)->create();
        $mentor = Mentor::factory()->create();

        $response = $this->getJson("/api/mentor/{$mentor->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $mentor->id]);
    }

    #[Test]
    public function retorna_404_ao_buscar_mentor_inexistente()
    {
        $response = $this->getJson('/api/mentor/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function mentor_pode_atualizar_proprios_dados()
    {
        $data = [
            'biografia' => 'Nova descrição atualizada',
            'preco' => 150.00
        ];

        $response = $this->actingAs($this->user, 'api')
            ->patchJson("/api/mentor/{$this->mentor->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['biografia' => 'Nova descrição atualizada']);
    }

    #[Test]
    public function usuario_nao_pode_atualizar_dados_de_outro_mentor()
    {
        $outroUser = User::factory()->create();

        $response = $this->actingAs($outroUser, 'api')
            ->patchJson("/api/mentor/{$this->mentor->id}", ['biografia' => 'Tentativa']);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Somente o próprio usuário pode editar suas informações']);
    }

    #[Test]
    public function mentor_pode_adicionar_habilidade()
    {
        Area::factory(20)->create();
        $habilidade = Habilidade::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->patchJson("/api/mentor/habilidade/{$habilidade->id}");

        $response->assertStatus(200);
        $this->assertTrue($this->mentor->habilidades->contains($habilidade));
    }

    #[Test]
    public function usuario_nao_mentor_nao_pode_adicionar_habilidade()
    {
        $user = User::factory()->create();
        Area::factory(20)->create();
        $habilidade = Habilidade::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/mentor/habilidade/{$habilidade->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Usuário não é mentor']);
    }

    #[Test]
    public function pode_setar_lista_de_habilidades()
    {
        Area::factory(20)->create();
        $habilidades = Habilidade::factory()->count(3)->create();
        $ids = $habilidades->pluck('id')->toArray();

        $response = $this->actingAs($this->user, 'api')
            ->patchJson("/api/mentor/{$this->mentor->id}/habilidades", [
                'habilidades' => $ids
            ]);

        $response->assertStatus(200);
        $this->assertEquals(3, $this->mentor->fresh()->habilidades->count());
    }

    #[Test]
    public function nao_pode_setar_habilidades_inexistentes()
    {
        $response = $this->actingAs($this->user, 'api')
            ->patchJson("/api/mentor/{$this->mentor->id}/habilidades", [
                'habilidades' => [999, 998]
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Foi informado id de habilidade inexistente']);
    }

    #[Test]
    public function mentor_pode_configurar_cargo()
    {
        $cargo = Cargo::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->patchJson("/api/mentor/cargo/{$cargo->id}");

        $response->assertStatus(200);
        $this->assertEquals($cargo->id, $this->mentor->fresh()->cargo_id);
    }

    #[Test]
    public function mentor_pode_configurar_empresa()
    {
        $empresa = Empresa::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->patchJson("/api/mentor/empresa/{$empresa->id}");

        $response->assertStatus(200);
        $this->assertEquals($empresa->id, $this->mentor->fresh()->empresa_id);
    }

    #[Test]
    public function mentor_pode_listar_suas_mentorias()
    {
        Mentoria::factory()->count(3)->create(['mentor_id' => $this->mentor->id, 'valor' => 500, 'quantidade_sessoes' => 5, 'expectativa' => 'Teste', 'user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/mentor/minhas');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }
}
