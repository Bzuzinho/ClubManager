# Plano de Implementação ClubManager
## Baseado na Documentação Técnica

Este documento consolida todos os requisitos dos documentos existentes e define um roadmap claro para implementação.

---

## 📋 Estado Atual (Implementado)

### Backend ✅
- [x] Laravel 12 + PHP 8.2
- [x] Laravel Sanctum para autenticação
- [x] Migração: tabela `users` com campo `role`
- [x] Seeder: AdminUserSeeder
- [x] Controller: AuthController (login)
- [x] API: POST /api/login retorna token

### Frontend ✅
- [x] React 19 + TypeScript + Vite
- [x] React Router com rotas protegidas
- [x] Axios client com interceptor de token
- [x] Layouts: AppLayout, DashboardLayout, Sidebar, TopBar
- [x] Views básicas: Login, Dashboard
- [x] Módulos stub: Members, Sports, Events, Financial

### Infraestrutura ✅
- [x] PostgreSQL (Neon) configurado
- [x] Script bootstrap.sh
- [x] Sistema automático de documentação

---

## 🎯 Fase 1: Modelação de Dados (Backend)

### Prioridade ALTA
Criar modelos e migrações para as entidades principais:

#### 1.1 Membros e Perfis
```php
// Models a criar:
- Member (id, user_id, number, birthdate, member_type_id, status, ...)
- MembershipType (id, name, monthly_fee, sports_allowed)
- Guardian (id, user_id) 
- Athlete (id, user_id, member_id, guardian_id)
- Document (id, documentable_type, documentable_id, file_path, type)
```

**Migrations:**
- `create_members_table`
- `create_membership_types_table`
- `create_guardians_table`
- `create_athletes_table`
- `create_documents_table` (polimórfica)

#### 1.2 Módulo Desportivo
```php
// Models:
- Sport (id, name, description)
- Team (id, sport_id, name, age_group)
- Training (id, team_id, date, location, description)
- Presence (id, training_id, athlete_id, status)
- Convocation (id, event_id, athlete_id, status)
- Competition (id, sport_id, name, date, location)
- Result (id, competition_id, team_id, score, position)
```

**Migrations:**
- `create_sports_table`
- `create_teams_table`
- `create_trainings_table`
- `create_presences_table`
- `create_convocations_table`
- `create_competitions_table`
- `create_results_table`

#### 1.3 Eventos
```php
// Models:
- Event (id, title, description, date, location, type)
- EventRegistration (id, event_id, member_id, status)
```

**Migrations:**
- `create_events_table`
- `create_event_registrations_table`

#### 1.4 Financeiro
```php
// Models:
- Invoice (id, member_id, amount, due_date, status, invoice_number)
- InvoiceItem (id, invoice_id, description, amount)
- Payment (id, invoice_id, amount, payment_date, method)
- Movement (id, amount, type, category, date, description)
- CostCenter (id, name, budget)
```

**Migrations:**
- `create_invoices_table`
- `create_invoice_items_table`
- `create_payments_table`
- `create_movements_table`
- `create_cost_centers_table`

---

## 🎯 Fase 2: API REST (Backend)

### 2.1 Controllers CRUD
Para cada modelo, criar controller com:
- `index()` - listar com filtros e paginação
- `store()` - criar novo registo
- `show($id)` - detalhes
- `update($id)` - atualizar
- `destroy($id)` - eliminar

**Controllers a criar:**
- MemberController
- MembershipTypeController
- TrainingController
- PresenceController
- EventController
- InvoiceController
- PaymentController
- MovementController
- SportController
- TeamController
- CompetitionController

### 2.2 Rotas API
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    // Membros
    Route::apiResource('members', MemberController::class);
    Route::post('members/{id}/documents', [MemberController::class, 'uploadDocument']);
    
    // Desportivo
    Route::apiResource('sports', SportController::class);
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('trainings', TrainingController::class);
    Route::post('trainings/{id}/presences', [PresenceController::class, 'mark']);
    
    // Eventos
    Route::apiResource('events', EventController::class);
    Route::post('events/{id}/register', [EventController::class, 'register']);
    
    // Financeiro
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('invoices/{id}/pay', [PaymentController::class, 'store']);
    Route::apiResource('movements', MovementController::class);
    
    // Relatórios
    Route::get('reports/financial', [ReportController::class, 'financial']);
    Route::get('reports/sports', [ReportController::class, 'sports']);
});
```

### 2.3 Form Requests
Criar validações para cada entidade:
- `StoreMemberRequest`
- `UpdateMemberRequest`
- `StoreInvoiceRequest`
- etc.

### 2.4 Políticas de Autorização
```php
// Policies a criar:
- MemberPolicy (admin pode tudo, encarregado só seus atletas)
- InvoicePolicy (admin e staff financeiro)
- TrainingPolicy (treinadores e admin)
```

---

## 🎯 Fase 3: Frontend - Componentes Base

### 3.1 Design System (seguindo GUIAS_GRAFICAS.md)

#### Configurar Tailwind com cores do projeto:
```typescript
// tailwind.config.js
colors: {
  primary: {
    DEFAULT: 'oklch(0.45 0.15 250)', // Deep Professional Blue
  },
  accent: {
    DEFAULT: 'oklch(0.68 0.18 45)', // Vibrant Sports Orange
  },
  neutral: {
    light: 'oklch(0.95 0.005 250)', // Soft Neutral Gray
    dark: 'oklch(0.35 0.01 250)',   // Charcoal
  }
}
```

#### Componentes Shadcn a instalar:
```bash
npx shadcn@latest add button
npx shadcn@latest add card
npx shadcn@latest add form
npx shadcn@latest add input
npx shadcn@latest add select
npx shadcn@latest add table
npx shadcn@latest add dialog
npx shadcn@latest add badge
npx shadcn@latest add tabs
npx shadcn@latest add calendar
npx shadcn@latest add toast
npx shadcn@latest add avatar
npx shadcn@latest add switch
npx shadcn@latest add checkbox
```

### 3.2 Componentes Customizados
```typescript
// Criar em src/components/ui/
- FileUpload.tsx         // Upload com preview
- UserSelector.tsx       // Pesquisa de utilizadores
- StatusBadge.tsx        // Badges coloridos (Ativo/Inativo/Suspenso)
- DataTable.tsx          // Tabela com ordenação e filtros
- DateRangePicker.tsx    // Seletor de períodos
- StatCard.tsx           // Cards de estatísticas
- ConfirmDialog.tsx      // Modal de confirmação
```

---

## 🎯 Fase 4: Frontend - Módulos Funcionais

### 4.1 Módulo Membros
**Páginas:**
- `/membros` - Lista com filtros
- `/membros/novo` - Formulário de criação
- `/membros/:id` - Detalhes e edição (com tabs)

**Funcionalidades:**
- ✓ CRUD completo
- ✓ Upload de documentos
- ✓ Associação encarregado-atleta
- ✓ Gestão de quotas
- ✓ Estados: Ativo/Inativo/Suspenso
- ✓ Pesquisa e filtros

**Componentes:**
```typescript
<MembersList />
<MemberForm />
<MemberProfile />
<DocumentUpload />
<GuardianSelector />
```

### 4.2 Módulo Desportivo
**Páginas:**
- `/desportivo/modalidades` - Gestão de desportos
- `/desportivo/equipas` - Gestão de equipas
- `/desportivo/treinos` - Calendário e presenças
- `/desportivo/competicoes` - Resultados

**Funcionalidades:**
- ✓ Gestão de equipas por escalão
- ✓ Marcação de presenças
- ✓ Convocatórias
- ✓ Registo de resultados
- ✓ Estatísticas de atletas

**Componentes:**
```typescript
<TrainingCalendar />
<PresenceMarker />
<TeamRoster />
<CompetitionResults />
<AthleteStats />
```

### 4.3 Módulo Eventos
**Páginas:**
- `/eventos` - Lista de eventos
- `/eventos/novo` - Criar evento
- `/eventos/:id` - Detalhes e inscrições

**Funcionalidades:**
- ✓ CRUD eventos
- ✓ Sistema de inscrições
- ✓ Lista de participantes
- ✓ Calendário visual

**Componentes:**
```typescript
<EventCalendar />
<EventForm />
<EventRegistrations />
<ParticipantList />
```

### 4.4 Módulo Financeiro
**Páginas:**
- `/financeiro/faturas` - Gestão de faturas
- `/financeiro/pagamentos` - Registo de pagamentos
- `/financeiro/movimentos` - Movimentos financeiros
- `/financeiro/relatorios` - Dashboards e relatórios

**Funcionalidades:**
- ✓ Geração automática de quotas mensais
- ✓ Emissão de faturas
- ✓ Registo de pagamentos
- ✓ Conciliação bancária
- ✓ Relatórios por centro de custo
- ✓ Gráficos de receitas/despesas

**Componentes:**
```typescript
<InvoiceGenerator />
<PaymentForm />
<MovementList />
<FinancialDashboard />
<RevenueChart />
<ExpenseChart />
```

---

## 🎯 Fase 5: Regras de Negócio

### 5.1 Validações Backend
- [ ] Número de sócio único
- [ ] Validação de escalões por idade
- [ ] Limite de desportos por tipo de quota
- [ ] Datas de validade de documentos
- [ ] Estados de fatura (Pendente→Pago→Vencido)

### 5.2 Automações
- [ ] Job mensal: gerar faturas de quotas
- [ ] Job diário: alertar documentos a expirar
- [ ] Job diário: marcar faturas vencidas
- [ ] Notificações por email (Laravel Mail)

### 5.3 Relatórios
- [ ] Dashboard financeiro com KPIs
- [ ] Relatório de presenças por atleta
- [ ] Relatório de resultados por modalidade
- [ ] Análise de receitas vs despesas
- [ ] Exportação para Excel/PDF

---

## 🎯 Fase 6: Melhorias e Otimizações

### 6.1 Performance
- [ ] Cache de queries frequentes (Redis)
- [ ] Lazy loading de componentes React
- [ ] Otimização de imagens
- [ ] API Pagination eficiente

### 6.2 Segurança
- [ ] Rate limiting nas APIs
- [ ] CSRF protection
- [ ] XSS sanitization
- [ ] Logs de auditoria

### 6.3 UX/UI
- [ ] Loading skeletons
- [ ] Error boundaries React
- [ ] Offline detection
- [ ] PWA capabilities
- [ ] Dark mode (opcional)

### 6.4 Testes
- [ ] Unit tests (PHPUnit)
- [ ] Feature tests (Laravel)
- [ ] E2E tests (Playwright/Cypress)
- [ ] API tests (Postman/Insomnia)

---

## 📅 Timeline Sugerida

### Sprint 1 (2 semanas) - Fundação
- Fase 1.1: Modelos de Membros
- Fase 2.1: API de Membros
- Fase 3.1: Design System
- Fase 4.1: Módulo Membros (básico)

### Sprint 2 (2 semanas) - Desportivo
- Fase 1.2: Modelos Desportivos
- Fase 2.1: API Desportiva
- Fase 4.2: Módulo Desportivo

### Sprint 3 (2 semanas) - Financeiro + Eventos
- Fase 1.3 + 1.4: Modelos Eventos e Financeiro
- Fase 2.1: APIs correspondentes
- Fase 4.3 + 4.4: Módulos Frontend

### Sprint 4 (2 semanas) - Regras e Automações
- Fase 5: Todas as validações e jobs
- Fase 2.3: Políticas e autorização

### Sprint 5 (1 semana) - Relatórios e Dashboards
- Fase 5.3: Sistema completo de relatórios

### Sprint 6 (1 semana) - Testes e Refinamentos
- Fase 6: Otimizações e testes

---

## 🔄 Processo de Desenvolvimento

### Para cada nova funcionalidade:
1. ✅ Criar/atualizar modelo e migration (Backend)
2. ✅ Criar controller e rotas API (Backend)
3. ✅ Criar testes de API (Backend)
4. ✅ Criar serviço/hook React (Frontend)
5. ✅ Criar componentes UI (Frontend)
6. ✅ Integrar com API (Frontend)
7. ✅ Testar end-to-end
8. ✅ Atualizar documentação
9. ✅ Commit com hook automático

### Convenções de Código

**Backend (Laravel):**
- PSR-12 para PHP
- Eloquent relationships bem definidas
- Form Requests para validação
- Resources para transformação de dados
- Services para lógica complexa

**Frontend (React):**
- TypeScript strict mode
- Functional components + Hooks
- React Query para state management
- Atomic Design para componentes
- CSS Modules ou Tailwind

**Git:**
- Branches: `feature/nome-funcionalidade`
- Commits: `tipo: descrição` (feat, fix, docs, style, refactor)
- Pull requests com review obrigatório

---

## 📚 Documentação a Manter

Arquivos que devem ser atualizados:
- ✓ `docs/INDEX.md` (automático via git hook)
- ✓ `docs/ESTADO_IMPLEMENTACAO_LARAVEL.md` (manual)
- ✓ `docs/LOGICA_FUNCAO_LARAVEL.md` (manual)
- ✓ `DOCUMENTATION_GUIDE.md` (quando necessário)
- ✓ README.md principal (quando necessário)

---

## 🎨 Checklist de Design (cada componente)

Antes de considerar um componente pronto, verificar:
- [ ] Usa cores da paleta definida
- [ ] Tipografia Inter nos tamanhos corretos
- [ ] Espaçamentos consistentes (16px/24px/32px)
- [ ] Estados visuais: normal, hover, active, disabled
- [ ] Animações suaves (150-300ms)
- [ ] Responsivo (mobile first)
- [ ] Acessível (contraste, keyboard, screen readers)
- [ ] Usa componentes Shadcn quando possível

---

**Este plano será a referência principal para todas as próximas implementações.**
