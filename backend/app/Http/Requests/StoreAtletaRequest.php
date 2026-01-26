<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAtletaRequest extends FormRequest
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
        return [
            'membro_id' => 'required|exists:membros,id',
            'ativo' => 'boolean',
            'numero_camisola' => 'nullable|integer|min:1|max:999',
            'tamanho_equipamento' => 'nullable|string|max:10',
            'altura' => 'nullable|numeric|min:0|max:300',
            'peso' => 'nullable|numeric|min:0|max:200',
            'pe_dominante' => 'nullable|in:direito,esquerdo,ambidestro',
            'posicao_preferida' => 'nullable|string|max:50',
            'observacoes_medicas' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'equipas' => 'nullable|array',
            'equipas.*.equipa_id' => 'required|exists:equipas,id',
            'equipas.*.numero_camisola' => 'nullable|integer|min:1|max:999',
            'equipas.*.posicao' => 'nullable|string|max:50',
            'equipas.*.titular' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'membro_id.required' => 'O membro é obrigatório',
            'membro_id.exists' => 'Membro não encontrado',
            'numero_camisola.max' => 'Número de camisola deve ser até 999',
            'altura.max' => 'Altura inválida',
            'peso.max' => 'Peso inválido',
        ];
    }
}
