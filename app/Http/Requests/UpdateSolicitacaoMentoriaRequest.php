<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitacaoMentoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'aceita' => ['required', 'boolean'],
            'justificativa' => ['required_if:aceita,false', 'nullable', 'string', 'max:255']
        ];
    }

    public function messages(): array
    {
        return [
            'aceita.required' => 'É necessário informar se aceita ou não a mentoria',
            'aceita.boolean' => 'O campo aceita deve ser verdadeiro ou falso',
            'justificativa.required_if' => 'A justificativa é obrigatória quando a mentoria é recusada',
            'justificativa.max' => 'A justificativa não pode ter mais que 255 caracteres'
        ];
    }
}
