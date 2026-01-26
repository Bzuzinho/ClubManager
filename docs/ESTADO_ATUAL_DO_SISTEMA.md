# Estado Atual do Sistema ClubManager

**Data da última atualização:** 23 de Janeiro de 2026  
**Versão do Laravel:** 12.42.0  
**Versão do PHP:** 8.2+  
**Versão do React:** 19.2.0  
**Versão do Spatie Permission:** 6.24  
**Versão do Laravel Sanctum:** 4.2

---

## Índice

1. [Visão Geral](#1-visão-geral)
2. [Arquitetura do Sistema](#2-arquitetura-do-sistema)
3. [Configurações do Backend](#3-configurações-do-backend)
4. [Base de Dados](#4-base-de-dados)
5. [Models e Relacionamentos](#5-models-e-relacionamentos)
6. [Routes e Controllers](#6-routes-e-controllers)
7. [Services e Business Logic](#7-services-e-business-logic)
8. [Frontend (React)](#8-frontend-react)
9. [CRUDs Implementados](#9-cruds-implementados)
10. [Erros e Problemas Críticos Identificados](#10-erros-e-problemas-críticos-identificados)
11. [Roadmap Priorizado](#11-roadmap-priorizado-ordem-de-execução)
12. [Tarefas Pendentes (Categorização Antiga)](#12-tarefas-pendentes-categorização-antiga)
13. [Checklist de Qualidade](#13-checklist-de-qualidade-antes-de-deploy)
14. [Documentação Relacionada](#14-documentação-relacionada)
15. [Regras de Desenvolvimento](#15-regras-de-desenvolvimento-team-guidelines)

---

## 1. Visão Geral

O **ClubManager** é um sistema de gestão completo para clubes desportivos, com foco em natação. Implementa uma arquitetura multi-clube (tenancy) desde a base, permitindo que o mesmo sistema gira múltiplos clubes independentes.

### Princípios de Design Implementados

- ✅ **Multi-clube (Tenancy)** - Suporte nativo para múltiplos clubes
- ✅ **Users como base única** - Identidade centralizada em `users`
- ✅ **Membros como perfil** - Um user pode ter múltiplos perfis (um por clube)
- ✅ **Soft Delete controlado** - Apenas em entidades não-críticas
- ✅ **Permissões hierárquicas** - Usando Spatie Permission
- ✅ **Estado financeiro derivado** - Calculado dinamicamente
- ✅ **Índices otimizados** - Em todas as FKs e campos de filtro

### Estado do Projeto

- **Backend:** Estrutura completa implementada com Laravel 12.42.0
- **Frontend:** SPA React 19.2.0 com TypeScript, módulos principais implementados
- **API:** 82+ endpoints RESTful, autenticação Sanctum funcional
- **Database:** 58 migrations criadas e aplicadas
- **Testes:** 91 testes backend + 13 testes unitários frontend + 16 testes E2E
- **DevOps:** CI/CD completo com GitHub Actions, scripts de deploy automatizados
- **Monitoring:** Sentry integrado, logs estruturados, dashboard de desenvolvimento
- **Documentação:** 15+ documentos técnicos detalhados

### Fases Concluídas

1. **FASE 1** - Correções Estruturais Críticas (Normalização, Tenancy, SoftDeletes)
2. **FASE 2** - Backend Production-Ready (Resources, Policies, Controllers v2)
3. **FASE 3** - Testes Backend (91 testes implementados)
4. **FASE 4** - CI/CD e Deploy (3 workflows GitHub Actions)
5. **FASE 5** - Frontend Tests & Monitoring (29 testes, Sentry, logging)
6. **FASE 6** - DevOps e Deploy (Ambientes, backup, monitoring production)

---

## 2. Arquitetura do Sistema

### 2.1 Stack Tecnológica

**Backend:**
- Laravel 12.42.0 (PHP 8.2+)
- Sanctum 4.2 (autenticação API)
- Spatie Laravel Permission 6.24 (roles & permissions)
- SQLite (desenvolvimento) / PostgreSQL (produção - Neon)
- PHPStan Level 5 (análise estática)
- Laravel Pint 1.26 (code style)
- Laravel Pail 1.2.4 (log viewer)

**Frontend:**
- React 19.2.0
- TypeScript 5.9.3
- React Router DOM 7.10.1
- Vite 7.2.4
- Axios 1.13.2
- Lucide React 0.561.0 (ícones)
- Vitest 2.0 (testes unitários)
- Playwright 1.40.0 (testes E2E)
- Sentry 7.90.0 (error tracking & monitoring)

**DevOps:**
- Dev Container (Debian GNU/Linux 13 - Trixie)
- Node.js 20+, npm, ESLint pré-instalados
- GitHub Actions (3 workflows de CI/CD)
- Scripts automatizados (bootstrap, deploy, backup, rollback)


### 2.2 Estrutura de Diretórios

```
ClubManager/
├── backend/          # API Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/    # Controllers (novos e legacy)
│   │   │   ├── Middleware/     # EnsureClubContext
│   │   │   └── Requests/       # Form Requests
│   │   ├── Models/             # Eloquent Models (60+ models)
│   │   └── Services/           # Business Logic
│   │       ├── Financeiro/
│   │       ├── Membros/
│   │       ├── Inventario/
│   │       └── Tenancy/
│   ├── database/
│   │   ├── migrations/         # 58 migrations (2026_01_22_*)
│   │   ├── seeders/            # Seeders principais
│   │   └── setup_all.sql       # Script SQL completo
│   ├── routes/
│   │   └── api.php             # Rotas API (v2 + legacy)
│   └── config/                 # Configurações Laravel
│
├── frontend/         # SPA React
│   └── src/
│       ├── modules/            # Módulos funcionais
│       │   ├── members/
│       │   ├── financial/
│       │   ├── sports/
│       │   └── events/
│       ├── views/              # Views principais
│       ├── components/         # Componentes reutilizáveis
│       ├── router/             # React Router config
│       └── auth/               # Autenticação
│
└── docs/             # Documentação
    ├── ClubManager_SPEC_DEFINITIVA_Copilot_Rewrite.md
    ├── REFATORACAO_2026_01_22.md
    └── ESTADO_ATUAL_DO_SISTEMA.md (este ficheiro)
```

### 2.3 Módulos do Sistema

1. **Core/Auth** - Autenticação, users, roles, sessions (✅ Implementado)
2. **Clube & Configuração** - Clubes, escalões, provas, mensalidades (✅ Implementado)
3. **Pessoas/Membros** - Ficha, RGPD, relações, tipos (✅ Implementado)
4. **Desportivo** - Atletas, épocas, planeamento, resultados (✅ Scaffolded)
5. **Atividades & Eventos** - Grupos, treinos, presenças, eventos (✅ Scaffolded)
6. **Financeiro** - Faturas, itens, pagamentos, conta corrente (✅ Implementado)
7. **Inventário** - Artigos, materiais, stock, empréstimos (📋 Estrutura DB)
8. **Comunicação** - Templates, segmentos, campanhas, envios (📋 Estrutura DB)
9. **Dashboards/Relatórios** - KPIs, exports (🚧 Frontend básico)
10. **Documentos & Auditoria** - Uploads, logs (✅ Models criados)

**Legenda:**
- ✅ Implementado - Backend + Frontend funcional
- ✅ Scaffolded - Controllers e rotas criados, frontend básico
- 📋 Estrutura DB - Migrations e models criados
- 🚧 Em desenvolvimento

---

## 3. Configurações do Backend

### 3.1 Variáveis de Ambiente (.env)

```env
APP_NAME=ClubManager
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=clubmanager
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
```

### 3.2 Dependências Principais (composer.json)

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.42.0",
    "laravel/sanctum": "^4.2",
    "laravel/tinker": "^2.10.1",
    "spatie/laravel-permission": "^6.24"
  },
  "require-dev": {
    "fakerphp/faker": "^1.24.1",
    "larastan/larastan": "^2.9",
    "laravel/pail": "^1.2.4",
    "laravel/pint": "^1.26",
    "laravel/sail": "^1.51",
    "phpunit/phpunit": "^11.5.3"
  }
}
```

**Dependências Notáveis:**
- **Spatie Permission** - Gestão completa de roles e permissões
- **Larastan (PHPStan)** - Análise estática de código (Level 5)
- **Laravel Pint** - Code formatter baseado em PHP CS Fixer
- **Laravel Pail** - Real-time log viewer para desenvolvimento


### 3.3 Scripts Composer Disponíveis

```bash
composer setup           # Instala tudo, gera key, faz migrate e build frontend
composer dev            # Inicia servidor, queue, logs e vite (concurrently)
composer test           # Executa testes PHPUnit
composer test:coverage  # Testes com cobertura mínima 80%
composer analyse        # PHPStan level 5 analysis
composer format         # Laravel Pint (formata código)
composer format:test    # Verifica formatação sem alterar
composer ci             # Pipeline completo (format:test + analyse + test:coverage)
```

**Script composer dev:**
Executa 4 processos em paralelo usando `npx concurrently`:
1. `php artisan serve` - API server (porta 8000)
2. `php artisan queue:listen` - Queue worker
3. `php artisan pail` - Real-time log viewer
4. `npm run dev` - Vite dev server (porta 5173)


### 3.4 Middleware Customizado

- **EnsureClubContext** - Garante que o utilizador tem um clube ativo na sessão
  - Localização: `app/Http/Middleware/EnsureClubContext.php`
  - Aplicado em rotas v2 do clube

---

## 4. Base de Dados

### 4.1 Estratégia de Migrations

Todas as migrations seguem nomenclatura ordenada: `2026_01_22_NNNNNN_create_*_table.php`

**Ordem de criação (por dependências):**

1. **Core (000001-000006)** - clubs, users, club_users, tokens, permissions, jobs
2. **Configuração (000100-000113)** - escalões, tipos, provas, mensalidades, bancos, etc.
3. **Pessoas (000200-000204)** - dados_pessoais, membros, relações
4. **Desportivo (000300-000306)** - atletas, dados_desportivos, escalões, ciclos
5. **Atividades (000400-000407)** - grupos, treinos, presenças, eventos, resultados
6. **Financeiro (000500-000505)** - dados_financeiros, faturas, itens, pagamentos
7. **Inventário (000600-000603)** - materiais, stock, empréstimos, manutenções
8. **Comunicação (000700-000703)** - templates, segmentos, campanhas, envios
9. **Documentos (000800-000802)** - ficheiros, entidade_ficheiros, auditoria
10. **Foreign Keys Finais (000900)** - Constraints adicionais

### 4.2 Tabelas Principais (58 tabelas)

#### Core/Tenancy
- `clubs` - Clubes do sistema
- `users` - Utilizadores (base única de identidade)
- `club_users` - Associação user ↔ clube (many-to-many)
- `personal_access_tokens` - Tokens Sanctum
- `roles`, `permissions`, `model_has_roles`, etc. - Spatie Permission
- `jobs`, `job_batches`, `failed_jobs` - Queue system

#### Configuração
- `escaloes` - Escalões etários
- `tipos_utilizador` - Tipos funcionais (Atleta, Encarregado, Treinador, etc.)
- `provas` - Provas/modalidades
- `mensalidades` - Tabela de mensalidades por escalão
- `bancos` - Bancos do clube
- `centros_custo` - Centros de custo
- `patronos` - Patronos/patrocinadores
- `fornecedores` - Fornecedores
- `armazens` - Armazéns
- `categorias_artigos` - Categorias de artigos
- `artigos` - Artigos de inventário
- `notificacoes_tipos` - Tipos de notificação
- `notificacoes_config` - Configuração de notificações
- `notificacoes_emails_envio` - Emails para envio

#### Pessoas/Membros
- `dados_pessoais` - Dados pessoais do user (1:1)
- `membros` - Perfil de membro num clube (unique: club_id + user_id)
- `dados_configuracao` - Configurações pessoais
- `user_tipos_utilizador` - Tipos do user num clube (many-to-many)
- `relacoes_users` - Relações entre users (encarregado → educando)

#### Desportivo
- `atletas` - Perfil de atleta (extends membro)
- `dados_desportivos` - Dados desportivos do atleta
- `atleta_escaloes` - Histórico de escalões do atleta
- `epocas` - Épocas desportivas
- `macrociclos` - Planeamento macro
- `mesociclos` - Planeamento meso
- `microciclos` - Planeamento micro

#### Atividades/Treinos/Eventos
- `grupos` - Grupos de treino
- `grupo_membros` - Membros dos grupos
- `treinos` - Sessões de treino
- `presencas` - Presenças em treinos
- `eventos_tipos` - Tipos de evento
- `eventos` - Eventos (competições, etc.)
- `eventos_participantes` - Participantes em eventos
- `resultados` - Resultados de competições

#### Financeiro
- `dados_financeiros` - Dados financeiros do membro
- `faturas` - Faturas (estado derivado)
- `catalogo_fatura_itens` - Catálogo de itens faturáveis
- `fatura_itens` - Itens de cada fatura
- `pagamentos` - Pagamentos recebidos
- `lancamentos_financeiros` - Lançamentos contabilísticos

#### Inventário
- `materiais` - Materiais e equipamentos
- `movimentos_stock` - Movimentos de stock
- `emprestimos` - Empréstimos de material
- `manutencoes` - Manutenções de equipamento

#### Comunicação
- `modelos_email` - Templates de email
- `segmentos` - Segmentos de comunicação
- `campanhas` - Campanhas de marketing
- `envios` - Histórico de envios

#### Documentos/Auditoria
- `ficheiros` - Ficheiros uploadados
- `entidade_ficheiros` - Associação polimórfica ficheiro ↔ entidade
- `auditoria` - Log de auditoria

### 4.3 Convenções de Índices

✅ **Todas as tabelas seguem:**
- Index em todas as foreign keys
- Index em `club_id` (tabelas multi-clube)
- Index em campos de filtro (estado, ativo, datas)
- Unique constraints compostos com `club_id` quando aplicável

### 4.4 Estratégia de Soft Delete

**❌ NÃO têm softDeletes (entidades críticas):**
- users, membros, faturas, pagamentos, resultados, presencas, lancamentos_financeiros

**✅ TÊM softDeletes (configurações/templates):**
- campanhas, modelos_email, segmentos (opcional)

**Controlo por estados:**
- `ativo` (boolean) - Para configs
- `estado` (string) - Para membros, faturas, eventos
- `data_fim` (date) - Para períodos

### 4.5 Script SQL Completo

Existe um ficheiro `database/setup_all.sql` com todas as tabelas em SQL puro (683 linhas) para setup direto sem migrations.

---

## 5. Models e Relacionamentos

### 5.1 Models Implementados (59 models)

**Localização:** `backend/app/Models/`

#### Core (3 models)
- `Club` - Clube
- `ClubUser` - Ponte user ↔ clube (pivot table)
- `User` - Utilizador (com HasRoles do Spatie)

#### Pessoas (5 models)
- `DadosPessoais` - Dados pessoais (1:1 com User)
- `Membro` - Perfil de membro (belongsTo User, Club)
- `EncarregadoEducacao` - Relação encarregado
- `RelacaoPessoa` - Relações entre users
- `TipoUtilizador` - Tipos funcionais

#### Desportivo (8 models)
- `Atleta` - Perfil atleta (belongsTo Membro)
- `DadosDesportivos` / `DadosDesportivosAtleta` - Dados desportivos
- `AtletaEscalao` - Histórico de escalões
- `Epoca`, `Macrociclo`, `Mesociclo`, `Microciclo` - Planeamento

#### Atividades/Eventos (11 models)
- `Grupo` - Grupos de treino
- `GrupoMembro` - Membros dos grupos (pivot)
- `Treino` - Sessões de treino
- `Presenca` / `PresencaTreino` - Presenças
- `Evento` - Eventos gerais
- `EventoTipo` / `TipoEvento` - Tipos de evento
- `EventoParticipante` / `InscricaoEvento` - Participantes
- `Resultado` - Resultados de competições
- `Competicao` - Competições (extends Evento)
- `Convocatoria` - Convocatórias

#### Financeiro (10 models)
- `DadosFinanceiros` - Dados financeiros do membro
- `Fatura` - Faturas (estado derivado)
- `FaturaItem` / `ItemFatura` - Itens de fatura
- `CatalogoFaturaItem` - Catálogo de itens faturáveis
- `Pagamento` - Pagamentos recebidos
- `MovimentoFinanceiro` - Lançamentos contabilísticos
- `ContaBancaria` - Contas bancárias
- `Banco` - Bancos
- `MetodoPagamento` - Métodos de pagamento
- `Mensalidade` - Tabela de mensalidades

#### Configuração (9 models)
- `Escalao` - Escalões etários
- `Prova` - Provas/modalidades
- `CentroCusto` - Centros de custo
- `Patrono` - Patronos/patrocinadores
- `Modalidade` - Modalidades desportivas
- `NotificacaoTipo` - Tipos de notificação
- `NotificacaoConfig` - Configuração de notificações
- `MembroTipo` / `TipoMembro` - Tipos de membro
- `Equipa` - Equipas

#### Inventário (modelo base)
- `Artigo` - Artigos de inventário

#### Documentos/Auditoria (5 models)
- `Ficheiro` - Ficheiros uploadados
- `Documento` - Documentos gerais
- `TipoDocumento` - Tipos de documento
- `Consentimento` - Consentimentos RGPD
- `HistoricoEstado` - Histórico de alterações de estado

**Total: 59 Models** (contando aliases/duplicados como modelos separados no filesystem)


### 5.2 Relacionamentos Principais

**User (base de identidade):**
```php
// User.php
hasOne(DadosPessoais::class)
hasMany(Membro::class)           // múltiplos perfis (1 por clube)
belongsToMany(Club::class, 'club_users')
hasMany(ClubUser::class)
```

**Membro (perfil por clube):**
```php
// Membro.php
belongsTo(User::class)
belongsTo(Club::class)
hasOne(Atleta::class)
hasOne(DadosFinanceiros::class)
hasMany(Fatura::class)
hasMany(Presenca::class)
belongsToMany(Grupo::class, 'grupo_membros')
```

**Atleta (perfil desportivo):**
```php
// Atleta.php
belongsTo(Membro::class)
hasOne(DadosDesportivos::class)
hasMany(AtletaEscalao::class)
hasMany(Resultado::class)
```

**Fatura (financeiro):**
```php
// Fatura.php
belongsTo(Membro::class)
belongsTo(Club::class)
hasMany(FaturaItem::class, 'fatura_id')  // nome corrigido
hasMany(Pagamento::class)

// Estado derivado
public function getEstadoPagamentoAttribute()
{
    $totalPago = $this->pagamentos->sum('valor');
    if ($totalPago >= $this->valor_total) return 'pago';
    if ($totalPago > 0) return 'parcial';
    if ($this->data_vencimento < now()) return 'atraso';
    return 'pendente';
}
```

### 5.3 Scopes Úteis

```php
// Membro
Membro::ativos()->get();          // where('estado', 'ativo')
Membro::inativos()->get();        // where('estado', 'inativo')

// Fatura (quando implementado)
Fatura::pendentes()->get();       // where('status_cache', 'pendente')
Fatura::emAtraso()->get();        // where('status_cache', 'atraso')
```

---

## 6. Routes e Controllers

### 6.1 Estrutura de Rotas (api.php)

O sistema tem **duas versões de API** para manter compatibilidade:

#### Rotas de Autenticação (públicas)
```php
POST /api/login
POST /api/register
```

#### Rotas Protegidas (auth:sanctum)
```php
GET  /api/me
POST /api/logout
```

#### Nova Arquitetura (v2 - com ClubContext)

**Gestão de Clubes:**
```php
GET  /api/clubs              # Listar clubes do user
POST /api/clubs/switch       # Trocar de clube ativo
GET  /api/clubs/active       # Obter clube ativo
POST /api/clubs/clear        # Limpar clube ativo
```

**Membros (v2):**
```php
GET    /api/v2/membros                        # Listar
POST   /api/v2/membros                        # Criar
GET    /api/v2/membros/{id}                   # Detalhes
PUT    /api/v2/membros/{id}                   # Atualizar
DELETE /api/v2/membros/{id}                   # Eliminar
```

**Faturas (v2):**
```php
GET  /api/v2/faturas                          # Listar
POST /api/v2/faturas                          # Criar fatura avulsa
GET  /api/v2/faturas/{id}                     # Detalhes
POST /api/v2/faturas/gerar-mensalidades       # Gerar faturas de mensalidade
POST /api/v2/faturas/{id}/itens               # Adicionar item
POST /api/v2/faturas/{id}/pagamentos          # Registar pagamento
```

**Conta Corrente:**
```php
GET /api/v2/membros/{membroId}/conta-corrente
GET /api/v2/membros/{membroId}/resumo-financeiro
```

#### Rotas Legacy (manter compatibilidade)

**Pessoas:**
```php
GET    /api/pessoas
POST   /api/pessoas
GET    /api/pessoas/{id}
PUT    /api/pessoas/{id}
DELETE /api/pessoas/{id}
POST   /api/pessoas/{id}/restore
```

**Membros (legacy):**
```php
GET  /api/membros
POST /api/membros
GET  /api/membros/{id}
PUT  /api/membros/{id}
PUT  /api/membros/{id}/tipos      # Atualizar tipos
```

**Tipos de Membro:**
```php
GET /api/tipos-membro
GET /api/tipos-membro/{id}
```

**Atletas:**
```php
GET  /api/atletas
POST /api/atletas
GET  /api/atletas/{id}
PUT  /api/atletas/{id}
PUT  /api/atletas/{id}/equipas
GET  /api/atletas/{id}/estatisticas
```

**Equipas:**
```php
GET  /api/equipas
POST /api/equipas
GET  /api/equipas/{id}
GET  /api/equipas/{id}/plantel
POST /api/equipas/{id}/atletas
```

**Treinos:**
```php
GET  /api/treinos
POST /api/treinos
GET  /api/treinos/{id}
POST /api/treinos/{id}/presencas
GET  /api/treinos/{id}/estatisticas-presenca
```

**Competições:**
```php
GET  /api/competicoes
POST /api/competicoes
GET  /api/competicoes/{id}
POST /api/competicoes/{id}/convocar
```

**Faturas (legacy):**
```php
GET  /api/faturas
POST /api/faturas
GET  /api/faturas/{id}
POST /api/faturas/{id}/cancelar
```

**Pagamentos:**
```php
POST /api/pagamentos
POST /api/pagamentos/{id}/confirmar
POST /api/pagamentos/{id}/cancelar
```

**Eventos:**
```php
GET  /api/eventos
POST /api/eventos
GET  /api/eventos/{id}
POST /api/eventos/{id}/inscrever
```

**Documentos:**
```php
GET  /api/documentos
POST /api/documentos
GET  /api/documentos/{id}
GET  /api/documentos/{id}/download
POST /api/documentos/{id}/validar
```

### 6.2 Controllers Implementados (16 controllers)

**Localização:** `backend/app/Http/Controllers/`

#### Nova Arquitetura (Api/)
- `AuthController` - Login, register, me, logout
- `MembrosController` (v2) - CRUD completo de membros com ClubContext
- `FaturasController` (v2) - CRUD faturas, geração mensalidades, pagamentos

#### Gestão de Clube
- `ClubSwitchController` - Trocar clube ativo na sessão

#### Controllers Legacy (compatibilidade)
- `PessoaController` - CRUD pessoas
- `MembroController` - CRUD membros (legacy)
- `TipoMembroController` - Listar tipos de membro
- `AtletaController` - CRUD atletas + equipas + estatísticas
- `EquipaController` - CRUD equipas + plantel + adicionar atletas
- `TreinoController` - CRUD treinos + registar presenças + estatísticas
- `CompeticaoController` - CRUD competições + convocar atletas
- `FaturaController` - CRUD faturas (legacy) + cancelar
- `PagamentoController` - Criar, confirmar, cancelar pagamentos
- `EventoController` - CRUD eventos + inscrever participantes
- `DocumentoController` - CRUD documentos + download + validar
- `MemberController` - (placeholder/exemplo)

**Total: 16 Controllers** com 82+ endpoints RESTful


### 6.3 Middleware Aplicado

**Todas as rotas protegidas:**
- `auth:sanctum` - Requer autenticação

**Rotas v2 (nova arquitetura):**
- `auth:sanctum`
- `ensure.club.context` - Requer clube ativo

---

## 7. Services e Business Logic

### 7.1 Estrutura de Services

Os services estão organizados por domínio em `app/Services/`:

```
Services/
├── Tenancy/
│   └── ClubContext.php          # Gestão do clube ativo
├── Membros/
│   └── MembroService.php        # Lógica de criação de membros
├── Financeiro/
│   ├── FaturacaoService.php     # Geração de faturas
│   └── ContaCorrenteService.php # Conta corrente e resumos
└── Inventario/
    └── (a implementar)
```

### 7.2 ClubContext (Tenancy Service)

**Responsabilidade:** Gerir o clube ativo do utilizador na sessão.

```php
// ClubContext.php
class ClubContext
{
    public function setActiveClubId(int $clubId): void
    public function getActiveClubId(): ?int
    public function clearActiveClub(): void
    public function hasActiveClub(): bool
}
```

**Uso nos Controllers:**
```php
$clubId = $this->clubContext->getActiveClubId();
$query->where('club_id', $clubId);
```

### 7.3 MembroService

**Responsabilidade:** Criar membros com toda a lógica associada (user, dados pessoais, tipos).

```php
// MembroService.php
class MembroService
{
    public function criarMembro(array $data): Membro
    {
        // 1. Criar/obter User
        // 2. Criar DadosPessoais
        // 3. Criar Membro
        // 4. Associar tipos_utilizador
        // 5. Criar DadosFinanceiros (se aplicável)
        // 6. Criar Atleta (se tipo Atleta)
    }
}
```

### 7.4 FaturacaoService

**Responsabilidade:** Gerar faturas de mensalidade automaticamente.

```php
// FaturacaoService.php
class FaturacaoService
{
    public function gerarFaturasMensalidade(
        int $membroId,
        string $mesInicio,
        ?string $mesFim = null
    ): array
    {
        // 1. Obter membro e dados financeiros
        // 2. Para cada mês no intervalo:
        //    - Verificar se já existe fatura
        //    - Criar fatura com item de mensalidade
        //    - Calcular valor total
        // 3. Retornar faturas criadas
    }
}
```

### 7.5 ContaCorrenteService

**Responsabilidade:** Calcular saldos e conta corrente.

```php
// ContaCorrenteService.php
class ContaCorrenteService
{
    public function obterContaCorrente(int $membroId): array
    {
        // 1. Obter todas as faturas
        // 2. Obter todos os pagamentos
        // 3. Calcular saldo acumulado
        // 4. Retornar movimentos com saldo progressivo
    }

    public function obterResumoFinanceiro(int $membroId): array
    {
        // Retorna: totalFaturado, totalPago, saldoPendente, etc.
    }
}
```

---

## 8. Frontend (React)

### 8.1 Estrutura do Frontend

```
frontend/src/
├── main.tsx              # Entry point
├── App.tsx               # App principal
├── api.ts                # Axios instance configurado
├── auth/
│   └── RequireAuth.tsx   # HOC para rotas protegidas
├── layouts/
│   └── AppLayout.tsx     # Layout principal com sidebar
├── router/
│   └── index.tsx         # Configuração React Router
├── views/
│   ├── Login.tsx         # Página de login
│   └── Dashboard.tsx     # Dashboard principal
├── modules/              # Módulos funcionais
│   ├── members/
│   │   ├── Members.tsx
│   │   ├── MemberForm.tsx
│   │   └── MemberDetails.tsx
│   ├── financial/
│   │   └── Financial.tsx
│   ├── sports/
│   │   └── Sports.tsx
│   └── events/
│       └── Events.tsx
└── components/           # Componentes reutilizáveis
    ├── Modal.tsx
    ├── Toast.tsx
    └── ConfirmDialog.tsx
```

### 8.2 Dependências Frontend (package.json)

```json
{
  "dependencies": {
    "@sentry/react": "^7.90.0",
    "@sentry/tracing": "^7.90.0",
    "axios": "^1.13.2",
    "lucide-react": "^0.561.0",
    "react": "^19.2.0",
    "react-dom": "^19.2.0",
    "react-router-dom": "^7.10.1"
  },
  "devDependencies": {
    "@playwright/test": "^1.40.0",
    "@testing-library/react": "^16.0.0",
    "@vitejs/plugin-react": "^5.1.1",
    "@vitest/ui": "^2.0.0",
    "typescript": "~5.9.3",
    "vite": "^7.2.4",
    "vitest": "^2.0.0"
  }
}
```

**Dependências Notáveis:**
- **Sentry** - Error tracking e performance monitoring
- **Playwright** - Testes E2E
- **Vitest** - Testes unitários e de integração
- **Testing Library** - Utilitários de teste para React


### 8.3 Rotas Frontend

```tsx
// router/index.tsx
/login                   → Login
/                        → Dashboard (requer auth)
/membros/*               → Members module
/desportivo/*            → Sports module
/eventos/*               → Events module
/financeiro/*            → Financial module
```

### 8.4 API Client (api.ts)

```typescript
// Axios instance com interceptors
// - Adiciona token automaticamente
// - Trata erros 401 (redirect para login)
// - Base URL configurada
```

### 8.5 Estado dos Módulos

| Módulo | Componentes | Estado |
|--------|-------------|--------|
| **Members** | Members, MemberForm, MemberDetails | ✅ Implementado |
| **Financial** | Financial | 🟡 Estrutura criada |
| **Sports** | Sports | 🟡 Estrutura criada |
| **Events** | Events | 🟡 Estrutura criada |
| **Dashboard** | Dashboard | 🟡 Estrutura criada |
| **Auth** | Login, RequireAuth | ✅ Implementado |
| **Layouts** | AppLayout, Sidebar, TopBar, DashboardLayout | ✅ Implementado |
| **Components** | Modal, Toast, ConfirmDialog | ✅ Implementado |

**Legenda:**
- ✅ Implementado e funcional
- 🟡 Estrutura criada, pendente implementação completa
- ❌ Não iniciado

### 8.6 Scripts Disponíveis

```bash
# Desenvolvimento
npm run dev              # Inicia Vite dev server

# Build
npm run build            # TypeScript check + build para produção
npm run preview          # Preview da build

# Qualidade de Código
npm run lint             # ESLint
npm run lint:fix         # ESLint com fix automático
npm run type-check       # TypeScript check sem build
npm run format           # Prettier format
npm run format:check     # Prettier check

# Testes
npm run test             # Vitest (watch mode)
npm run test:ui          # Vitest UI
npm run test:ci          # Vitest CI mode + coverage
npm run test:coverage    # Vitest com coverage

# Testes E2E
npm run test:e2e         # Playwright (headless)
npm run test:e2e:ui      # Playwright com UI
npm run test:e2e:headed  # Playwright com browser
npm run test:e2e:debug   # Playwright debug mode
```

### 8.7 Testes Frontend

#### Testes Unitários/Integração (Vitest)
- **13 testes implementados**
  - `App.test.tsx` (3 testes)
  - `components/Button.test.tsx` (6 testes)
  - `components/MembrosPage.test.tsx` (4 testes)

#### Testes E2E (Playwright)
- **16 testes implementados**
  - `auth.spec.ts` (6 testes) - Login, logout, erros
  - `membros.spec.ts` (10 testes) - CRUD completo de membros

#### Monitoring e Logging
- **Sentry** - Error tracking automático
- **Logger estruturado** - `src/lib/logger.ts`
- **Dashboard de desenvolvimento** - Monitoring widget em modo dev

---

## 9. CRUDs Implementados

### 9.1 CRUDs Completos (Backend + Frontend estruturado)

#### ✅ Membros (v2)
**Backend:** `Api\MembrosController`
- ✅ Listar (com filtros: estado, search)
- ✅ Criar (com validação completa)
- ✅ Mostrar detalhes (com relacionamentos)
- 🟡 Atualizar (controller criado, validação pendente)
- 🟡 Eliminar (controller criado, lógica pendente)

**Frontend:** `modules/members/Members.tsx`
- 🟡 Listar membros
- 🟡 Formulário de criação/edição
- 🟡 Detalhes do membro

#### ✅ Faturas (v2)
**Backend:** `Api\FaturasController`
- ✅ Listar (com filtros: membro, mês, estado)
- ✅ Criar fatura avulsa
- ✅ Gerar faturas de mensalidade (automático)
- ✅ Mostrar detalhes
- ✅ Adicionar item à fatura
- ✅ Registar pagamento

**Frontend:** `modules/financial/Financial.tsx`
- 🟡 Estrutura criada

#### 🟡 Clubes
**Backend:** `ClubSwitchController`
- ✅ Listar clubes do user
- ✅ Trocar clube ativo
- ✅ Obter clube ativo
- ✅ Limpar clube ativo

**Frontend:** 
- ❌ Não iniciado

### 9.2 CRUDs Legacy (manter compatibilidade)

#### Pessoas
- ✅ Backend completo: `PessoaController`
- ❌ Frontend não iniciado

#### Atletas
- ✅ Backend completo: `AtletaController`
- ❌ Frontend não iniciado

#### Equipas
- ✅ Backend completo: `EquipaController`
- ❌ Frontend não iniciado

#### Treinos
- ✅ Backend completo: `TreinoController`
- ❌ Frontend não iniciado

#### Eventos
- ✅ Backend completo: `EventoController`
- ❌ Frontend não iniciado

### 9.3 CRUDs Pendentes

- ❌ Escalões
- ❌ Provas
- ❌ Grupos
- ❌ Resultados
- ❌ Materiais/Inventário
- ❌ Campanhas
- ❌ Templates Email
- ❌ Documentos

---

## 10. Estado da Qualidade e Testes

### 10.1 Testes Backend (91 testes)

**Localização:** `backend/tests/`

#### Distribuição de Testes
- **29 testes de Controllers** (MembrosController, FaturasController)
  - CRUD operations
  - Validação de dados
  - Respostas de erro
  
- **35 testes de Policies** (MembroPolicy, FaturaPolicy)
  - Permissões por role
  - Autorização de acesso
  - Context de clube
  
- **15 testes de Scopes** (ClubScope em 8 models)
  - Isolamento multi-tenancy
  - Filtros automáticos por club_id
  
- **12 testes de Resources**
  - Estrutura JSON
  - Transformação de dados
  - Relacionamentos nested

#### Cobertura
- ✅ 100% cobertura de multi-tenancy isolation
- ✅ Código crítico coberto (financeiro, membros)
- 🟡 Cobertura geral a melhorar (objetivo: 80%+)

#### Scripts de Teste
```bash
cd backend
composer test              # Executa todos os testes
composer test:coverage     # Com cobertura (mínimo 80%)
composer analyse           # PHPStan level 5
composer format:test       # Verifica code style
composer ci                # Pipeline completo
```

### 10.2 Testes Frontend (29 testes)

#### Testes Unitários/Integração - Vitest (13 testes)
- `App.test.tsx` (3 testes)
- `Button.test.tsx` (6 testes)
- `MembrosPage.test.tsx` (4 testes)

**Cobertura:**
- Renderização de componentes
- Estados de loading e erro
- Interações do usuário
- Validação de props

#### Testes E2E - Playwright (16 testes)
- `auth.spec.ts` (6 testes)
  - Login successful
  - Login com credenciais erradas
  - Logout
  - Redirecionamento não autenticado
  
- `membros.spec.ts` (10 testes)
  - CRUD completo de membros
  - Validação de formulários
  - Feedback visual
  - Navegação

**Matrix de Browsers:**
- Desktop: Chrome, Firefox, Safari
- Mobile: Chrome Mobile, Safari Mobile

### 10.3 CI/CD (GitHub Actions)

#### Workflows Implementados (3)

**1. backend-ci.yml**
- Executa em: push, pull_request
- Matrix: PHP 8.2, 8.3
- Services: MySQL
- Steps:
  - Composer install
  - PHPStan analysis
  - Laravel Pint check
  - PHPUnit tests

**2. frontend-ci.yml**
- Executa em: push, pull_request
- Matrix: Node 20.x, 22.x
- Steps:
  - npm install
  - TypeScript check
  - ESLint
  - Vitest tests
  - Build

**3. deploy.yml**
- Executa em: push to main (manual trigger)
- Deploy via SSH
- Backup automático
- Zero-downtime deployment

### 10.4 Code Quality Tools

#### Backend
- **PHPStan** - Level 5 analysis (2GB memory limit)
- **Laravel Pint** - Code formatting (baseado em PHP CS Fixer)
- **PHPUnit** - Test runner (11.5.3)
- **Larastan** - PHPStan para Laravel (2.9)

#### Frontend
- **TypeScript** - Type checking (5.9.3)
- **ESLint** - Linting (9.39.1)
- **Prettier** - Code formatting (integrado)
- **Vitest** - Test runner (2.0)
- **Playwright** - E2E testing (1.40.0)

### 10.5 Monitoring e Observabilidade

#### Error Tracking
- **Sentry** (7.90.0) integrado no frontend
- Captura automática de erros
- Performance monitoring
- Session replay (opcional)

#### Logging
- **Laravel Pail** - Real-time log viewer (backend)
- **Logger estruturado** - Frontend (`src/lib/logger.ts`)
- Níveis: debug, info, warn, error
- Contexto automático (timestamp, component)

#### Development Dashboard
- Widget de monitoring em modo dev
- Métricas em tempo real
- Error counts
- Performance stats

---

## 11. Problemas Identificados e Resolvidos

### 11.1 ✅ RESOLVIDO - Normalização user_id vs membro_id

**Status:** Implementado na FASE 1

**Solução:**
- `users` como base única de identidade
- `membros` como perfil por clube
- Relações claramente definidas
- Documentação atualizada

### 11.2 ✅ RESOLVIDO - Soft Deletes Controlado

**Status:** Implementado na refatoração 2026-01-22

**Solução:**
- Removido `SoftDeletes` de entidades críticas
- Controlo via `estado`, `ativo`, `data_fim`
- Apenas configs/templates mantêm soft deletes

### 11.3 ✅ RESOLVIDO - API Resources

**Status:** 17 Resources implementadas

**Resources criadas:**
- MembroResource
- FaturaResource
- PagamentoResource
- AtletaResource
- DadosPessoaisResource
- DadosFinanceirosResource
- DadosDesportivosResource
- (+ 10 outras)

### 11.4 ✅ RESOLVIDO - Policies e Autorização

**Status:** 2 Policies implementadas + 91 testes

**Policies:**
- `MembroPolicy` - view, create, update, delete
- `FaturaPolicy` - view, create, update, delete

**Autorização:**
- 11 métodos de controller com authorization
- Testes completos de permissões

### 11.5 ✅ RESOLVIDO - Multi-Tenancy (ClubScope)

**Status:** ClubScope implementado em 11 models

**Models com ClubScope:**
- Membro, Fatura, Grupo, Treino, Evento
- Atleta, Equipa, Material, Campanha
- CatalogoFaturaItem, NotificacaoConfig

**Middleware:**
- `EnsureClubContext` em rotas v2
- Validação de clube ativo

### 11.6 🟡 EM PROGRESSO - Cobertura de Testes

**Status Atual:**
- Backend: 91 testes (código crítico coberto)
- Frontend: 29 testes (módulos principais)

**Objetivo:**
- Backend: 80%+ cobertura geral
- Frontend: 70%+ cobertura
- E2E: Fluxos principais todos cobertos

### 11.7 🟡 PENDENTE - Documentação API

**Status:** Postman collection criada (ClubManager-API.postman_collection.json)

**A fazer:**
- OpenAPI/Swagger spec
- Documentação auto-gerada (Scribe/Scramble)
- Exemplos de request/response
- Guia de autenticação

---

## 12. Roadmap e Próximas Fases

### 12.1 FASE 7 - Módulos Desportivo e Eventos (Prioridade Alta)

**Objetivos:**
- Frontend completo para módulo Sports
- Frontend completo para módulo Events
- Integração com API existente
- Testes E2E dos fluxos

**Tasks:**
1. Implementar CRUD de Atletas no frontend
2. Implementar gestão de Equipas
3. Implementar calendário de Treinos
4. Implementar gestão de Eventos
5. Dashboard desportivo com KPIs

### 12.2 FASE 8 - Módulo Financeiro Completo (Prioridade Alta)

**Objetivos:**
- Interface completa de faturação
- Conta corrente por membro
- Relatórios financeiros
- Emissão automática de recibos

**Tasks:**
1. Dashboard financeiro
2. Gestão de faturas (frontend)
3. Conta corrente interativa
4. Relatórios e exports
5. Integração pagamentos MB/MBWAY

### 12.3 FASE 9 - Comunicação e Campanhas (Prioridade Média)

**Objetivos:**
- Sistema de email marketing
- Templates personalizáveis
- Segmentação de membros
- Tracking de envios

**Tasks:**
1. Editor de templates
2. Gestão de segmentos
3. Criação de campanhas
4. Dashboard de métricas

### 12.4 FASE 10 - Inventário e Materiais (Prioridade Média)

**Objetivos:**
- Gestão de stock
- Controlo de empréstimos
- Manutenções

**Tasks:**
1. CRUD de materiais
2. Movimentos de stock
3. Sistema de empréstimos
4. Alertas de manutenção

### 12.5 FASE 11 - Dashboards e Relatórios (Prioridade Alta)

**Objetivos:**
- Dashboards executivos
- KPIs em tempo real
- Relatórios customizáveis
- Exports (PDF, Excel)

**Tasks:**
1. Dashboard executivo
2. Dashboard por módulo
3. Gerador de relatórios
4. Sistema de exports

### 12.6 FASE 12 - Mobile App (Futuro)

**Objetivos:**
- App nativa ou PWA
- Funcionalidades principais
- Offline-first

---
## 13. Scripts de Automação

O projeto inclui vários scripts bash para automação de tarefas comuns:

### 13.1 Scripts Principais

**bootstrap.sh** - Setup inicial completo
- Cria diretórios necessários
- Configura variáveis de ambiente
- Instala dependências backend e frontend
- Executa migrations e seeders

**deploy.sh** - Deploy automatizado
- Backup da base de dados atual
- Pull do código
- Instalação de dependências
- Migrations
- Build do frontend
- Restart de serviços
- Zero-downtime deployment

**backup.sh** - Backup da base de dados
- Dump SQL com timestamp
- Compressão automática
- Retenção configurável
- Suporte para diferentes drivers (SQLite, PostgreSQL, MySQL)

**rollback.sh** - Rollback de migrations
- Desfaz última migration batch
- Backup automático antes do rollback
- Confirmação de segurança

**fix_migrations.sh** - Reset completo de migrations
- Drop de todas as tabelas
- Fresh migrations
- Reseed da base de dados
- **ATENÇÃO:** Apaga todos os dados!

**setup_fase5.sh** - Setup específico da FASE 5
- Configuração de testes
- Instalação de Playwright browsers
- Setup de monitoring

**generate_docs.sh** - Geração de documentação
- API documentation
- Database schema
- Code documentation

### 13.2 Localização

Todos os scripts estão na raiz do projeto (`/workspaces/ClubManager/*.sh`)

---

## 14. Documentação Relacionada

### 14.1 Documentos Principais

**Raiz do Projeto:**
- `README.md` - Documentação principal, setup e features
- `API-README.md` - Documentação da API
- `DOCUMENTATION_GUIDE.md` - Guia de documentação
- `HISTORICO_DESENVOLVIMENTO.md` - Histórico de todas as fases

**Fases Concluídas:**
- `FASE_5_CONCLUIDA.md` - Testes e Monitoring (detalhado)
- `FASE_5_RESUMO.md` - Resumo executivo da FASE 5
- `FASE_6_CONCLUIDA.md` - DevOps e Deploy (detalhado)
- `FASE_6_RESUMO.md` - Resumo executivo da FASE 6

**Diretório docs/:**
- `ClubManager_SPEC_DEFINITIVA_Copilot_Rewrite.md` - Especificação técnica completa
- `REFATORACAO_2026_01_22.md` - Documentação da refatoração estrutural
- `ESTADO_ATUAL_DO_SISTEMA.md` - Este documento
- `GUIA_TESTES_MONITORING.md` - Guia de testes e monitoring
- `CICD.md` - Documentação CI/CD
- `DEPLOYMENT.md` - Guia de deployment
- `VERSIONING.md` - Estratégia de versionamento
- `GUIAS_GRAFICAS.md` - Guias de interface

**Arquivos de Fases Anteriores (docs/Old/):**
- FASE_1_CONCLUIDA.md
- FASE_2_CONCLUIDA.md
- FASE_3_CONCLUIDA.md
- FASE_4_CONCLUIDA.md
- Outros documentos históricos

### 14.2 Postman Collection

- `ClubManager-API.postman_collection.json` - Collection completa da API para importar no Postman

---

## 15. Regras de Desenvolvimento (Team Guidelines)

### 15.1 Arquitetura e Código

1. **Todo desenvolvimento novo em v2**
   - Usar nova arquitetura (Controllers/Api, Services, Resources)
   - Aplicar ClubContext e Policies
   - Legacy apenas para manutenção

2. **Convenções de Nomenclatura**
   - Models: Singular, PascalCase (`Membro`, `Fatura`)
   - Tabelas: Plural, snake_case (`membros`, `faturas`)
   - Controllers v2: Plural (`MembrosController`)
   - Services: Singular + Service (`MembroService`)

3. **Multi-Tenancy Obrigatório**
   - Todas as tabelas operacionais têm `club_id`
   - Sempre filtrar por `club_id` nas queries
   - Usar ClubScope quando aplicável
   - Middleware `ensure.club.context` em rotas v2

4. **Soft Deletes - Regra Clara**
   - ❌ NÃO usar em: users, membros, faturas, pagamentos, resultados
   - ✅ USAR em: templates, campanhas, configs administráveis
   - Preferir: `estado`, `ativo`, `data_fim`

5. **API Responses**
   - Sempre usar JsonResources
   - Nunca retornar models diretamente
   - Incluir meta e links quando apropriado
   - Seguir padrão RESTful

6. **Autorização**
   - Criar Policies para todas as entidades principais
   - Usar `authorize()` em controllers
   - Validar permissões antes de actions
   - Documentar permissões necessárias

### 15.2 Qualidade de Código

1. **Code Style**
   - Backend: Laravel Pint (executar antes de commit)
   - Frontend: ESLint + Prettier
   - Comandos: `composer format`, `npm run lint:fix`

2. **Type Safety**
   - Backend: PHPStan Level 5 (mínimo)
   - Frontend: TypeScript strict mode
   - Zero erros antes de commit

3. **Testes**
   - Todo código crítico deve ter testes
   - Cobertura mínima: 80% (backend), 70% (frontend)
   - E2E para fluxos principais
   - Executar `composer ci` antes de push

4. **Commits**
   - Mensagens descritivas em português
   - Formato: `tipo: descrição`
   - Tipos: feat, fix, refactor, test, docs, chore

### 15.3 Base de Dados

1. **Migrations**
   - Sempre criar migration para alterações
   - Nomenclatura: `YYYY_MM_DD_NNNNNN_action_table.php`
   - Incluir `down()` method
   - Testar rollback

2. **Índices**
   - Todas as FKs devem ter índice
   - `club_id` em todas as tabelas multi-clube
   - Campos de filtro frequentes
   - Índices compostos quando necessário

3. **Seeds**
   - Dados de teste realistas
   - Suportar múltiplos clubes
   - Incluir permissões e roles
   - Idempotentes (podem rodar múltiplas vezes)

### 15.4 Frontend

1. **Componentes**
   - Reutilizáveis e composíveis
   - Props tipadas com TypeScript
   - Documentar props complexas
   - Incluir loading e error states

2. **State Management**
   - Context API para estado global
   - React Query para server state (futuro)
   - Local state quando suficiente

3. **API Integration**
   - Usar `api.ts` centralizado
   - Tratar erros consistentemente
   - Loading states em todas as requests
   - Feedback visual ao usuário

4. **Testes**
   - Componentes: Testing Library
   - E2E: Playwright
   - Coverage mínimo: 70%

### 15.5 DevOps

1. **Ambientes**
   - Development: Local (SQLite)
   - Staging: Neon PostgreSQL
   - Production: PostgreSQL com Redis

2. **CI/CD**
   - Todos os checks devem passar
   - Tests, lint, type-check obrigatórios
   - Deploy automático apenas na main

3. **Monitoring**
   - Sentry configurado em produção
   - Logs estruturados
   - Alertas para erros críticos

---

## 16. Informações Técnicas Adicionais

### 16.1 Configuração de Ambiente

**PostgreSQL (Neon - Production/Staging):**
```env
DATABASE_URL="postgresql://user:pass@host/db?sslmode=require"
DB_CONNECTION=pgsql
```

**SQLite (Development):**
```env
DB_CONNECTION=sqlite
```

**Redis (Production - Future):**
```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 16.2 Portas Padrão

- Backend API: `8000`
- Frontend Dev: `5173`
- PostgreSQL: `5432`
- Redis: `6379`

### 16.3 Credenciais de Teste

**Admin:**
- Email: admin@admin.pt
- Password: password

### 16.4 Estrutura de Permissões

**Roles:**
- `super-admin` - Acesso total
- `admin` - Gestão completa do clube
- `secretaria` - Operações do dia-a-dia
- `treinador` - Módulo desportivo
- `encarregado` - Visualização de dados próprios

**Permissions Pattern:**
- `{modulo}.{acao}` (ex: `membros.ver`, `financeiro.editar`)

---

## 17. Resumo Executivo

### 17.1 Estado Geral do Projeto

✅ **Infraestrutura Sólida**
- Backend Laravel 12 production-ready
- Frontend React 19 com TypeScript
- 58 migrations implementadas
- 59 models criados
- 16 controllers funcionais
- 82+ endpoints API

✅ **Qualidade de Código**
- 91 testes backend (controllers, policies, scopes, resources)
- 29 testes frontend (13 unitários + 16 E2E)
- PHPStan Level 5 configurado
- CI/CD com GitHub Actions
- Code formatters (Pint, ESLint, Prettier)

✅ **Arquitetura Moderna**
- Multi-tenancy implementado
- API Resources para respostas normalizadas
- Policies para autorização
- Services para business logic
- ClubScope para isolamento de dados

✅ **DevOps e Monitoring**
- 3 workflows GitHub Actions
- Scripts de deploy e backup automatizados
- Sentry integrado
- Logs estruturados
- Dashboard de desenvolvimento

### 17.2 Módulos por Estado

| Módulo | Backend | Frontend | Testes | Status |
|--------|---------|----------|--------|--------|
| Auth | ✅ | ✅ | ✅ | Completo |
| Clubes | ✅ | ✅ | ✅ | Completo |
| Membros | ✅ | ✅ | ✅ | Completo |
| Financeiro | ✅ | 🟡 | ✅ | Backend OK |
| Desportivo | ✅ | 🟡 | 🟡 | API OK |
| Eventos | ✅ | 🟡 | 🟡 | API OK |
| Inventário | 📋 | ❌ | ❌ | DB apenas |
| Comunicação | 📋 | ❌ | ❌ | DB apenas |
| Documentos | ✅ | ❌ | ❌ | API OK |

### 17.3 Próximos Passos Prioritários

1. **Frontend dos módulos Desportivo e Eventos** (FASE 7)
2. **Frontend completo do módulo Financeiro** (FASE 8)
3. **Aumentar cobertura de testes para 80%+**
4. **Documentação API (OpenAPI/Swagger)**
5. **Módulos Comunicação e Inventário** (FASE 9-10)

### 17.4 Métricas do Projeto

- **Linhas de Código Backend:** ~15,000+
- **Linhas de Código Frontend:** ~3,000+
- **Total de Testes:** 120 (91 backend + 29 frontend)
- **Migrations:** 58
- **Models:** 59
- **Controllers:** 16
- **API Resources:** 17
- **Endpoints API:** 82+
- **Documentos Técnicos:** 15+

---

**Documento mantido por:** Equipa de Desenvolvimento ClubManager  
**Última revisão completa:** 23 de Janeiro de 2026  
**Próxima revisão prevista:** Após FASE 7  - Listar o que é v2 vs legacy (por módulo)
  - Regra clara: novas features só em v2
  - Deprecation plan do legacy

- [ ] **Atualizar `README.md`** com aviso
  - "Development: Use v2 routes only"
  - Link para `VERSIONING.md`

#### 1.2 Auditar e Corrigir SoftDeletes

- [ ] **Remover `SoftDeletes` de models críticos:**
  - [ ] `Membro` → usar `estado` ('ativo'|'inativo'|'suspenso') + `data_fim`
  - [ ] `Fatura` → usar `status_cache` (estado derivado)
  - [ ] `Pagamento` → nunca apaga (histórico financeiro)
  - [ ] `Pessoa` / `User` → nunca apaga
  - [ ] `Atleta` → herdar controlo do Membro
  - [ ] `Resultado` → nunca apaga (histórico desportivo)
  - [ ] `Presenca` → nunca apaga (histórico de treino)
  - [ ] `MovimentoFinanceiro` → nunca apaga

- [ ] **Confirmar SoftDeletes apenas em:**
  - [ ] `ModeloEmail`, `Campanha`, `Segmento` (templates/marketing)
  - [ ] Opcionalmente: `Treino`, `Evento` (administráveis)

- [ ] **Atualizar migrations** se necessário (remover `softDeletes()`)

#### 1.3 Normalizar user_id vs membro_id (100%)

- [ ] **Auditar todas as tabelas:**
  - [ ] `eventos_participantes` → usar `membro_id` (contexto clube)
  - [ ] `envios` (comunicação) → verificar se é `user_id` ou `membro_id`
  - [ ] `notificacoes_emails_envio` → verificar contexto
  - [ ] `grupo_membros` → confirmar `membro_id` ✅
  - [ ] `presencas` → confirmar `membro_id` ✅
  - [ ] `relacoes_users` → confirmar `user_id` (relação familiar) ✅

- [ ] **Criar migrations de correção** se necessário

- [ ] **Atualizar models** com relacionamentos corretos

- [ ] **Documentar regra** em `ESTADO_ATUAL_DO_SISTEMA.md`:
  - `user_id` = identidade/pessoa (dados pessoais, relações familiares)
  - `membro_id` = perfil no clube (financeiro, desportivo, atividades)

#### 1.4 Garantir Tenancy em Todo o Código

- [ ] **Criar Global Scopes** para forçar `club_id`:
  - [ ] Criar `ClubScope` trait
  - [ ] Aplicar em todos os models operacionais
  - [ ] Exceções: `User`, `Club`, `DadosPessoais` (globais)

- [ ] **Auditar controllers legacy:**
  - [ ] Garantir filtro `club_id` em TODAS as queries
  - [ ] Adicionar validação de contexto de clube

- [ ] **Criar testes** para tenancy:
  - [ ] Tentar aceder dados de outro clube (deve falhar)
  - [ ] Verificar isolamento entre clubes

---

### 🟡 FASE 2 - Backend Production-Ready

**Objetivo: API v2 completa, segura e testada.**

#### 2.1 API Resources (Normalizar Respostas)

- [ ] **Criar Resources para entidades principais:**
  - [ ] `MembroResource` (com dados pessoais, financeiros, tipos)
  - [ ] `FaturaResource` (com itens, pagamentos, estado derivado)
  - [ ] `PagamentoResource`
  - [ ] `AtletaResource` (com dados desportivos, escalões)
  - [ ] `GrupoResource` (com membros)
  - [ ] `TreinoResource` (com presenças)
  - [ ] `EventoResource` (com participantes)
  - [ ] `UserResource` (sem campos sensíveis)
  - [ ] `ClubResource`

- [ ] **Atualizar controllers v2** para usar Resources
- [ ] **Criar Resources para collections** (`MembroCollection`, etc.)
- [ ] **Padronizar formato de erro** (ErrorResource)

#### 2.2 Policies e Autorização Consistente

- [ ] **Criar Policies:**
  - [ ] `MembroPolicy` (viewAny, view, create, update, delete)
  - [ ] `FaturaPolicy`
  - [ ] `PagamentoPolicy`
  - [ ] `AtletaPolicy`
  - [ ] `EventoPolicy`
  - [ ] `TreinoPolicy`

- [ ] **Aplicar em Controllers v2:**
  ```php
  $this->authorize('viewAny', Membro::class);
  ```

- [ ] **Criar testes de autorização:**
  - [ ] User sem permissão não acede
  - [ ] User de clube A não vê dados de clube B
  - [ ] Roles corretas têm acesso

#### 2.3 Completar Controllers v2

- [ ] **MembrosController:**
  - [ ] `update()` - com validação e service
  - [ ] `destroy()` - marcar como inativo (não apagar)
  - [ ] Adicionar filtros avançados
  - [ ] Adicionar exports (CSV/PDF)

- [ ] **FaturasController:**
  - [ ] `update()` - permitir edição de faturas não pagas
  - [ ] `destroy()` - cancelar fatura (não apagar)
  - [ ] Endpoint de reenvio de notificação
  - [ ] Endpoint de download PDF

- [ ] **Criar novos controllers v2:**
  - [ ] `AtletasController`
  - [ ] `GruposController`
  - [ ] `TreinosController`
  - [ ] `EventosController`
  - [ ] `InventarioController`

#### 2.4 Índices e Performance

- [ ] **Verificar índices em migrations:**
  - [ ] Todas as FKs têm índice ✅ (já tem)
  - [ ] `club_id` em todas as tabelas ✅ (já tem)
  - [ ] Colunas de filtro (datas, estados) ✅ (já tem na maioria)

- [ ] **Adicionar índices faltantes:**
  - [ ] `faturas.status_cache` (se materializado)
  - [ ] `faturas.data_vencimento`
  - [ ] `membros.numero_socio`
  - [ ] Outros identificados via slow query log

- [ ] **Criar índices compostos** onde faz sentido:
  - [ ] `(club_id, estado)` em tabelas de estados
  - [ ] `(club_id, data)` em tabelas de eventos

---

### 🟢 FASE 3 - Testes (Airbag do Projeto)

**Objetivo: Cobertura mínima de testes em código crítico.**

#### 3.1 Testes de Services Críticos

- [ ] **MembroService:**
  - [ ] Teste: criar membro com user novo
  - [ ] Teste: criar membro com user existente
  - [ ] Teste: associar tipos de utilizador
  - [ ] Teste: criar atleta automaticamente
  - [ ] Teste: validar unique (club + user)

- [ ] **FaturacaoService:**
  - [ ] Teste: gerar fatura de mensalidade
  - [ ] Teste: gerar faturas em lote (vários meses)
  - [ ] Teste: não duplicar faturas existentes
  - [ ] Teste: criar fatura avulsa
  - [ ] Teste: adicionar item a fatura

- [ ] **ContaCorrenteService:**
  - [ ] Teste: calcular saldo correto
  - [ ] Teste: identificar faturas em atraso
  - [ ] Teste: estado derivado (pago/parcial/pendente)
  - [ ] Teste: resumo financeiro

#### 3.2 Testes de Models e Relacionamentos

- [ ] **Relacionamentos:**
  - [ ] User → Membros (múltiplos clubes)
  - [ ] Membro → Atleta (1:1)
  - [ ] Fatura → Itens → Pagamentos
  - [ ] Grupo → Membros (many-to-many)
  - [ ] Evento → Participantes

#### 3.3 Testes de Autorização

- [ ] **Tenancy:**
  - [ ] User não vê dados de outro clube
  - [ ] Trocar clube ativo funciona
  - [ ] Queries sempre filtram por club_id

- [ ] **Permissions:**
  - [ ] Role 'admin' tem acesso total
  - [ ] Role 'secretaria' tem acesso limitado
  - [ ] User sem role não acede

---

### 🟣 FASE 4 - Frontend Production-Ready

**Objetivo: UI consistente e funcional para módulos principais.**

#### 4.1 Estado Global e Autenticação

- [ ] **Implementar Context API:**
  - [ ] `AuthContext` (user, token, login, logout)
  - [ ] `ClubContext` (clube ativo, trocar clube)
  - [ ] `PermissionsContext` (can, roles)

- [ ] **Persistência:**
  - [ ] Token em `localStorage`
  - [ ] Clube ativo em `sessionStorage`
  - [ ] Refresh automático de token

- [ ] **Interceptors Axios:**
  - [ ] Adicionar token automaticamente
  - [ ] Adicionar `X-Club-Id` header
  - [ ] Tratar 401 (redirect login)
  - [ ] Tratar 403 (sem permissão)

#### 4.2 Sistema de Design e Componentes

- [ ] **Criar biblioteca de componentes base:**
  - [ ] `Button` (variants, sizes, loading)
  - [ ] `Input` (text, number, date, select)
  - [ ] `DataTable` (paginação, filtros, sort)
  - [ ] `Modal` (melhorar o existente)
  - [ ] `Toast` (melhorar o existente)
  - [ ] `Card`, `Badge`, `Tabs`
  - [ ] `DatePicker`, `Select com Search`

- [ ] **Definir tema:**
  - [ ] Cores (primary, secondary, danger, etc.)
  - [ ] Typography
  - [ ] Spacing, shadows, borders
  - [ ] Dark mode (opcional)

#### 4.3 Tipos TypeScript

- [ ] **Criar `types/index.ts`:**
  - [ ] Interfaces para todas as entidades
  - [ ] Types para API responses
  - [ ] Types para forms
  - [ ] Enums (estados, tipos, etc.)

#### 4.4 Implementar Módulos Principais

- [ ] **Módulo Membros (completo):**
  - [ ] Listagem com filtros e paginação
  - [ ] Formulário de criação/edição
  - [ ] Vista de detalhes (tabs: dados, financeiro, desportivo)
  - [ ] Integração API v2

- [ ] **Módulo Financeiro (completo):**
  - [ ] Listagem de faturas (filtros por mês, estado, membro)
  - [ ] Geração de mensalidades (interface + confirmação)
  - [ ] Registo de pagamentos
  - [ ] Conta corrente de membro
  - [ ] Dashboard financeiro (KPIs)

- [ ] **Módulo Desportivo (básico):**
  - [ ] Listagem de atletas
  - [ ] Criação de treinos
  - [ ] Registo de presenças

- [ ] **Seletor de Clube:**
  - [ ] Dropdown no header
  - [ ] Listar clubes do user
  - [ ] Trocar clube (reload página)

---

### 🔵 FASE 5 - Features Avançadas

**Objetivo: Completar funcionalidades secundárias.**

#### 5.1 Inventário

- [ ] CRUD de materiais
- [ ] Movimentos de stock
- [ ] Empréstimos
- [ ] Manutenções

#### 5.2 Comunicação

- [ ] Templates de email
- [ ] Segmentos
- [ ] Campanhas
- [ ] Histórico de envios

#### 5.3 Relatórios e Dashboards

- [ ] Dashboard financeiro (receitas, dívidas, projeções)
- [ ] Dashboard desportivo (presenças, resultados)
- [ ] Exports (PDF, Excel)

#### 5.4 Documentos

- [ ] Upload de ficheiros
- [ ] Associação a entidades
- [ ] Download
- [ ] Validação de documentos

---

### 🟤 FASE 6 - DevOps e Deploy

#### 6.1 Ambientes

- [ ] Configurar MySQL/PostgreSQL (produção)
- [ ] Configurar Redis (cache + queue)
- [ ] Configurar S3 (storage de ficheiros)
- [ ] Variáveis de ambiente por ambiente

#### 6.2 CI/CD

- [ ] Pipeline de testes (GitHub Actions)
- [ ] Deploy automático (staging)
- [ ] Migrations automáticas
- [ ] Rollback plan

#### 6.3 Monitorização

- [ ] Logs estruturados (Monolog)
- [ ] Error tracking (Sentry)
- [ ] Performance monitoring
- [ ] Backup automático (diário)

---

## 12. Tarefas Pendentes (Categorização Antiga)

> **NOTA:** Esta secção foi mantida para referência, mas deve seguir o **Roadmap Priorizado** acima.

### 12.1 Backend - Alta Prioridade

- [ ] **Completar controllers v2** (ver FASE 2.3)
- [ ] **Testar e corrigir relacionamentos** (ver FASE 3.2)
- [ ] **Implementar Services pendentes**
  - [ ] AtletaService (criação com dados desportivos)
  - [ ] EventoService (inscrições, convocatórias)
  - [ ] InventarioService (movimentos de stock)
  - [ ] ComunicacaoService (envio de emails)

- [ ] **Validação e Form Requests**
  - [ ] Criar Form Requests para todas as operações
  - [ ] Validação de datas (períodos, vencimentos)
  - [ ] Validação de valores monetários

- [ ] **Jobs e Queues**
  - [ ] Job para envio de emails
  - [ ] Job para geração automática de mensalidades
  - [ ] Job para atualização de `status_cache` das faturas

### 12.2 Backend - Média Prioridade

- [ ] **Seeders e Factories**
  - [ ] Testar todos os seeders
  - [ ] Criar factories para todos os models
  - [ ] Seeder de dados de teste

- [ ] **API Resources** (ver FASE 2.1)
- [ ] **Documentação API**
  - [ ] Gerar collection Postman atualizada
  - [ ] Documentar todos os endpoints
  - [ ] Exemplos de request/response

### 12.3 Frontend - Alta Prioridade

(Ver FASE 4 do Roadmap Priorizado)

### 12.4 Frontend - Média Prioridade

(Ver FASE 4 e 5 do Roadmap Priorizado)

### 12.5 DevOps e Deploy

(Ver FASE 6 do Roadmap Priorizado)

### 12.6 Testes

(Ver FASE 3 do Roadmap Priorizado)

---

## 13. Checklist de Qualidade (Antes de Deploy)

### 13.1 Backend

- [ ] ✅ Todos os models auditados (SoftDeletes correto)
- [ ] ✅ Normalização user_id/membro_id completa
- [ ] ✅ Tenancy garantido (Global Scopes)
- [ ] ✅ API Resources implementadas
- [ ] ✅ Policies aplicadas em rotas v2
- [ ] ✅ Testes de Services críticos (>80% coverage)
- [ ] ✅ Índices otimizados
- [ ] ✅ Seeders testados e funcionais
- [ ] ✅ Validação consistente (Form Requests)
- [ ] ✅ Error handling padronizado

### 13.2 Frontend

- [ ] ✅ Estado global implementado
- [ ] ✅ Autenticação persistente
- [ ] ✅ Tipos TypeScript completos
- [ ] ✅ Componentes padronizados
- [ ] ✅ Módulos principais funcionais
- [ ] ✅ Seletor de clube funcionando
- [ ] ✅ Error handling consistente
- [ ] ✅ Loading states em todas as operações

### 13.3 Segurança

- [ ] ✅ CORS configurado corretamente
- [ ] ✅ Rate limiting aplicado
- [ ] ✅ SQL Injection prevenido (Eloquent)
- [ ] ✅ XSS prevenido (escape de outputs)
- [ ] ✅ CSRF tokens em forms
- [ ] ✅ Passwords hasheadas (bcrypt)
- [ ] ✅ Tokens expiram corretamente
- [ ] ✅ Logs não expõem dados sensíveis

### 13.4 Performance

- [ ] ✅ Queries otimizadas (N+1 prevenido)
- [ ] ✅ Eager loading onde necessário
- [ ] ✅ Cache implementado (Redis)
- [ ] ✅ Queue para operações pesadas
- [ ] ✅ Paginação em listagens
- [ ] ✅ Índices em colunas críticas

---

## 14. Documentação Relacionada

### 14.1 Documentos Principais

1. **ClubManager_SPEC_DEFINITIVA_Copilot_Rewrite.md**
   - Especificação técnica completa e definitiva
   - Decisões de arquitetura
   - Modelo de dados completo
   - Regras de negócio
   - **Status:** ✅ Documento base aprovado

2. **REFATORACAO_2026_01_22.md**
   - Registo da refatoração realizada em 22/01/2026
   - Alterações estruturais implementadas
   - Lista de migrations criadas
   - Models e seeders
   - **Status:** ✅ Refatoração concluída

3. **ESTADO_ATUAL_DO_SISTEMA.md** (este ficheiro)
   - Consolidação do estado atual
   - Inventário completo de código
   - **Erros críticos identificados**
   - **Roadmap priorizado de desenvolvimento**
   - **Status:** ✅ Atualizado em 22/01/2026

4. **VERSIONING.md** (a criar - FASE 1.1)
   - Separação clara entre v2 e legacy
   - Plano de deprecação
   - Guia de desenvolvimento

### 14.2 Documentos Legacy (docs/Old/)

Os seguintes documentos foram movidos para `docs/Old/` por estarem desatualizados:

- ANALISE_ESTADO_ATUAL.md
- COMPARACAO_ARQUITETURA_VS_IMPLEMENTACAO.md
- ESTADO_IMPLEMENTACAO_LARAVEL.md
- NOVA_ARQUITETURA_BACKEND.md
- PLANO_ACAO_IMEDIATO.md
- PLANO_IMPLEMENTACAO.md
- Outros...

**⚠️ Nota:** Não consultar estes documentos para desenvolvimento. Usar apenas os documentos principais acima.

### 14.3 Outros Ficheiros Relevantes

- **API-README.md** - Documentação da API (raiz do projeto)
- **README.md** (backend) - Instruções de setup do backend
- **README.md** (frontend) - Instruções de setup do frontend
- **bootstrap.sh** - Script de bootstrap do projeto
- **ClubManager-API.postman_collection.json** - Collection Postman

---

## 15. Regras de Desenvolvimento (Team Guidelines)

### 15.1 Código Backend

1. **Sempre usar v2 para novas features**
   - Routes: `/api/v2/*`
   - Controllers: `App\Http\Controllers\Api\*`
   - Middleware obrigatório: `ensure.club.context`

2. **Services para lógica de negócio**
   - Controllers apenas orchestram
   - Lógica complexa vai em Services
   - Services são testáveis

3. **API Resources obrigatórias**
   - Nunca retornar models diretamente
   - Usar `MembroResource`, `FaturaResource`, etc.

4. **Autorização obrigatória**
   - Policies para todas as entidades
   - `$this->authorize()` em todos os métodos

5. **Tenancy obrigatório**
   - Global Scope em models operacionais
   - Sempre filtrar por `club_id`

### 15.2 Código Frontend

1. **TypeScript obrigatório**
   - Criar interfaces para todas as entidades
   - Tipar props de componentes
   - Tipar responses da API

2. **Componentes reutilizáveis**
   - Não duplicar código UI
   - Usar componentes base (Button, Input, etc.)

3. **Estado gerido centralmente**
   - Usar Context API
   - Não duplicar estado entre componentes

4. **Error handling consistente**
   - Sempre tratar erros da API
   - Mostrar mensagens user-friendly
   - Logs de debug apenas em dev

### 15.3 Testes

1. **Testes obrigatórios para:**
   - Todos os Services
   - Todos os Controllers v2
   - Lógica de cálculo (financeiro, estados)

2. **Não fazer merge sem testes**
   - Feature nova = testes novos
   - Bug fix = teste de regressão

### 15.4 Git Workflow

1. **Branches:**
   - `main` - produção (protegido)
   - `develop` - desenvolvimento
   - `feature/*` - novas features
   - `fix/*` - correções

2. **Commits:**
   - Mensagens descritivas
   - Prefixos: `feat:`, `fix:`, `refactor:`, `test:`, `docs:`

3. **Pull Requests:**
   - Review obrigatório
   - CI/CD deve passar
   - Atualizar documentação se necessário

---

## Contacto e Manutenção

**Última atualização:** 22 de Janeiro de 2026 - 23:45  
**Responsável:** Equipa de Desenvolvimento ClubManager  
**Versão do documento:** 2.0

---

## Changelog

### v2.0 - 22 de Janeiro de 2026 (23:45)
- 🔴 **CRÍTICO:** Identificados erros estruturais (SoftDeletes, normalização, tenancy)
- ✅ Adicionada secção completa de **Erros Críticos Identificados**
- ✅ Criado **Roadmap Priorizado** em 6 fases
- ✅ Adicionado **Checklist de Qualidade** pré-deploy
- ✅ Criadas **Regras de Desenvolvimento** para equipa
- ✅ Reorganização das prioridades (FASE 1 = crítico, FASE 6 = deploy)

### v1.0 - 22 de Janeiro de 2026
- ✅ Primeira versão do documento consolidado
- ✅ Inventário completo de configurações, migrations, models
- ✅ Análise de routes, controllers e services
- ✅ Estado do frontend React
- ✅ Lista de CRUDs implementados
- ✅ Erros conhecidos e tarefas pendentes
