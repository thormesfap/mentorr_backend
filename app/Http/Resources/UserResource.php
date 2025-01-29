<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            "nome" => $this->name,
            "email" => $this->email,
            'roles' => $this->roleNames,
            'mentor' => $this->mentor,
            'telefone' => $this->telefone,
            'data_nascimento' => $this->data_nascimento,
            'foto_perfil' => $this->when($this->foto_perfil, function () {
                return '/storage/' . $this->foto_perfil;
            })
        ];
    }
}
