# Versionamento da API ClubManager

**Data:** 22 de Janeiro de 2026  
**Status:** Em vigor

---

## Regra Fundamental

> **TODO O DESENVOLVIMENTO NOVO DEVE SER FEITO EM V2**  
> O código legacy (sem prefixo `/v2/`) é mantido apenas para compatibilidade retroativa.  
> **NÃO adicionar novas features ao código legacy.**

---

## Separação: V2 vs Legacy

### 🟢 V2 - Nova Arquitetura (USAR ESTE)

**Características:**
- ✅ Multi-clube (tenancy) nativo
- ✅ Middleware `ensure.club.context` obrigatório
- ✅ API Resources (formato normalizado)
- ✅ Policies e autorização consistente
- ✅ Services para lógica de negócio
- ✅ Testes obrigatórios

**Estrutura:**
```
Routes:        /api/v2/*
Controllers:   App\Http\Controllers\Api\*
Middleware:    ensure.club.context
Resources:     App\Http\Resources\*
Services:      App\Services\*
```

**Módulos V2 Implementados:**

| Módulo | Controller | Status | Endpoints |
|--------|-----------|--------|-----------|
| **Clubes** | `ClubSwitchController` | ✅ Completo | GET /clubs, POST /clubs/switch |
| **Membros** | `Api\MembrosController` | 🟡 Parcial | GET/POST /v2/membros, GET /v2/membros/{id} |
| **Faturas** | `Api\FaturasController` | 🟡 Parcial | GET/POST /v2/faturas, POST /v2/faturas/gerar-mensalidades |
| **Conta Corrente** | `Api\FaturasController` | ✅ Completo | GET /v2/membros/{id}/conta-corrente |

**Pendentes em V2:**
- [ ] Atletas
- [ ] Grupos
- [ ] Treinos
- [ ] Eventos
- [ ] Inventário
- [ ] Comunicação

---

### 🔴 Legacy - Compatibilidade (NÃO DESENVOLVER AQUI)

**Características:**
- ❌ Sem tenancy consistente
- ❌ Retorna models diretamente (sem Resources)
- ❌ Autorização inconsistente
- ❌ Sem testes
- ⚠️ Mantido apenas para não quebrar clientes existentes

**Estrutura:**
```
Routes:        /api/* (sem /v2/)
Controllers:   App\Http\Controllers\*Controller (sem Api\)
```

**Módulos Legacy (NÃO TOCAR):**

| Módulo | Controller | Deprecação |
|--------|-----------|-----------|
| Pessoas | `PessoaController` | Migrar para v2 quando possível |
| Membros | `MembroController` | ✅ Substituído por `Api\MembrosController` |
| Tipos Membro | `TipoMembroController` | Manter |
| Atletas | `AtletaController` | Migrar para v2 |
| Equipas | `EquipaController` | Migrar para v2 |
| Treinos | `TreinoController` | Migrar para v2 |
| Competições | `CompeticaoController` | Migrar para v2 |
| Faturas | `FaturaController` | ✅ Substituído por `Api\FaturasController` |
| Pagamentos | `PagamentoController` | Migrar para v2 |
| Eventos | `EventoController` | Migrar para v2 |
| Documentos | `DocumentoController` | Migrar para v2 |

---

## Plano de Migração Legacy → V2

### Prioridade 1 (Q1 2026)
- [ ] Atletas → `Api\AtletasController`
- [ ] Grupos → `Api\GruposController`
- [ ] Treinos → `Api\TreinosController`

### Prioridade 2 (Q2 2026)
- [ ] Eventos → `Api\EventosController`
- [ ] Pagamentos → Integrar em `Api\FaturasController`
- [ ] Documentos → `Api\DocumentosController`

### Prioridade 3 (Q3 2026)
- [ ] Inventário → `Api\InventarioController`
- [ ] Comunicação → `Api\ComunicacaoController`

### Deprecação Final (Q4 2026)
- [ ] Anunciar deprecação oficial dos endpoints legacy
- [ ] Notificar clientes (se houver)
- [ ] Remover código legacy após período de transição

---

## Checklist: Criar Nova Feature em V2

Quando criar uma nova funcionalidade, seguir esta checklist:

### 1. Backend

- [ ] **Controller** em `App\Http\Controllers\Api\`
  - Extender `Controller`
  - Injetar Services necessários
  - Injetar `ClubContext`

- [ ] **Routes** em `routes/api.php`
  - Prefixo `/api/v2/`
  - Middleware: `['auth:sanctum', 'ensure.club.context']`

- [ ] **Service** em `App\Services\{Modulo}\`
  - Lógica de negócio
  - Validações complexas
  - Transações

- [ ] **Resource** em `App\Http\Resources\`
  - `{Entity}Resource` para item
  - `{Entity}Collection` para lista (se necessário)

- [ ] **Policy** em `App\Policies\`
  - `viewAny`, `view`, `create`, `update`, `delete`
  - Verificar clube + permissões

- [ ] **Form Request** em `App\Http\Requests\`
  - Validação de input
  - Autorização

- [ ] **Testes**
  - Feature test do controller
  - Unit test do service
  - Test de policy

### 2. Frontend

- [ ] **Types** em `frontend/src/types/`
  - Interface da entidade
  - Response types

- [ ] **API Client** em `frontend/src/api/`
  - Métodos CRUD
  - Usar endpoint v2

- [ ] **Componentes** em `frontend/src/modules/{modulo}/`
  - Listagem
  - Formulário
  - Detalhes

---

## Guidelines de Desenvolvimento

### Backend

1. **Nunca retornar models diretamente**
   ```php
   // ❌ ERRADO
   return response()->json($membro);
   
   // ✅ CORRETO
   return new MembroResource($membro);
   ```

2. **Sempre autorizar**
   ```php
   // ✅ No início do método
   $this->authorize('viewAny', Membro::class);
   ```

3. **Sempre filtrar por club_id**
   ```php
   // ✅ Usar ClubContext
   $clubId = $this->clubContext->getActiveClubId();
   $query->where('club_id', $clubId);
   ```

4. **Lógica em Services**
   ```php
   // ❌ ERRADO - lógica no controller
   public function store(Request $request) {
       $user = User::create(...);
       $membro = Membro::create(...);
       // ... 50 linhas de código
   }
   
   // ✅ CORRETO - lógica no service
   public function store(Request $request) {
       $membro = $this->membroService->criarMembro($request->validated());
       return new MembroResource($membro);
   }
   ```

### Frontend

1. **Sempre usar tipos TypeScript**
   ```typescript
   // ✅ CORRETO
   interface Membro {
       id: number;
       user_id: number;
       club_id: number;
       // ...
   }
   ```

2. **Usar Context para estado global**
   ```typescript
   // ✅ CORRETO
   const { activeClub } = useClubContext();
   ```

3. **Tratar erros da API**
   ```typescript
   // ✅ CORRETO
   try {
       await api.post('/v2/membros', data);
       toast.success('Membro criado');
   } catch (error) {
       toast.error(error.message);
   }
   ```

---

## FAQ

### Por que criar v2 em vez de corrigir o legacy?

O código legacy tem problemas estruturais que não podem ser corrigidos sem breaking changes:
- Falta de tenancy consistente
- Models expostos diretamente na API
- Autorização inconsistente
- Difícil de testar

Criar v2 permite:
- Arquitetura correta desde o início
- Manter compatibilidade com código antigo
- Migração gradual

### Quando posso usar endpoints legacy?

**Apenas para leitura** em features antigas. Nunca para criar/editar dados.

### Como sei se um endpoint é v2 ou legacy?

- Se começa com `/api/v2/` → V2 ✅
- Se começa com `/api/` (sem v2) → Legacy ❌

### Posso misturar v2 e legacy?

Não. Uma feature deve estar 100% em v2 ou 100% em legacy. Não misturar.

### Quando o legacy será removido?

Planeado para Q4 2026, após período de transição de 6 meses.

---

## Suporte

Para dúvidas sobre versionamento:
- Consultar: `ESTADO_ATUAL_DO_SISTEMA.md` secção 11 (Roadmap)
- Consultar: `ClubManager_SPEC_DEFINITIVA_Copilot_Rewrite.md`

**Última atualização:** 22 de Janeiro de 2026
