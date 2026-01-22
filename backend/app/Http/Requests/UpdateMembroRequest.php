<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $membroId = $this->route('membro');
        
        return [
            'pessoa_id' => 'required|exists:pessoas,id',
            'numero_socio' => 'nullable|string|max:50|unique:membros,numero_socio,' . $membroId,
            'estado' => 'required|in:ativo,inativo,pendente,suspenso',
            'data_inscricao' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'motivo_inativacao' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pessoa_id.required' => 'A pessoa é obrigatória',
            'pessoa_id.exists' => 'Pessoa não encontrada',
            'estado.required' => 'O estado é obrigatório',
        ];
    }
}
