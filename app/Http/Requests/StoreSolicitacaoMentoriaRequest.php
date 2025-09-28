<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitacaoMentoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mentor_id' => ['required', 'exists:users,id'],
            'expectativa' => ['required', 'max:255']
        ];
    }

    public function messages(): array
    {
        return [
            'mentor_id.required' => 'O ID do mentor é obrigatório',
            'mentor_id.exists' => 'O mentor informado não existe'
        ];
    }
}
