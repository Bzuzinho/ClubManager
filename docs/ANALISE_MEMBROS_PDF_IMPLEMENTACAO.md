# Análise do PDF - Módulo Membros - Plano de Implementação

## 📋 Resumo Executivo

O PDF especifica uma ficha de utilizador/membro com **4 separadores principais** e requisitos extensos de campos, relações bidirecionais e lógica condicional. O sistema atual tem **significativas lacunas** que precisam ser corrigidas.

---

## 🔍 Análise Comparativa: Estado Atual vs. PDF

### ✅ Separadores Existentes vs. Especificados

| PDF | Atual | Status |
|-----|-------|--------|
| Separador 1 - Pessoal | ✅ Tab "Geral" | Parcialmente implementado |
| Separador 2 - Financeiro | ✅ Tab "Financeiro" | Parcialmente implementado |
| Separador 3 - Desportivo | ✅ Tab "Desportivo" | Estrutura básica, faltam sub-tabs |
| Separador 4 - Configuração | ❌ Não existe | **Falta implementar completamente** |

### 📊 Análise de Campos - SEPARADOR 1 (PESSOAL)

| Campo PDF | Tabela BD | Coluna BD | Status | Ação Necessária |
|-----------|-----------|-----------|--------|-----------------|
| foto_perfil | - | - | ❌ Falta | Implementar upload imagem + storage |
| nome_completo | dados_pessoais | nome_completo | ✅ Existe | Validar uso correto |
| data_nascimento | dados_pessoais | data_nascimento | ✅ Existe | OK |
| nif | dados_pessoais | nif | ✅ Existe | OK |
| cc | dados_pessoais | cc | ✅ Existe | OK |
| morada | dados_pessoais | morada | ✅ Existe | OK |
| codigo_postal | dados_pessoais | codigo_postal | ✅ Existe | OK |
| localidade | dados_pessoais | localidade | ✅ Existe | OK |
| nacionalidade | dados_pessoais | nacionalidade | ✅ Existe | OK |
| **estado_civil** | dados_pessoais | - | ❌ **FALTA** | **Adicionar coluna** |
| **ocupacao** | dados_pessoais | - | ❌ **FALTA** | **Adicionar coluna** |
| **empresa** | dados_pessoais | - | ❌ **FALTA** | **Adicionar coluna** |
| **escola** | dados_pessoais | - | ❌ **FALTA** | **Adicionar coluna** |
| **menor** | dados_pessoais | - | ❌ **FALTA** | **Adicionar coluna boolean** |
| sexo | dados_pessoais | sexo | ✅ Existe | Validar enum (M/F) |
| **numero_irmaos** | dados_pessoais | - | ❌ **FALTA** | **Adicionar coluna integer** |
| contacto | dados_pessoais | contacto_telefonico | ✅ Existe | OK |
| email_secundario | dados_pessoais | email_secundario | ✅ Existe | OK |
| encarregado_educacao | relacoes_users | - | ⚠️ Parcial | **Lógica complexa - ver seção especial** |
| tipo_membro | user_tipos_utilizador | - | ✅ Existe | Validar permite múltiplos |
| estado | membros | estado | ✅ Existe | Validar enum (ativo/inativo/suspenso) |
| contacto_telefonico | dados_pessoais | contacto_telefonico | ✅ Existe | OK (duplicado de contacto?) |
| nº_de_socio | membros | numero_socio | ✅ Existe | Implementar sequencial único |

**CAMPOS EM FALTA NO SEPARADOR PESSOAL: 7**
- estado_civil
- ocupacao
- empresa
- escola
- menor (boolean)
- numero_irmaos
- foto_perfil

---

### 📊 Análise de Campos - SEPARADOR 2 (FINANCEIRO)

| Campo PDF | Tabela BD | Coluna BD | Status | Ação Necessária |
|-----------|-----------|-----------|--------|-----------------|
| tipo_mensalidade | dados_financeiros | mensalidade_id | ✅ Existe | FK para mensalidades |
| conta_corrente | dados_financeiros | - | ❌ **FALTA** | **Adicionar coluna** |
| centro_custo | - | - | ❌ **FALTA** | **Criar tabela pivot membro_centros_custo** |

**CAMPOS EM FALTA NO SEPARADOR FINANCEIRO: 2**
- conta_corrente
- centro_custo (relação N:N)

---

### 📊 Análise de Campos - SEPARADOR 3 (DESPORTIVO)

#### Sub-Separador: Dados Desportivos

| Campo PDF | Tabela BD | Coluna BD | Status | Ação Necessária |
|-----------|-----------|-----------|--------|-----------------|
| num_federacao | dados_desportivos | num_federacao | ✅ Existe | OK |
| **cartao_federacao** | - | - | ❌ **FALTA** | **Adicionar upload imagem** |
| numero_pmb | dados_desportivos | numero_pmb | ✅ Existe | OK |
| data_inscricao | dados_desportivos | data_inscricao | ✅ Existe | OK |
| **inscricao** | - | - | ❌ **FALTA** | **Adicionar upload ficheiro** |
| escalao | atleta_escaloes | - | ✅ Existe | Pivot table OK (múltiplos escalões) |
| data_atestado_medico | dados_desportivos | data_atestado_medico | ✅ Existe | OK |
| **arquivo_atestado_medico** | - | - | ❌ **FALTA** | **Múltiplos ficheiros - relação files** |
| informacoes_medicas | dados_desportivos | informacoes_medicas | ✅ Existe | Validar tipo text/json |
| presencas | presencas | - | ✅ Existe | Tabela existe |
| treinos | treinos | - | ✅ Existe | Tabela existe |
| resultados | resultados | - | ✅ Existe | Tabela existe |
| **ativo** | dados_desportivos | - | ❌ **FALTA** | **Adicionar coluna boolean** |

#### Sub-Separadores Adicionais
- **Convocatórias**: ✅ Implementado via eventos_participantes
- **Registo Presenças**: ✅ Implementado via presencas
- **Resultados**: ✅ Implementado via resultados
- **Treinos**: ✅ Implementado via treinos (falta lógica de edição pelo atleta)
- **Planeamento (Mesociclos/Microciclos)**: ✅ Tabelas existem

**CAMPOS EM FALTA NO SEPARADOR DESPORTIVO: 4**
- cartao_federacao (imagem)
- inscricao (ficheiro)
- arquivo_atestado_medico (múltiplos ficheiros)
- ativo (boolean)

---

### 📊 Análise de Campos - SEPARADOR 4 (CONFIGURAÇÃO)

| Campo PDF | Tabela BD | Coluna BD | Status | Ação Necessária |
|-----------|-----------|-----------|--------|-----------------|
| **perfil** | - | - | ❌ **FALTA** | **Implementar via Spatie Roles** |
| rgpd | dados_configuracao | rgpd | ✅ Existe | OK |
| data_rgpd | dados_configuracao | data_rgpd | ✅ Existe | OK |
| **arquivo_rgpd** | - | - | ❌ **FALTA** | **Adicionar upload ficheiro** |
| consentimento | dados_configuracao | consentimento | ✅ Existe | OK |
| data_consentimento | dados_configuracao | data_consentimento | ✅ Existe | OK |
| **arquivo_consentimento** | - | - | ❌ **FALTA** | **Adicionar upload ficheiro** |
| afiliacao | dados_configuracao | afiliacao | ✅ Existe | OK |
| data_afiliacao | dados_configuracao | data_afiliacao | ✅ Existe | OK |
| **arquivo_afiliacao** | - | - | ❌ **FALTA** | **Adicionar upload ficheiro** |
| **rgpd_assinado** | dados_configuracao | - | ❌ **FALTA** | **Adicionar coluna boolean** |
| declaracao_transporte | dados_configuracao | declaracao_transporte | ✅ Existe | OK |
| **declaracao_transporte_arquivo** | - | - | ❌ **FALTA** | **Adicionar upload ficheiro** |
| Email_utilizador | dados_configuracao | email_utilizador | ✅ Existe | Validar sincronização com users.email |

**CAMPOS EM FALTA NO SEPARADOR CONFIGURAÇÃO: 6**
- perfil (roles/permissions)
- arquivo_rgpd
- arquivo_consentimento
- arquivo_afiliacao
- rgpd_assinado
- declaracao_transporte_arquivo

---

## 🔗 Lógica de Relações Complexas

### 🔴 CRÍTICO: Relação Encarregado de Educação ↔ Educando

**Especificação do PDF:**

1. **Se campo "menor" = true**:
   - Mostrar campo "encarregado_educacao"
   - Permitir escolher utilizador(es) com tipo_membro = "Encarregado de Educação"
   - **AUTOMÁTICO**: Quando EE é atribuído ao menor, o menor deve aparecer automaticamente no campo "educando" do EE

2. **Se campo "menor" = false E tipo_membro inclui "Encarregado de Educação"**:
   - Mostrar campo "educando"
   - Permitir escolher utilizador(es) com campo "menor" = true

**Implementação Necessária:**

```php
// Tabela: relacoes_users
// Precisa de 2 registos para cada relação:
// 1. user_origem_id = EE, user_destino_id = Educando, tipo_relacao = 'encarregado_educacao'
// 2. user_origem_id = Educando, user_destino_id = EE, tipo_relacao = 'educando'

// Quando adicionar EE a um menor:
RelacaoUser::create([
    'club_id' => $clubId,
    'user_origem_id' => $encarregadoId,
    'user_destino_id' => $menorId,
    'tipo_relacao' => 'encarregado_educacao',
    'ativo' => true
]);

RelacaoUser::create([
    'club_id' => $clubId,
    'user_origem_id' => $menorId,
    'user_destino_id' => $encarregadoId,
    'tipo_relacao' => 'educando',
    'ativo' => true
]);
```

**Action Items:**
- ✅ Tabela relacoes_users existe
- ❌ Campo "menor" não existe em dados_pessoais
- ❌ Lógica de sincronização automática não implementada
- ❌ Frontend não tem campo "educando" visível para EE
- ❌ Frontend não tem campo "encarregado_educacao" condicional

---

### 🔴 CRÍTICO: Visibilidade do Separador Desportivo

**Especificação do PDF:**
- Se tipo_membro inclui "atleta" → mostrar separador Desportivo
- Caso contrário → esconder separador

**Implementação Necessária:**
- Frontend: verificar array de tipos_utilizador do membro
- Se algum tipo tem slug='atleta' → mostrar tab "Desportivo"

---

## 📝 Migrations Necessárias

### Migration 1: Adicionar campos em falta na tabela `dados_pessoais`

```php
Schema::table('dados_pessoais', function (Blueprint $table) {
    $table->string('estado_civil')->nullable()->after('nacionalidade');
    $table->string('ocupacao')->nullable()->after('estado_civil');
    $table->string('empresa')->nullable()->after('ocupacao');
    $table->string('escola')->nullable()->after('empresa');
    $table->boolean('menor')->default(false)->after('sexo');
    $table->integer('numero_irmaos')->nullable()->after('menor');
    $table->string('foto_perfil')->nullable()->after('id'); // path para storage
});
```

### Migration 2: Adicionar campos em falta na tabela `dados_financeiros`

```php
Schema::table('dados_financeiros', function (Blueprint $table) {
    $table->string('conta_corrente')->nullable()->after('mensalidade_id');
});
```

### Migration 3: Criar tabela pivot `membro_centros_custo`

```php
Schema::create('membro_centros_custo', function (Blueprint $table) {
    $table->id();
    $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
    $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
    $table->foreignId('centro_custo_id')->constrained('centros_custo')->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['club_id', 'membro_id', 'centro_custo_id']);
    $table->index('club_id');
});
```

### Migration 4: Adicionar campos em falta na tabela `dados_desportivos`

```php
Schema::table('dados_desportivos', function (Blueprint $table) {
    $table->string('cartao_federacao')->nullable()->after('num_federacao'); // path
    $table->string('inscricao')->nullable()->after('data_inscricao'); // path
    $table->boolean('ativo')->default(true)->after('informacoes_medicas');
});
```

### Migration 5: Adicionar campos em falta na tabela `dados_configuracao`

```php
Schema::table('dados_configuracao', function (Blueprint $table) {
    $table->string('arquivo_rgpd')->nullable()->after('data_rgpd');
    $table->string('arquivo_consentimento')->nullable()->after('data_consentimento');
    $table->string('arquivo_afiliacao')->nullable()->after('data_afiliacao');
    $table->boolean('rgpd_assinado')->default(false)->after('rgpd');
    $table->string('declaracao_transporte_arquivo')->nullable()->after('declaracao_transporte');
});
```

### Migration 6: Integração com sistema de ficheiros (entidade_ficheiros)

**A tabela `entidade_ficheiros` já existe!** Usá-la para:
- arquivo_atestado_medico (múltiplos)
- Todos os outros arquivos podem usar esta tabela

```php
// Exemplo de uso:
EntidadeFicheiro::create([
    'club_id' => $clubId,
    'ficheiro_id' => $ficheiro->id,
    'entidade_tipo' => 'App\Models\DadosDesportivos',
    'entidade_id' => $dadosDesportivos->id,
    'tipo_relacao' => 'atestado_medico',
    'ordem' => 1
]);
```

---

## 🎨 Frontend: Componentes a Criar/Modificar

### 1. **MemberProfile.tsx** - Adicionar 4º Tab "Configuração"

```typescript
const tabs = [
  { id: 'geral', label: 'Geral' },
  { id: 'financeiro', label: 'Financeiro' },
  { id: 'desportivo', label: 'Desportivo', hidden: !isAtleta }, // Condicional
  { id: 'configuracao', label: 'Configuração' } // NOVO
];
```

### 2. **MemberForm.tsx** - Expandir campos

Adicionar ao form:
- foto_perfil (upload)
- estado_civil (text)
- ocupacao (text)
- empresa (text)
- escola (text)
- menor (checkbox) → controla visibilidade de encarregado_educacao
- numero_irmaos (number)
- encarregado_educacao (multi-select condicional)
- educando (multi-select condicional para EE)

### 3. **Criar: ConfiguracaoTab.tsx**

Novo componente com:
- Perfil de autorizações (select roles)
- RGPD: checkbox + data + upload arquivo
- rgpd_assinado: checkbox
- Consentimento: checkbox + data + upload arquivo
- Afiliação: checkbox + data + upload arquivo
- Declaração Transporte: checkbox + upload arquivo
- Email_utilizador (text)
- Botão "Reenviar recuperação de password"

### 4. **Criar: DadosDesportivosTab.tsx** (Sub-tabs)

```typescript
const subTabs = [
  { id: 'dados', label: 'Dados Desportivos' },
  { id: 'convocatorias', label: 'Convocatórias' },
  { id: 'presencas', label: 'Registo Presenças' },
  { id: 'resultados', label: 'Resultados' },
  { id: 'treinos', label: 'Treinos' },
  { id: 'planeamento', label: 'Planeamento' }
];
```

### 5. **FinanceiroTab.tsx** - Adicionar campos

- conta_corrente (text)
- centro_custo (multi-select)

---

## 🔧 Backend: Controllers e Services

### 1. **MembrosController** - Expandir métodos

```php
public function show($id)
{
    // Adicionar eager loadings:
    'dadosConfiguracao',
    'relacoes.userDestino', // Para EE
    'relacoes.userOrigem',  // Para educandos
    'dadosFinanceiros.centrosCusto',
    'atleta.dadosDesportivos',
    'atleta.ficheiros' // arquivos atestados
}

public function update(Request $request, $id)
{
    // Adicionar lógica de sincronização EE ↔ educando
    // Adicionar upload de ficheiros
    // Atualizar centros_custo pivot
}
```

### 2. **Criar: ConfiguracaoController**

```php
class ConfiguracaoController extends Controller
{
    public function update(Request $request, $userId)
    {
        // Atualizar dados_configuracao
        // Upload de arquivos
        // Sincronizar roles/permissions
        // Sincronizar email_utilizador com users.email
    }
    
    public function reenviarRecuperacaoSenha($userId)
    {
        // Enviar email de reset password
    }
}
```

### 3. **Criar: RelacaoService**

```php
class RelacaoService
{
    public function syncEncarregadoEducacao($menorId, array $encarregadosIds)
    {
        // Remover relações antigas
        // Criar novas relações bidirecionais
        // Validar: EE deve ter tipo "Encarregado de Educação"
        // Validar: menor deve ter campo "menor" = true
    }
}
```

### 4. **FileUploadService** - Reutilizar existente

Integrar com tabelas:
- ficheiros
- entidade_ficheiros

---

## 📋 Checklist de Implementação

### Fase 1: Database Schema (Prioridade ALTA)
- [ ] Migration 1: campos dados_pessoais
- [ ] Migration 2: campos dados_financeiros
- [ ] Migration 3: pivot membro_centros_custo
- [ ] Migration 4: campos dados_desportivos
- [ ] Migration 5: campos dados_configuracao
- [ ] Executar migrations
- [ ] Validar schema no PostgreSQL

### Fase 2: Backend Models (Prioridade ALTA)
- [ ] Atualizar DadosPessoais model (fillable, casts)
- [ ] Atualizar DadosFinanceiros model
- [ ] Criar/atualizar relationship centrosCusto em DadosFinanceiros
- [ ] Atualizar DadosDesportivos model
- [ ] Atualizar DadosConfiguracao model
- [ ] Criar relationship educandos/encarregados em User model
- [ ] Criar scopes para menor/encarregado

### Fase 3: Backend Services (Prioridade ALTA)
- [ ] Criar RelacaoService
- [ ] Atualizar MembroService para novos campos
- [ ] Implementar lógica de sincronização EE ↔ educando
- [ ] Integrar FileUploadService

### Fase 4: Backend Controllers (Prioridade ALTA)
- [ ] Expandir MembrosController::show() com eager loading
- [ ] Expandir MembrosController::update() com novos campos
- [ ] Criar ConfiguracaoController
- [ ] Adicionar rotas em api.php

### Fase 5: Backend Resources (Prioridade MÉDIA)
- [ ] Atualizar DadosPessoaisResource
- [ ] Atualizar DadosFinanceirosResource
- [ ] Atualizar DadosDesportivosResource
- [ ] Criar DadosConfiguracaoResource
- [ ] Atualizar MembroResource (incluir todos os relacionamentos)

### Fase 6: Frontend - Forms (Prioridade ALTA)
- [ ] Atualizar MemberForm.tsx com novos campos de dados pessoais
- [ ] Adicionar campo foto_perfil (upload)
- [ ] Implementar campo "menor" (checkbox)
- [ ] Implementar campo "encarregado_educacao" (condicional, multi-select)
- [ ] Implementar campo "educando" (condicional, multi-select)
- [ ] Adicionar validações frontend

### Fase 7: Frontend - Tabs (Prioridade ALTA)
- [ ] Criar ConfiguracaoTab.tsx
- [ ] Expandir FinanceiroTab.tsx (conta_corrente, centros_custo)
- [ ] Criar DadosDesportivosTab.tsx com sub-tabs
- [ ] Implementar lógica de visibilidade do tab Desportivo
- [ ] Atualizar MemberProfile.tsx com 4º tab

### Fase 8: Frontend - Sub-Tabs Desportivo (Prioridade MÉDIA)
- [ ] Sub-tab: Dados Desportivos (form)
- [ ] Sub-tab: Convocatórias (table)
- [ ] Sub-tab: Registo Presenças (table)
- [ ] Sub-tab: Resultados (table)
- [ ] Sub-tab: Treinos (table + edição atleta)
- [ ] Sub-tab: Planeamento (mesociclos/microciclos)

### Fase 9: Upload de Ficheiros (Prioridade MÉDIA)
- [ ] Integrar upload foto_perfil
- [ ] Integrar upload cartao_federacao
- [ ] Integrar upload inscricao
- [ ] Integrar upload arquivo_atestado_medico (múltiplos)
- [ ] Integrar upload arquivo_rgpd
- [ ] Integrar upload arquivo_consentimento
- [ ] Integrar upload arquivo_afiliacao
- [ ] Integrar upload declaracao_transporte_arquivo

### Fase 10: Permissions & Roles (Prioridade MÉDIA)
- [ ] Integrar Spatie Roles no ConfiguracaoTab
- [ ] Criar perfis padrão (Admin, Atleta, EE, etc.)
- [ ] Implementar authorization em controllers

### Fase 11: Testes (Prioridade BAIXA)
- [ ] Testes unitários: RelacaoService
- [ ] Testes unitários: sincronização EE ↔ educando
- [ ] Testes de integração: MembrosController
- [ ] Testes de integração: ConfiguracaoController
- [ ] Testes E2E: criação de membro menor com EE
- [ ] Testes E2E: visibilidade condicional de tabs

### Fase 12: UI/UX Final (Prioridade BAIXA)
- [ ] Validar graficamente contra PDF
- [ ] Ajustar labels/placeholders
- [ ] Adicionar tooltips explicativos
- [ ] Melhorar responsividade
- [ ] Adicionar loading states
- [ ] Adicionar error handling

---

## ⚠️ Avisos Importantes

1. **Número de Sócio Sequencial**: O PDF especifica "Único Sequencial". Implementar lógica de auto-increment por club.

2. **Email Utilizador vs Email**: Segundo PDF, `email_utilizador` deve ser igual ao `email` na criação, mas editável depois. Sincronização necessária.

3. **Relações Bidirecionais**: A relação EE ↔ educando é complexa e crítica. Testar extensivamente.

4. **Permissões de Acesso**: PDF especifica que EE podem acessar ficha do educando. Implementar authorization adequada.

5. **Ficheiros Múltiplos**: `arquivo_atestado_medico` permite múltiplos. Usar tabela `entidade_ficheiros`.

6. **Visibilidade Condicional**: Muita lógica de show/hide baseada em flags. Centralizar em helpers frontend.

---

## 📊 Resumo de Gaps Identificados

| Categoria | Total de Campos | Implementados | Em Falta | % Completo |
|-----------|----------------|---------------|----------|------------|
| Separador Pessoal | 22 | 15 | 7 | 68% |
| Separador Financeiro | 3 | 1 | 2 | 33% |
| Separador Desportivo | 14 | 10 | 4 | 71% |
| Separador Configuração | 13 | 7 | 6 | 54% |
| **TOTAL** | **52** | **33** | **19** | **63%** |

**Campos críticos em falta: 19**
**Lógica condicional complexa: 3 casos**
**Relações bidirecionais: 1 (EE ↔ educando)**
**Sistema de ficheiros: 8 uploads necessários**

---

## 🚀 Estimativa de Esforço

| Fase | Esforço (horas) | Prioridade |
|------|----------------|------------|
| Fase 1: Database Schema | 2h | ALTA |
| Fase 2: Backend Models | 2h | ALTA |
| Fase 3: Backend Services | 4h | ALTA |
| Fase 4: Backend Controllers | 3h | ALTA |
| Fase 5: Backend Resources | 2h | MÉDIA |
| Fase 6: Frontend Forms | 6h | ALTA |
| Fase 7: Frontend Tabs | 4h | ALTA |
| Fase 8: Sub-Tabs Desportivo | 6h | MÉDIA |
| Fase 9: Upload Ficheiros | 4h | MÉDIA |
| Fase 10: Permissions | 3h | MÉDIA |
| Fase 11: Testes | 6h | BAIXA |
| Fase 12: UI/UX | 4h | BAIXA |
| **TOTAL** | **46 horas** | - |

---

## 📝 Notas Finais

Este documento serve como **blueprint completo** para implementação do módulo de membros conforme especificação do PDF. Recomenda-se implementação faseada, começando pelas **Fases 1-4 (backend)** seguidas das **Fases 6-7 (frontend básico)** antes de avançar para features adicionais.

**Última atualização**: 2026-01-22
**Analisado por**: GitHub Copilot
**Documento de referência**: `docs/Modulo Membros - Ficha Utilizador_Separadores_Campos.docx.pdf`
