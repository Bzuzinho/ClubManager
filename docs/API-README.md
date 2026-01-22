# 🏆 ClubManager API Documentation

API REST completa para gestão de clubes desportivos.

## 📋 Índice

- [Autenticação](#autenticação)
- [Endpoints Principais](#endpoints-principais)
- [Modelos de Dados](#modelos-de-dados)
- [Exemplos de Uso](#exemplos-de-uso)

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** para autenticação baseada em tokens.

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@clubmanager.com",
  "password": "admin123"
}
```

**Resposta:**
```json
{
  "token": "1|8lVBiAYd68xK2U8rgkDIrisjm30xIHEm3PoEdHCr6aa0baff",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@clubmanager.com"
  }
}
```

### Usar Token
Adicione o token em todas as requisições protegidas:
```http
Authorization: Bearer {seu-token}
```

## 📍 Endpoints Principais

### Pessoas
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/pessoas` | Listar pessoas |
| POST | `/api/pessoas` | Criar pessoa |
| GET | `/api/pessoas/{id}` | Ver pessoa |
| PUT | `/api/pessoas/{id}` | Atualizar pessoa |
| DELETE | `/api/pessoas/{id}` | Remover pessoa |
| POST | `/api/pessoas/{id}/restore` | Restaurar pessoa |

**Filtros disponíveis:**
- `?search=nome` - Pesquisa por nome, email, NIF ou telemóvel
- `?nacionalidade=Portuguesa` - Filtrar por nacionalidade
- `?per_page=20` - Paginação (padrão: 15)

### Membros
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/membros` | Listar membros |
| POST | `/api/membros` | Criar membro |
| GET | `/api/membros/{id}` | Ver membro |
| PUT | `/api/membros/{id}` | Atualizar membro |
| DELETE | `/api/membros/{id}` | Remover membro |
| PUT | `/api/membros/{id}/tipos` | Atualizar tipos |

**Filtros disponíveis:**
- `?estado=ativo` - Filtrar por estado
- `?search=nome` - Pesquisa
- `?tipo_membro_id=1` - Filtrar por tipo
- `?ativos=true` - Apenas ativos
- `?inativos=true` - Apenas inativos
- `?pendentes=true` - Apenas pendentes

### Atletas
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/atletas` | Listar atletas |
| POST | `/api/atletas` | Criar atleta |
| GET | `/api/atletas/{id}` | Ver atleta |
| PUT | `/api/atletas/{id}` | Atualizar atleta |
| DELETE | `/api/atletas/{id}` | Remover atleta |
| PUT | `/api/atletas/{id}/equipas` | Atualizar equipas |
| GET | `/api/atletas/{id}/estatisticas` | Estatísticas |

**Filtros disponíveis:**
- `?ativo=true` - Apenas ativos
- `?search=nome` - Pesquisa
- `?equipa_id=1` - Filtrar por equipa
- `?posicao=Médio` - Filtrar por posição

### Equipas
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/equipas` | Listar equipas |
| POST | `/api/equipas` | Criar equipa |
| GET | `/api/equipas/{id}` | Ver equipa |
| PUT | `/api/equipas/{id}` | Atualizar equipa |
| DELETE | `/api/equipas/{id}` | Remover equipa |
| GET | `/api/equipas/{id}/plantel` | Ver plantel |
| POST | `/api/equipas/{id}/atletas` | Adicionar atletas |

### Treinos
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/treinos` | Listar treinos |
| POST | `/api/treinos` | Criar treino |
| GET | `/api/treinos/{id}` | Ver treino |
| PUT | `/api/treinos/{id}` | Atualizar treino |
| DELETE | `/api/treinos/{id}` | Remover treino |
| POST | `/api/treinos/{id}/presencas` | Registar presenças |
| GET | `/api/treinos/{id}/estatisticas-presenca` | Estatísticas |

### Competições
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/competicoes` | Listar competições |
| POST | `/api/competicoes` | Criar competição |
| GET | `/api/competicoes/{id}` | Ver competição |
| PUT | `/api/competicoes/{id}` | Atualizar competição |
| DELETE | `/api/competicoes/{id}` | Remover competição |
| POST | `/api/competicoes/{id}/convocar` | Convocar atletas |

### Faturas
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/faturas` | Listar faturas |
| POST | `/api/faturas` | Criar fatura |
| GET | `/api/faturas/{id}` | Ver fatura |
| PUT | `/api/faturas/{id}` | Atualizar fatura |
| DELETE | `/api/faturas/{id}` | Remover fatura |
| POST | `/api/faturas/{id}/cancelar` | Cancelar fatura |

**Filtros disponíveis:**
- `?estado=pendente` - Filtrar por estado (pendente, paga, vencida, parcial, cancelada)
- `?tipo=mensalidade` - Filtrar por tipo
- `?membro_id=1` - Filtrar por membro
- `?pendentes=true` - Apenas pendentes
- `?pagas=true` - Apenas pagas
- `?vencidas=true` - Apenas vencidas

### Pagamentos
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/pagamentos` | Registar pagamento |
| POST | `/api/pagamentos/{id}/confirmar` | Confirmar pagamento |
| POST | `/api/pagamentos/{id}/cancelar` | Cancelar pagamento |

### Eventos
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/eventos` | Listar eventos |
| POST | `/api/eventos` | Criar evento |
| GET | `/api/eventos/{id}` | Ver evento |
| PUT | `/api/eventos/{id}` | Atualizar evento |
| DELETE | `/api/eventos/{id}` | Remover evento |
| POST | `/api/eventos/{id}/inscrever` | Inscrever membro |

### Documentos
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/documentos` | Listar documentos |
| POST | `/api/documentos` | Upload documento |
| GET | `/api/documentos/{id}` | Ver documento |
| DELETE | `/api/documentos/{id}` | Remover documento |
| GET | `/api/documentos/{id}/download` | Download documento |
| POST | `/api/documentos/{id}/validar` | Validar documento |

## 📦 Modelos de Dados

### Criar Pessoa
```json
{
  "nome_completo": "João Silva",
  "email": "joao.silva@example.com",
  "telemovel": "912345678",
  "data_nascimento": "2010-05-15",
  "nacionalidade": "Portuguesa",
  "morada": "Rua Principal, 123",
  "codigo_postal": "4000-123",
  "localidade": "Porto",
  "distrito": "Porto",
  "nif": "123456789",
  "observacoes": "Notas adicionais"
}
```

### Criar Membro
```json
{
  "pessoa_id": 1,
  "estado": "ativo",
  "data_inscricao": "2026-01-22",
  "data_inicio": "2026-01-22",
  "observacoes": "Membro regular",
  "tipos": [
    {
      "tipo_membro_id": 1,
      "data_inicio": "2026-01-22",
      "ativo": true
    }
  ]
}
```

### Criar Atleta
```json
{
  "membro_id": 1,
  "ativo": true,
  "numero_camisola": 10,
  "altura": 175,
  "peso": 70,
  "pe_dominante": "direito",
  "posicao_preferida": "Médio",
  "tamanho_equipamento": "M",
  "observacoes_medicas": "Sem restrições",
  "equipas": [
    {
      "equipa_id": 1,
      "numero_camisola": 10,
      "posicao": "Médio",
      "titular": true
    }
  ]
}
```

### Criar Fatura
```json
{
  "membro_id": 1,
  "data_emissao": "2026-01-22",
  "data_vencimento": "2026-02-22",
  "tipo": "mensalidade",
  "observacoes": "Mensalidade de janeiro",
  "itens": [
    {
      "descricao": "Mensalidade Janeiro 2026",
      "quantidade": 1,
      "preco_unitario": 30.00,
      "desconto": 0
    },
    {
      "descricao": "Equipamento desportivo",
      "quantidade": 1,
      "preco_unitario": 50.00,
      "desconto": 5.00
    }
  ]
}
```

### Registar Presenças em Treino
```json
{
  "presencas": [
    {
      "atleta_id": 1,
      "estado": "presente",
      "hora_chegada": "18:00",
      "hora_saida": "20:00"
    },
    {
      "atleta_id": 2,
      "estado": "ausente",
      "justificacao": "Doente"
    },
    {
      "atleta_id": 3,
      "estado": "atrasado",
      "hora_chegada": "18:30",
      "observacoes": "Atraso justificado"
    }
  ]
}
```

## 🚀 Exemplos de Uso

### 1. Criar novo atleta completo
```bash
# 1. Criar pessoa
curl -X POST http://localhost:8000/api/pessoas \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "nome_completo": "Pedro Santos",
    "email": "pedro@example.com",
    "telemovel": "912345678",
    "data_nascimento": "2010-03-15"
  }'

# 2. Criar membro
curl -X POST http://localhost:8000/api/membros \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "pessoa_id": 1,
    "estado": "ativo",
    "tipos": [{"tipo_membro_id": 1, "ativo": true}]
  }'

# 3. Criar atleta
curl -X POST http://localhost:8000/api/atletas \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "membro_id": 1,
    "ativo": true,
    "numero_camisola": 10,
    "posicao_preferida": "Avançado"
  }'
```

### 2. Registar presença em treino
```bash
curl -X POST http://localhost:8000/api/treinos/1/presencas \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "presencas": [
      {
        "atleta_id": 1,
        "estado": "presente",
        "hora_chegada": "18:00"
      }
    ]
  }'
```

### 3. Criar e confirmar pagamento
```bash
# 1. Criar pagamento
curl -X POST http://localhost:8000/api/pagamentos \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fatura_id": 1,
    "metodo_pagamento_id": 1,
    "data_pagamento": "2026-01-22",
    "valor": 30.00
  }'

# 2. Confirmar pagamento
curl -X POST http://localhost:8000/api/pagamentos/1/confirmar \
  -H "Authorization: Bearer {token}"
```

## 📊 Respostas da API

### Sucesso
```json
{
  "message": "Operação realizada com sucesso",
  "data": {
    "id": 1,
    ...
  }
}
```

### Erro de Validação
```json
{
  "message": "Os dados fornecidos são inválidos",
  "errors": {
    "email": ["O email é inválido"],
    "nome_completo": ["O nome completo é obrigatório"]
  }
}
```

### Erro de Autenticação
```json
{
  "message": "Não autenticado"
}
```

## 🔗 Importar Coleção Postman

Importe o ficheiro `ClubManager-API.postman_collection.json` no Postman ou Insomnia para testar todos os endpoints.

## 📝 Notas

- Todos os endpoints (exceto login e register) requerem autenticação
- As respostas de listagens são paginadas por padrão (15 itens)
- Suporte a soft deletes em modelos principais
- Validação automática de dados com mensagens em português
- Transações de base de dados para operações complexas
