# Refatoração ClubManager - Backend

## Resumo da Implementação

Esta refatoração implementa a arquitetura definida em `ClubManager_SPEC_DEFINITIVA_Copilot_Rewrite.md`.

### Componentes Criados

#### 1. Services

##### **ClubContext** (`app/Services/Tenancy/ClubContext.php`)
Gestão de contexto multi-clube (tenancy):
- `getActiveClub()` - Obter clube ativo da sessão
- `setActiveClub($clubId)` - Definir clube ativo (valida acesso)
- `getUserClubs()` - Listar clubes do utilizador
- `requireActiveClub()` - Garantir clube ativo ou lançar exceção

##### **MembroService** (`app/Services/Membros/MembroService.php`)
Criação e gestão completa de membros:
- `criarMembro($dados)` - Workflow completo:
  1. Garantir User
  2. Garantir club_users
  3. Upsert dados_pessoais
  4. Criar membros
  5. Upsert dados_configuracao
  6. Attach tipos_utilizador
  7. Se atleta → criar dados desportivos
  8. Se tem mensalidade → criar dados_financeiros

##### **FaturacaoService** (`app/Services/Financeiro/FaturacaoService.php`)
Faturação automática:
- `gerarFaturasMensalidade($membroId, $mesInicio, $mesFim)` - Gerar faturas mensais
- `criarFaturaAvulsa($membroId, $itens)` - Criar fatura custom
- `adicionarItemFatura($faturaId, $item)` - Adicionar itens

##### **ContaCorrenteService** (`app/Services/Financeiro/ContaCorrenteService.php`)
Estado financeiro derivado:
- `contaCorrente($membroId)` - Extrato completo com saldos
- `resumoFinanceiro($membroId)` - Totais e estatísticas
- `registarPagamento($faturaId, $dados)` - Registar pagamento
- **Estado calculado dinamicamente** (não persistido)

##### **StockService** (`app/Services/Inventario/StockService.php`)
Gestão de inventário:
- `registarEntrada($materialId, $quantidade)` - Entrada de stock
- `registarSaida($materialId, $quantidade)` - Saída de stock
- `criarEmprestimo($materialId, $quantidade, $dados)` - Criar empréstimo
- `registarDevolucao($emprestimoId)` - Devolução de empréstimo
- Validações automáticas de stock

#### 2. Controllers

##### **ClubSwitchController** (`app/Http/Controllers/ClubSwitchController.php`)
Gestão de seleção de clube:
- `GET /clubs` - Listar clubes do utilizador
- `POST /clubs/switch` - Selecionar clube ativo
- `GET /clubs/active` - Obter clube ativo
- `POST /clubs/clear` - Limpar seleção

##### **MembrosController** (`app/Http/Controllers/Api/MembrosController.php`)
CRUD de membros (nova versão):
- `GET /v2/membros` - Listar membros (com filtros)
- `POST /v2/membros` - Criar membro completo
- `GET /v2/membros/{id}` - Detalhes do membro
- `PUT /v2/membros/{id}` - Atualizar membro
- `DELETE /v2/membros/{id}` - Desativar membro

##### **FaturasController** (`app/Http/Controllers/Api/FaturasController.php`)
Gestão de faturas e pagamentos:
- `GET /v2/faturas` - Listar faturas
- `POST /v2/faturas` - Criar fatura avulsa
- `POST /v2/faturas/gerar-mensalidades` - Gerar mensalidades
- `POST /v2/faturas/{id}/pagamentos` - Registar pagamento
- `GET /v2/membros/{id}/conta-corrente` - Extrato
- `GET /v2/membros/{id}/resumo-financeiro` - Resumo

#### 3. Middleware

##### **EnsureClubContext** (`app/Http/Middleware/EnsureClubContext.php`)
Valida que há um clube ativo na sessão antes de aceder a recursos multi-tenant.
- Retorna 400 se não houver clube ativo
- Adiciona `active_club_id` ao request

#### 4. Model Updates

##### **User Model** (`app/Models/User.php`)
Atualizado com:
- Trait `HasRoles` do Spatie Permission
- Relações: `dadosPessoais`, `clubUsers`, `clubs`, `membros`
- Método `getMembroAtivo($clubId)` para obter membro no clube ativo

### Rotas API

```
POST   /api/login
POST   /api/register

# Gestão de Clubes
GET    /api/clubs
POST   /api/clubs/switch
GET    /api/clubs/active
POST   /api/clubs/clear

# Membros (nova versão - requer clube ativo)
GET    /api/v2/membros
POST   /api/v2/membros
GET    /api/v2/membros/{id}
PUT    /api/v2/membros/{id}
DELETE /api/v2/membros/{id}

# Faturas (nova versão - requer clube ativo)
GET    /api/v2/faturas
POST   /api/v2/faturas
POST   /api/v2/faturas/gerar-mensalidades
POST   /api/v2/faturas/{id}/itens
POST   /api/v2/faturas/{id}/pagamentos

# Conta Corrente
GET    /api/v2/membros/{membroId}/conta-corrente
GET    /api/v2/membros/{membroId}/resumo-financeiro
```

### Fluxos Principais

#### 1. Criar Membro Completo

```php
POST /api/v2/membros
{
  "user": {
    "name": "João Silva",
    "email": "joao@example.com",
    "telefone": "912345678"
  },
  "dados_pessoais": {
    "nome_completo": "João Manuel Silva",
    "data_nascimento": "2005-03-15",
    "nif": "123456789",
    "morada": "Rua ABC, 123"
  },
  "tipos_utilizador": [1, 2],  // IDs de tipos (ex: atleta, sócio)
  "dados_desportivos": {  // Se for atleta
    "num_federacao": "FED12345",
    "escalao_atual_id": 3
  },
  "dados_financeiros": {
    "mensalidade_id": 1,
    "dia_cobranca": 5
  }
}
```

#### 2. Gerar Mensalidades

```php
POST /api/v2/faturas/gerar-mensalidades
{
  "membro_id": 1,
  "mes_inicio": "2024-01",
  "mes_fim": "2024-07"  // Opcional, default = Julho do ano corrente
}
```

#### 3. Obter Conta Corrente

```php
GET /api/v2/membros/1/conta-corrente

Response:
{
  "linhas": [
    {
      "tipo": "fatura",
      "data": "2024-01-01",
      "mes": "2024-01",
      "descricao": "Fatura #1 - 2024-01",
      "debito": 30.00,
      "credito": 0,
      "saldo_fatura": 30.00,
      "saldo_acumulado": 30.00,
      "estado": "pendente",
      "fatura_id": 1
    },
    {
      "tipo": "pagamento",
      "data": "2024-01-05",
      "descricao": "Pagamento - transferencia",
      "debito": 0,
      "credito": 30.00,
      "saldo_acumulado": 0,
      "pagamento_id": 1
    }
  ],
  "saldo_total": 0,
  "total_em_atraso": 0
}
```

### Arquitetura

```
┌─────────────────────────────────────────────┐
│            Frontend (React/TS)               │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│          API Routes (Sanctum Auth)           │
│  ├─ /clubs                                   │
│  ├─ /v2/membros   [EnsureClubContext]       │
│  └─ /v2/faturas   [EnsureClubContext]       │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│              Controllers                     │
│  ├─ ClubSwitchController                    │
│  ├─ MembrosController                       │
│  └─ FaturasController                       │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│               Services                       │
│  ├─ ClubContext (Tenancy)                   │
│  ├─ MembroService                           │
│  ├─ FaturacaoService                        │
│  ├─ ContaCorrenteService                    │
│  └─ StockService                            │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│               Models                         │
│  ├─ Club                                     │
│  ├─ User (+ Spatie HasRoles)                │
│  ├─ Membro                                   │
│  ├─ Fatura (estado derivado)                │
│  └─ ... (30+ models)                        │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│          PostgreSQL Database                 │
│  57 tables (multi-tenant com club_id)       │
└─────────────────────────────────────────────┘
```

### Próximos Passos

1. **Resolver problema de migrations PostgreSQL**
   - Testar migrations em SQLite local
   - Ou executar SQL diretamente no Neon.tech

2. **Executar seeders**
   ```bash
   php artisan db:seed --class=ClubSeeder
   php artisan db:seed --class=PermissionsSeeder
   php artisan db:seed --class=NotificacoesTiposSeeder
   php artisan db:seed --class=ConfiguracaoClubSeeder
   ```

3. **Criar Policies**
   - `MembroPolicy` - Autorização baseada em Spatie
   - `FaturaPolicy` - Validar acesso ao clube

4. **Criar Form Requests**
   - `StoreMembroRequest`
   - `UpdateMembroRequest`
   - `StoreFaturaRequest`

5. **Adaptar Frontend**
   - Implementar seleção de clube na UI
   - Atualizar chamadas API para `/v2/`
   - Adicionar gestão de permissões Spatie

### Compatibilidade

- Rotas antigas mantidas em `/api/membros`, `/api/faturas`
- Novas rotas em `/api/v2/membros`, `/api/v2/faturas`
- Migração progressiva sem quebrar frontend existente
