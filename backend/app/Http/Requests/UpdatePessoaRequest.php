<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePessoaRequest extends FormRequest
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
        $pessoaId = $this->route('pessoa');
        
        return [
            'user_id' => 'nullable|exists:users,id',
            'nome_completo' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20|unique:pessoas,nif,' . $pessoaId,
            'email' => 'nullable|email|max:255',
            'telemovel' => 'nullable|string|max:20',
            'telefone_fixo' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'nacionalidade' => 'nullable|string|max:100',
            'naturalidade' => 'nullable|string|max:100',
            'numero_identificacao' => 'nullable|string|max:50',
            'validade_identificacao' => 'nullable|date',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'foto_perfil' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nome_completo.required' => 'O nome completo é obrigatório',
            'email.email' => 'Email inválido',
            'nif.unique' => 'Este NIF já está registado',
        ];
    }
}
