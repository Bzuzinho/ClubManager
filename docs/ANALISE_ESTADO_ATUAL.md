# Análise do Estado Atual do ClubManager
**Data:** 21 de Janeiro de 2026

## 📊 Visão Geral

Este documento apresenta uma análise detalhada do que está implementado no ClubManager versus o que está planejado na arquitetura da base de dados e no plano de implementação.

---

## ✅ O QUE JÁ ESTÁ IMPLEMENTADO

### Backend - Infraestrutura Base (40% completo)

#### ✅ Estrutura Laravel
- Laravel 12 com PHP 8.2 configurado
- Laravel Sanctum para autenticação (tokens)
- Sistema de migrations funcional
- Seeders configurados (AdminUserSeeder)
- Scripts Composer para desenvolvimento

#### ✅ Autenticação
- Controller: `Api/AuthController`
  - `POST /api/login` - Login com token
  - `GET /api/me` - Dados do utilizador autenticado
- Middleware `auth:sanctum` configurado
- Tokens de acesso funcionais

#### ✅ Migrations Criadas (5 tabelas)
1. **users** - Utilizadores base do sistema
2. **jobs** - Sistema de filas Laravel
3. **personal_access_tokens** - Tokens Sanctum
4. **dados_pessoais** - Dados pessoais dos membros
5. **dados_configuracao** - Configurações e consentimentos RGPD

#### ✅ Models Existentes (10 modelos)
Já existem modelos, mas **sem migrations correspondentes**:
- `Pessoa` - com relações definidas
- `Membro` - com relações definidas
- `Atleta` - com relações definidas
- `AtletaEscalao`
- `Consentimento`
- `DadosDesportivos`
- `Escalao`
- `MembroTipo`
- `RelacaoPessoa`
- `TipoMembro`
- `Utilizador`

⚠️ **PROBLEMA:** Estes modelos existem mas não têm migrations! Precisam de ser criadas.

#### ✅ Controllers Implementados
- `AuthController` - Completo
- `MemberController` - Parcialmente implementado (apenas index)

### Frontend - Estrutura Base (50% completo)

#### ✅ Infraestrutura
- React 19 + TypeScript + Vite configurado
- React Router com rotas protegidas
- Cliente Axios com interceptor de token
- Estrutura de pastas organizada

#### ✅ Layouts Implementados
- `AppLayout` - Layout principal
- `DashboardLayout` - Layout do dashboard
- `Sidebar` - Navegação lateral
- `TopBar` - Barra superior

#### ✅ Views Base
- `Login.tsx` - Página de login funcional
- `Dashboard.tsx` - Dashboard básico

#### ✅ Módulos (Stubs - apenas estrutura)
- `Members` - Módulo de membros (stub)
- `Sports` - Módulo desportivo (stub)
- `Events` - Módulo de eventos (stub)
- `Financial` - Módulo financeiro (stub)

---

## ❌ O QUE FALTA IMPLEMENTAR

### Backend - Base de Dados (CRÍTICO - 0% feito)

Seguindo a arquitetura do sistema, faltam **TODAS** estas tabelas e migrations:

#### 🔴 FASE 1 - PRIORIDADE MÁXIMA

##### Módulo Membros
```
❌ membros (core do sistema)
❌ tipos_membro (tipos de quotas)
❌ encarregados_educacao
❌ atletas_encarregados (relação)
❌ documentos (polimórfica para múltiplas entidades)
❌ historico_estados (auditoria de mudanças de estado)
```

##### Módulo Desportivo
```
❌ modalidades (desportos oferecidos)
❌ equipas (equipas por modalidade/escalão)
❌ escaloes (categorias por idade)
❌ atletas_equipas (relação N:N)
❌ treinos (sessões de treino)
❌ presencas_treino (controle de presenças)
❌ competicoes (jogos/torneios)
❌ convocatorias (atletas convocados)
❌ resultados_competicao (pontuações/classificações)
❌ dados_desportivos_atleta (estatísticas)
```

##### Módulo Eventos
```
❌ eventos (eventos gerais do clube)
❌ inscricoes_evento (inscrições de membros)
❌ tipos_evento (categoria dos eventos)
```

##### Módulo Financeiro
```
❌ faturas (invoices)
❌ itens_fatura (linhas de fatura)
❌ pagamentos (registos de pagamento)
❌ movimentos_financeiros (receitas/despesas)
❌ centros_custo (departamentos/modalidades)
❌ contas_bancarias
❌ metodos_pagamento
❌ categorias_movimento
```

#### 🔴 FASE 2 - Relações e Triggers

```
❌ Constraints de Foreign Keys entre todas as tabelas
❌ Índices para otimização de queries
❌ Triggers para auditoria
❌ Stored procedures (se necessário)
```

### Backend - Controllers e API (0% feito)

Faltam **TODOS** os controllers CRUD:

#### Módulo Membros
```
❌ MemberController (completo - só tem index())
❌ MembershipTypeController
❌ GuardianController
❌ DocumentController
```

#### Módulo Desportivo
```
❌ SportController
❌ TeamController
❌ TrainingController
❌ PresenceController
❌ CompetitionController
❌ ConvocationController
❌ ResultController
```

#### Módulo Eventos
```
❌ EventController
❌ EventRegistrationController
```

#### Módulo Financeiro
```
❌ InvoiceController
❌ PaymentController
❌ MovementController
❌ CostCenterController
```

### Backend - Validações e Regras (0% feito)

```
❌ Form Requests para validação de cada entidade
❌ Policies para autorização (admin, encarregado, atleta, staff)
❌ Gates para permissões específicas
❌ Validação de regras de negócio:
   ❌ Número de sócio único
   ❌ Validação de escalões por idade
   ❌ Limite de desportos por tipo de quota
   ❌ Datas de validade de documentos
   ❌ Estados de fatura (Pendente→Pago→Vencido)
```

### Backend - Automações (0% feito)

```
❌ Jobs Laravel:
   ❌ GenerateMonthlyInvoicesJob (faturas mensais)
   ❌ CheckExpiringDocumentsJob (alertar documentos a expirar)
   ❌ MarkOverdueInvoicesJob (marcar faturas vencidas)
   ❌ SendWelcomeEmailJob (enviar credenciais)
❌ Sistema de notificações por email
❌ Agendamento de jobs (cron)
```

### Frontend - Componentes Base (0% feito)

#### Design System
```
❌ Configurar Tailwind com cores do projeto
❌ Instalar componentes Shadcn/UI:
   ❌ button, card, form, input, select
   ❌ table, dialog, badge, tabs
   ❌ calendar, toast, avatar, switch, checkbox
```

#### Componentes Customizados
```
❌ FileUpload.tsx (upload com preview)
❌ UserSelector.tsx (pesquisa de utilizadores)
❌ StatusBadge.tsx (badges Ativo/Inativo/Suspenso)
❌ DataTable.tsx (tabela com ordenação/filtros)
❌ DateRangePicker.tsx (seletor de períodos)
❌ StatCard.tsx (cards de estatísticas)
❌ ConfirmDialog.tsx (modal de confirmação)
```

### Frontend - Módulos Funcionais (0% feito)

#### Módulo Membros
```
❌ MembersList (lista com filtros)
❌ MemberForm (criar/editar)
❌ MemberProfile (detalhes com tabs)
❌ DocumentUpload (gestão de documentos)
❌ GuardianSelector (associar encarregados)
```

#### Módulo Desportivo
```
❌ TrainingCalendar (calendário de treinos)
❌ PresenceMarker (marcar presenças)
❌ TeamRoster (plantel de equipa)
❌ CompetitionResults (resultados)
❌ AthleteStats (estatísticas)
```

#### Módulo Eventos
```
❌ EventCalendar (calendário visual)
❌ EventForm (criar/editar eventos)
❌ EventRegistrations (inscrições)
❌ ParticipantList (lista de participantes)
```

#### Módulo Financeiro
```
❌ InvoiceGenerator (gerar faturas)
❌ PaymentForm (registar pagamentos)
❌ MovementList (lista de movimentos)
❌ FinancialDashboard (dashboard financeiro)
❌ RevenueChart (gráfico de receitas)
❌ ExpenseChart (gráfico de despesas)
```

### Testes (0% feito)

```
❌ Unit tests (PHPUnit)
❌ Feature tests (Laravel)
❌ E2E tests (Playwright/Cypress)
❌ API tests (Postman/Insomnia)
```

### Otimizações e Segurança (0% feito)

```
❌ Cache de queries (Redis)
❌ Rate limiting nas APIs
❌ CSRF protection
❌ XSS sanitization
❌ Logs de auditoria
❌ Loading skeletons
❌ Error boundaries React
❌ Offline detection
```

---

## 🎯 PRIORIZAÇÃO RECOMENDADA

### 🔥 URGENTE - Sem isto, o sistema não funciona

#### 1. Migrations das Tabelas Core (Semana 1)
Criar migrations para:
1. `membros` + `tipos_membro` (base de tudo)
2. `modalidades` + `equipas` + `escaloes`
3. `treinos` + `presencas_treino`
4. `faturas` + `pagamentos`

#### 2. Models e Relações Eloquent (Semana 1)
Corrigir/criar modelos com relações corretas:
- Ajustar models existentes às novas migrations
- Definir fillable/guarded
- Estabelecer relacionamentos (hasMany, belongsTo, etc.)

#### 3. Controllers CRUD Básicos (Semana 2)
Implementar controladores com operações básicas:
- MemberController completo
- SportController
- TeamController
- TrainingController
- InvoiceController

#### 4. API Routes (Semana 2)
Definir todas as rotas REST em `routes/api.php`

### 📊 IMPORTANTE - Funcionalidades essenciais

#### 5. Frontend - Módulo Membros (Semana 3)
- Lista de membros
- Formulário criar/editar
- Upload de documentos
- Gestão de encarregados

#### 6. Frontend - Módulo Desportivo (Semana 4)
- Calendário de treinos
- Marcação de presenças
- Gestão de equipas

#### 7. Validações e Policies (Semana 4)
- Form Requests
- Políticas de autorização
- Regras de negócio

### 🎨 DESEJÁVEL - Melhorias e polish

#### 8. Design System (Semana 5)
- Configurar Tailwind
- Instalar Shadcn
- Componentes customizados

#### 9. Automações e Jobs (Semana 5)
- Geração automática de faturas
- Alertas de documentos
- Notificações por email

#### 10. Relatórios e Dashboards (Semana 6)
- Dashboard financeiro
- Relatórios de presenças
- Estatísticas de atletas

#### 11. Testes e Otimizações (Semana 7)
- Testes unitários
- Testes de integração
- Performance e cache

---

## 📈 PERCENTAGEM DE CONCLUSÃO

### Geral: **15%**

#### Backend: **20%**
- ✅ Infraestrutura: 90%
- ✅ Autenticação: 100%
- ⚠️ Migrations: 10% (5/50+ tabelas)
- ⚠️ Models: 30% (existem mas sem migrations)
- ⚠️ Controllers: 5% (1/15+ controllers)
- ❌ Validações: 0%
- ❌ Policies: 0%
- ❌ Jobs: 0%

#### Frontend: **25%**
- ✅ Infraestrutura: 100%
- ✅ Layouts: 100%
- ✅ Autenticação: 100%
- ⚠️ Módulos: 10% (só estrutura)
- ❌ Componentes: 0%
- ❌ Design System: 0%
- ❌ CRUD Funcional: 0%

---

## 🚨 PROBLEMAS IDENTIFICADOS

### 1. **Desalinhamento entre Models e Migrations**
- Existem 10 models sem migrations correspondentes
- Models usam nomenclatura português mas podem não seguir convenções Laravel
- Falta definir relações Eloquent corretamente

### 2. **Falta de Estrutura de Base de Dados**
- Apenas 5 das 50+ tabelas necessárias foram criadas
- Sem isto, nenhum módulo pode funcionar de verdade

### 3. **Controllers Incompletos**
- MemberController mal iniciado
- Todos os outros controllers em falta

### 4. **Frontend com Stubs**
- Todos os módulos mostram apenas placeholders
- Nenhum CRUD funcional
- Sem integração com API

### 5. **Falta de Design System**
- Tailwind não configurado com cores do projeto
- Shadcn não instalado
- Componentes não seguem guidelines

---

## 📋 PRÓXIMOS PASSOS RECOMENDADOS

### Passo 1: Decidir Estratégia de Models
**DECISÃO NECESSÁRIA:** 
- ❓ Usar os models existentes (Pessoa, Membro, Atleta) e criar migrations para eles?
- ❓ OU seguir o plano novo (Member, Guardian, Athlete) e deletar os existentes?

### Passo 2: Criar Migrations Core (1-2 dias)
Criar migrations para as tabelas essenciais do sistema.

### Passo 3: Ajustar/Criar Models (1 dia)
Garantir que todos os models têm migrations e relações corretas.

### Passo 4: Implementar Controllers Base (2-3 dias)
Criar controllers CRUD para as entidades principais.

### Passo 5: Frontend - Módulo Membros (3-4 dias)
Implementar primeiro CRUD completo no frontend.

### Passo 6: Testar e Iterar
Garantir que o fluxo completo funciona antes de avançar.

---

## 🎯 RECOMENDAÇÃO FINAL

**Estado Atual:** O projeto tem uma boa fundação (infraestrutura, autenticação, layouts) mas está a ~15% de conclusão.

**Gargalo Principal:** Falta de migrations e estrutura de base de dados. Sem isto, nada mais pode avançar.

**Próxima Ação:** Criar as migrations core do sistema (membros, desportivo básico, financeiro básico) seguindo a arquitetura definida.

**Timeline Realista:** 6-8 semanas para MVP funcional com módulos básicos.

---

*Este documento deve ser atualizado conforme o desenvolvimento avança.*
