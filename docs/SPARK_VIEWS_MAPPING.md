# Mapeamento Spark → Laravel Pages

Este documento mapeia as views originais do Spark (pasta `/src`) para as páginas Laravel + Inertia (pasta `/frontend/src`), servindo como referência para implementação futura.

## ℹ️ Notas Importantes

- **As views Spark NÃO devem ser migradas diretamente** (usam `useKV` e runtime Spark incompatível)
- Este documento serve como **referência de funcionalidades** para implementação Laravel + Inertia
- Tipos TypeScript já foram migrados para `frontend/src/types/`
- Helpers e hooks já foram migrados para `frontend/src/utils/` e `frontend/src/hooks/`

## 📊 Status da Migração

| Módulo | Spark View | LOC | Laravel Inertia Page | Status | Prioridade | Notas |
|--------|-----------|-----|---------------------|--------|-----------|-------|
| Auth | `LoginView.tsx` | 104 | `views/Login.tsx` | ✅ Migrado | - | Completo com Laravel Sanctum |
| Dashboard | `HomeView.tsx` | 83 | `views/Dashboard.tsx` | ✅ Migrado | - | Stats básicas implementadas |
| Membros | `MembersView.tsx` | 87 | `modules/members/` | 🟡 50% | Alta | CRUD básico, falta tabs completas |
| Financeiro | `FinancialView.tsx` | ~100 | `pages/Financial/` | ❌ Falta | Alta | Faturas, pagamentos, conta corrente |
| Desportivo | `SportsView.tsx` | 74 | `modules/sports/` | 🟡 30% | Alta | Estrutura base criada |
| Eventos | `EventsView.tsx` | 61 | `modules/events/` | 🟡 20% | Média | API parcial, UI falta |
| Loja | `LojaView.tsx` | 62 | `pages/Shop/` | ❌ Falta | Média | Artigos, encomendas, stock |
| Patrocinadores | `SponsorsView.tsx` | ~80 | `pages/Sponsors/` | ❌ Falta | Baixa | CRUD simples |
| Marketing | `MarketingView.tsx` | 74 | `pages/Marketing/` | ❌ Falta | Baixa | Campanhas e newsletters |
| Comunicação | `CommunicationView.tsx` | 74 | `pages/Communication/` | ❌ Falta | Baixa | Emails automatizados |
| Configurações | `SettingsView.tsx` | 101 | `pages/Settings/` | ❌ Falta | Média | Config global do sistema |

**Legenda:**
- ✅ Migrado: Funcionalidade completa e testada
- 🟡 Parcial: Estrutura criada, funcionalidades incompletas
- ❌ Falta: Não iniciado

## 📋 Detalhamento por Módulo

### 1. Membros (`MembersView.tsx` → `modules/members/`)

**Status:** 🟡 50% completo

**Campos-Chave do Formulário:**

#### Tab: Pessoal
- `nome_completo` (required)
- `data_nascimento` (required)
- `sexo` (required)
- `email_utilizador` (required)
- `numero_socio` (auto-gerado: YYYY-NNNN)
- `tipo_membro[]` (multi-select: atleta, encarregado_educacao, treinador, dirigente, socio, funcionario)
- `estado` (ativo, inativo, suspenso)
- `foto_perfil` (upload)
- `morada`, `codigo_postal`, `localidade`
- `contacto_telefonico`, `email_secundario`
- `nif`, `cc`
- `nacionalidade`, `estado_civil`, `ocupacao`
- `empresa` (se tipo != atleta)
- `escola` (se menor = true)

#### Tab: Financeiro
- `tipo_mensalidade` (select de Mensalidades ativas)
- `conta_corrente` (saldo acumulado, read-only com link para histórico)
- `centro_custo[]` (multi-select de Centros de Custo)

#### Tab: Desportivo (visível se tipo_membro inclui "atleta")
- `num_federacao`
- `cartao_federacao` (upload)
- `numero_pmb`
- `escalao[]` (multi-select de Escalões)
- `data_atestado_medico`
- `arquivo_atestado_medico[]` (upload múltiplo)
- `informacoes_medicas` (textarea)
- `ativo_desportivo` (boolean)

#### Tab: Configuração
- `rgpd` (checkbox + data + arquivo)
- `consentimento` (checkbox + data + arquivo)
- `afiliacao` (checkbox + data + arquivo)
- `declaracao_de_transporte` (checkbox + arquivo)

**Relações:**
- `encarregado_educacao[]` → User[] (se menor = true, multi-select de users com tipo "encarregado_educacao")
- `educandos[]` → User[] (se tipo_membro inclui "encarregado_educacao", lista de menores associados)

**Fluxos de Navegação:**
1. **Listar:** Tabela com filtros (estado, tipo, pesquisa por nome/número), paginação
2. **Criar:** Botão "Novo Membro" → Formulário multi-tab → Validação → API POST /api/membros → Redirect
3. **Editar:** Click na linha → Formulário pré-preenchido → API PUT /api/membros/{id}
4. **Ver:** Click no ícone "olho" → Modal read-only com todas as tabs
5. **Apagar:** Click no ícone "lixo" → Confirmação → API DELETE /api/membros/{id} (soft delete)

**Validações:**
- `nome_completo`: min 3 caracteres
- `email_utilizador`: formato email único
- `data_nascimento`: data válida, idade >= 1 ano
- Se `menor = true` → `encarregado_educacao` obrigatório
- Se `tipo_membro` inclui "atleta" → `escalao` obrigatório

**O que falta implementar:**
- Tabs Financeiro e Desportivo completos
- Upload de arquivos (foto, documentos)
- Relação encarregados/educandos (UI + backend)
- Histórico de alterações (auditoria)

---

### 2. Financeiro (`FinancialView.tsx` → `pages/Financial/`)

**Status:** ❌ Não iniciado

**Sub-módulos:**

#### 2.1. Faturas
**Entidades:** `Fatura`, `FaturaItem`

**Campos principais:**
- `user_id` (select de membros)
- `tipo` (mensalidade, inscricao, material, servico, outro)
- `data_emissao`, `data_vencimento`
- `valor_total` (calculado a partir dos items)
- `estado_pagamento` (pendente, pago, vencido, parcial, cancelado)
- `numero_recibo`, `referencia_pagamento`
- `centro_custo_id`
- `items[]`:
  - `descricao`, `valor_unitario`, `quantidade`
  - `imposto_percentual`, `total_linha`

**Funcionalidades:**
- Criar fatura manual ou automática (mensalidade)
- Gerar PDF com template Laravel (DomPDF ou similar)
- Enviar por email
- Registar pagamento (atualizar estado)
- Cancelar fatura

#### 2.2. Conta Corrente
**Entidade:** `LancamentoFinanceiro`

**Visão:** Extrato por membro com:
- Lançamentos (receitas/despesas)
- Faturas pendentes
- Pagamentos efetuados
- Saldo atual

**Filtros:**
- Período (data início/fim)
- Tipo de lançamento
- Centro de custo

#### 2.3. Movimentos
**Entidade:** `Movimento`, `MovimentoItem`

**Diferença de Fatura:** Movimentos podem ser sem `user_id` (fornecedores externos)

**Campos:**
- `nome_manual`, `nif_manual`, `morada_manual` (se user_id = null)
- `classificacao` (receita, despesa)
- `tipo` (inscricao, material, servico, outro)
- Resto similar a Fatura

#### 2.4. Extrato Bancário
**Entidade:** `ExtratoBancario`

**Funcionalidades:**
- Upload CSV de extrato bancário
- Conciliação manual com lançamentos
- Identificação automática por referência

**O que implementar:**
1. CRUD Faturas com geração PDF
2. Sistema de pagamentos (registar, validar)
3. Conta corrente por membro
4. Dashboard financeiro (receitas/despesas por período)
5. Relatórios (mensalidades pendentes, top devedores)

---

### 3. Desportivo (`SportsView.tsx` → `modules/sports/`)

**Status:** 🟡 30% estrutura base

**Sub-módulos:**

#### 3.1. Treinos
**Entidades:** `Treino`, `TreinoSerie`, `TreinoAtleta`

**Campos principais:**
- `data`, `hora_inicio`, `hora_fim`
- `local` (nome da piscina/ginásio)
- `tipo_treino` (aerobio, sprint, tecnica, forca, recuperacao, misto)
- `escaloes[]` (quais escalões participam)
- `volume_planeado_m` (metros totais)
- `descricao_treino` (texto livre)
- `series[]`:
  - `ordem`, `descricao_texto` (ex: "4x100m Crawl EN1")
  - `distancia_total_m`, `zona_intensidade`, `estilo`
  - `repeticoes`, `intervalo`

**Registro de Presença:**
- Lista de atletas convocados
- Marcar presença/ausência
- `volume_real_m` (metros nadados)
- `rpe` (Rate of Perceived Exertion, 1-10)
- `observacoes_tecnicas`

#### 3.2. Competições
**Entidades:** `Competicao`, `Prova`, `InscricaoProva`, `Resultado`, `ResultadoSplit`

**Fluxo:**
1. Criar Competição (nome, local, datas)
2. Adicionar Provas (estilo, distância, escalão)
3. Inscrever atletas em provas
4. Registar resultados:
   - Tempo oficial
   - Posição
   - Pontos FINA
   - Splits (tempos parciais)

#### 3.3. Planeamento
**Entidades:** `Epoca`, `Macrociclo`, `Mesociclo`, `Microciclo`

**Hierarquia:**
```
Época (ex: "Temporada 2025/2026")
└── Macrociclo (ex: "Preparação Geral - Set a Dez")
    └── Mesociclo (ex: "Resistência Aeróbia - 4 semanas")
        └── Microciclo (ex: "Semana 1")
            └── Treinos (cada dia da semana)
```

**Campos Época:**
- `nome`, `ano_temporada`, `data_inicio`, `data_fim`
- `tipo` (principal, secundaria, verao)
- `estado` (planeada, em_curso, concluida)
- `volume_total_previsto`, `num_competicoes_previstas`
- `objetivos_performance`, `objetivos_tecnicos`

**O que implementar:**
1. CRUD Treinos com planeamento de séries
2. Registro de presenças + volumes reais
3. CRUD Competições + Inscrições + Resultados
4. Planeamento periódico (Épocas → Macro → Meso → Micro)
5. Dashboard desportivo (volumes semanais, presenças, evolução resultados)

---

### 4. Eventos (`EventsView.tsx` → `modules/events/`)

**Status:** 🟡 20% API parcial

**Entidades:** `Event`, `EventoConvocatoria`, `ConvocatoriaGrupo`, `EventoPresenca`, `EventoResultado`

**Tipos de Evento:**
- `prova` (competição)
- `estagio` (training camp)
- `reuniao` (meeting)
- `evento_interno` (festa, convívio)
- `treino` (pode gerar evento a partir de treino)
- `outro`

**Campos principais:**
- `titulo`, `descricao`
- `data_inicio`, `hora_inicio`, `data_fim`, `hora_fim`
- `local`, `local_detalhes`
- `tipo`, `visibilidade` (privado, restrito, publico)
- `escaloes_elegiveis[]`
- `transporte_necessario`, `hora_partida`, `local_partida`
- `taxa_inscricao`, `custo_inscricao_por_prova`
- `estado` (rascunho, agendado, em_curso, concluido, cancelado)

**Convocatórias:**
- Criar convocatória para grupo de atletas
- Atletas confirmam presença (pendente, confirmado, recusado)
- Envio automático de email/notificação

**Sistema de Custos:**
- Tipo custo: por_salto ou por_atleta
- Calcular valor total baseado em provas inscritas
- Gerar movimento financeiro automático

**Presença:**
- Registar presença no evento
- Hora de chegada
- Observações

**O que implementar:**
1. CRUD Eventos com recorrência
2. Sistema de convocatórias (criar, enviar, confirmar)
3. Integração com financeiro (custos → movimentos)
4. Registro de presenças
5. Dashboard de eventos (próximos, histórico, taxas de confirmação)

---

### 5. Loja (`LojaView.tsx` → `pages/Shop/`)

**Status:** ❌ Não iniciado

**Entidades:** `ArtigoLoja`, `EncomendaArtigo`, `MovimentoStock`, `Fornecedor`

**Sub-módulos:**

#### 5.1. Artigos
**Campos:**
- `nome`, `descricao`, `categoria`
- `preco_venda`, `preco_custo`
- `stock_atual`, `stock_minimo`
- `fornecedor_id`
- `imagem` (upload)
- `ativo`

**Categorias sugeridas:**
- Equipamento (touca, óculos, fato de banho)
- Vestuário (t-shirts, casacos clube)
- Acessórios (mochilas, bidões)
- Material treino (prancha, pull-boy)

#### 5.2. Encomendas
**Fluxo:**
1. Atleta/Encarregado faz pedido de artigos
2. Estado: pendente → aprovada → em_preparacao → entregue
3. Local entrega: clube / morada_atleta / outro
4. Gera movimento financeiro automático

**Campos:**
- `user_id`, `artigo_id`, `quantidade`
- `valor_unitario`, `valor_total`
- `escalao_id`, `centro_custo_id`
- `local_entrega`, `morada_entrega`
- `estado`, `data_entrega`

#### 5.3. Stock
**Movimentos:**
- `entrada` (compra a fornecedor)
- `saida` (venda/encomenda)
- `ajuste` (inventário)
- `devolucao`

**Registo:**
- `stock_anterior`, `stock_novo`
- `motivo`, `fornecedor_id`, `encomenda_id`
- `valor_unitario`, `registado_por`

#### 5.4. Fornecedores
**Campos simples:**
- `nome`, `nif`, `morada`, `contactos`
- `iban` (para pagamentos)
- `ativo`

**O que implementar:**
1. CRUD Artigos com imagens
2. Sistema de encomendas (carrinho → checkout → aprovação)
3. Gestão de stock (alertas stock mínimo, inventário)
4. CRUD Fornecedores
5. Relatórios (vendas por categoria, artigos mais vendidos)

---

### 6. Patrocinadores (`SponsorsView.tsx` → `pages/Sponsors/`)

**Status:** ❌ Não iniciado

**Entidade:** `Sponsor`

**Campos:**
- `nome`, `logo` (upload)
- `tipo` (principal, secundario, apoio)
- `contrato_inicio`, `contrato_fim`
- `valor_anual`
- `contacto_nome`, `contacto_email`, `contacto_telefone`
- `ativo`

**Funcionalidades:**
- CRUD simples
- Exibição no site público (logos em grid)
- Dashboard de patrocínios (total anual, próximos a vencer)

---

### 7. Marketing (`MarketingView.tsx` → `pages/Marketing/`)

**Status:** ❌ Não iniciado

**Funcionalidades sugeridas:**
- Campanhas de email (newsletters)
- Segmentação de público (por tipo_membro, escalão, etc.)
- Templates de email
- Estatísticas (taxa abertura, clicks)
- Gestão de conteúdo para redes sociais

**Implementação futura** (baixa prioridade)

---

### 8. Comunicação (`CommunicationView.tsx` → `pages/Communication/`)

**Status:** ❌ Não iniciado

**Funcionalidades:**
- Emails automatizados:
  - Boas-vindas (novo membro)
  - Lembretes (mensalidade vencida)
  - Confirmação (encomenda, inscrição evento)
  - Convocatórias
- Templates Laravel (Blade/Mailable)
- Configuração SMTP

**Implementação futura** (baixa prioridade)

---

### 9. Configurações (`SettingsView.tsx` → `pages/Settings/`)

**Status:** ❌ Não iniciado

**Sub-módulos:**

#### 9.1. Configurações Gerais
- Nome do clube
- Logo (upload)
- Cores principais (theme)
- Morada, contactos, email geral
- Redes sociais (links)

#### 9.2. Escalões
- CRUD Escalões (nome, idade_min, idade_max)
- Usado em Membros, Treinos, Competições

#### 9.3. Centros de Custo
- CRUD Centros de Custo
- Tipo: equipa, departamento, pessoa, projeto
- Orçamento anual

#### 9.4. Mensalidades
- CRUD Tipos de Mensalidade
- Designação, valor, ativo

#### 9.5. Tipos de Evento
- CRUD EventoTipoConfig
- Nome, cor, ícone
- Flags: gera_taxa, requer_convocatoria, requer_transporte

#### 9.6. Utilizadores e Permissões
- Gestão de contas (admin, staff)
- Perfis de acesso (ACL básico)

---

## 🚀 Prioridades de Implementação

### Fase 7 (Atual) - Completar Membros
**Tempo estimado:** 2 semanas

- [ ] Tab Financeiro (mensalidade, conta corrente)
- [ ] Tab Desportivo (campos federação, atestado)
- [ ] Tab Configuração (RGPD, consentimentos)
- [ ] Upload de arquivos (foto, documentos)
- [ ] Relação encarregados/educandos
- [ ] Testes E2E completos

### Fase 8 - Financeiro
**Tempo estimado:** 3 semanas

- [ ] CRUD Faturas com PDF
- [ ] Sistema de pagamentos
- [ ] Conta corrente por membro
- [ ] CRUD Movimentos
- [ ] Dashboard financeiro
- [ ] Relatórios básicos

### Fase 9 - Desportivo
**Tempo estimado:** 4 semanas

- [ ] CRUD Treinos com séries
- [ ] Registro de presenças
- [ ] CRUD Competições + Provas
- [ ] Inscrições e resultados
- [ ] Planeamento (Épocas, ciclos)
- [ ] Dashboard desportivo

### Fase 10 - Eventos
**Tempo estimado:** 2 semanas

- [ ] CRUD Eventos
- [ ] Sistema de convocatórias
- [ ] Integração financeira
- [ ] Registro de presenças
- [ ] Dashboard de eventos

### Fase 11 - Loja
**Tempo estimado:** 2 semanas

- [ ] CRUD Artigos
- [ ] Sistema de encomendas
- [ ] Gestão de stock
- [ ] CRUD Fornecedores

### Fase 12 - Configurações
**Tempo estimado:** 1 semana

- [ ] Configurações gerais
- [ ] CRUD Escalões
- [ ] CRUD Centros de Custo
- [ ] CRUD Mensalidades
- [ ] CRUD Tipos de Evento

### Fase 13 - Patrocinadores (Opcional)
**Tempo estimado:** 3 dias

- [ ] CRUD Patrocinadores
- [ ] Exibição pública

### Fase 14 - Marketing/Comunicação (Futuro)
**Tempo estimado:** TBD

- [ ] Campanhas de email
- [ ] Emails automatizados
- [ ] Templates

---

## 📝 Padrões de Código

### Estrutura de Página Inertia

```typescript
// frontend/src/pages/Membros/Index.tsx
import { Head } from '@inertiajs/react';
import DashboardLayout from '@/layouts/DashboardLayout';
import type { User } from '@/types';

interface Props {
  membros: {
    data: User[];
    current_page: number;
    last_page: number;
    total: number;
  };
}

export default function MembrosIndex({ membros }: Props) {
  return (
    <DashboardLayout>
      <Head title="Membros" />
      
      {/* Conteúdo da página */}
    </DashboardLayout>
  );
}
```

### Estrutura de Controller Laravel

```php
// backend/app/Http/Controllers/MembroController.php
namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class MembroController extends Controller
{
    public function index()
    {
        $membros = User::with(['encarregados', 'educandos'])
            ->paginate(20);
            
        return Inertia::render('Membros/Index', [
            'membros' => $membros,
        ]);
    }
    
    public function store(StoreMembroRequest $request)
    {
        $membro = User::create($request->validated());
        
        return redirect()->route('membros.index')
            ->with('success', 'Membro criado com sucesso');
    }
}
```

---

## 📚 Recursos Adicionais

- **Tipos TypeScript:** `frontend/src/types/`
- **Helpers:** `frontend/src/utils/user-helpers.ts`
- **Hooks:** `frontend/src/hooks/use-mobile.ts`
- **Documentação Backend:** `docs/backend-README.md`
- **API Postman:** `ClubManager-API.postman_collection.json`

---

**Última atualização:** 2026-02-03  
**Autor:** @copilot  
**Reviewers:** @Bzuzinho
