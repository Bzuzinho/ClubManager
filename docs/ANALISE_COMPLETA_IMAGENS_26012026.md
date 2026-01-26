# RELATÓRIO COMPLETO DE ANÁLISE DAS IMAGENS - CLUBMANAGER
**Data:** 26 de Janeiro de 2026
**Fonte:** /workspaces/ClubManager/docs/Imagens_prints_26012026

---

## RESUMO EXECUTIVO

Total de imagens analisadas: **65 imagens**
Módulos identificados: **9 módulos principais**

### Status Geral de Implementação:
- ✅ **Implementado Completamente**: ~15%
- ⚠️ **Implementado Parcialmente**: ~45%
- ❌ **Não Implementado**: ~40%

---

## 1. DASHBOARD (2 imagens)

### 1.1 Dashboard.png
**Componentes Visíveis:**
- Cards com estatísticas:
  * Membros Ativos (número + ícone)
  * Atletas (número + ícone)
  * Encarregados de Educação (número + ícone)
  * Eventos Próximos (número + ícone)
  * Receitas do Mês (valor em EUR)
- Lista de próximos eventos (tabela/lista)
- Atividades recentes
- Layout em grid 2-3 colunas
- Cores: Fundo claro, cards brancos, acentos azuis

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Estrutura básica existe em `/frontend/src/views/Dashboard.tsx`
- ✅ Cards de estatísticas implementados
- ⚠️ Dados parciais (membros e atletas funcionam)
- ❌ Card "Encarregados de Educação" não implementado
- ❌ Card "Receitas do Mês" não implementado completamente
- ⚠️ Layout pode não corresponder exatamente ao design

**Rotas API:**
- ✅ `/api/v2/membros` - Existe
- ✅ `/api/atletas` - Existe
- ❌ Endpoint específico para encarregados - **EM FALTA**
- ❌ Endpoint para receitas/dashboard - **EM FALTA**

**Discrepâncias:**
1. Falta endpoint agregado para dashboard stats
2. Encarregados de educação não têm query específica
3. Receitas do mês precisam de endpoint no backend
4. Design dos cards pode não ser idêntico

### 1.2 Dashboard_AcessoRapido.png
**Componentes Visíveis:**
- Botões de acesso rápido com ícones grandes
- Grid de ações rápidas (ex: Novo Membro, Registar Presença, etc.)
- Ícones coloridos e distintivos

**Estado Atual:**
 **Não Implementado**
- Dashboard atual não tem secção de acesso rápido
- Navegação é feita apenas por sidebar

**Discrepâncias:**
1. Componente de acesso rápido totalmente em falta
2. Precisa de implementação de UI
3. Não requer novas rotas API (usa as existentes)

---

## 2. MEMBROS (10 imagens)

### 2.1 Membros_Dashboard_Cards.png
**Componentes Visíveis:**
- Vista em cards/grid
- Cada card mostra:
  * Avatar ou iniciais
  * Nome do membro
  * Número de sócio
  * Estado (badge colorido)
  * Tipos de membro (badges)
  * Botões de ação (ver, editar)
- Layout responsivo em grid

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Lista existe em `/frontend/src/modules/members/Members.tsx`
- ⚠️ Atualmente implementada como tabela, não cards
- ❌ Vista em cards não existe

**Rotas API:**
- ✅ `/api/v2/membros` - Existe e funciona

**Discrepâncias:**
1. Falta toggle entre vista lista/cards
2. Design de cards não implementado
3. Avatar/iniciais podem precisar de ajustes visuais

### 2.2 Membros_Dashboard_Lista.png
**Componentes Visíveis:**
- Vista em lista/tabela
- Colunas:
  * Avatar/Foto
  * Nome
  * Número de Sócio
  * Email
  * Telefone
  * Estado
  * Tipos de Membro
  * Ações
- Filtros no topo (pesquisa, estado, tipo)
- Paginação no final
- Botão "Novo Membro"

**Estado Atual:**
 **Implementado**
- ✅ Tabela existe e funciona
- ✅ Filtros implementados
- ✅ Paginação existe no backend
- ⚠️ Alguns campos podem estar em falta

**Rotas API:**
- ✅ `/api/v2/membros` com filtros - Existe

**Discrepâncias:**
1. Verificar se todos os campos da tabela estão visíveis
2. Design visual pode precisar de ajustes finos
3. Avatar/foto provavelmente não implementado

### 2.3 Novo Membro.png
**Componentes Visíveis:**
- Modal/Form com campos:
  * Dados Pessoais (nome, NIF, email, telefone, data nascimento)
  * Dados de Membro (número sócio, data adesão, estado)
  * Tipos de Membro (checkboxes múltiplos)
  * Dados Desportivos (se atleta)
  * Observações
- Botões: Cancelar, Guardar
- Layout multi-secção

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Form existe em `/frontend/src/modules/members/MemberForm.tsx`
- ⚠️ Campos básicos implementados
- ❌ Dados desportivos não estão no form de criação
- ❌ Layout pode ser diferente

**Rotas API:**
- ✅ `POST /api/v2/membros` - Existe

**Discrepâncias:**
1. Dados desportivos (atleta) não estão integrados no form
2. Upload de foto/avatar não implementado
3. Validação pode precisar de melhorias
4. Design visual pode diferir

### 2.4 Membro_Pessoal.png
**Componentes Visíveis:**
- Página de detalhes do membro com tabs:
  * Pessoal
  * Desportivo
  * Financeiro
- Tab "Pessoal" mostra:
  * Foto/Avatar grande
  * Informação pessoal completa
  * Documentos
  * Contactos de emergência
  * Histórico

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Detalhes básicos em `/frontend/src/modules/members/MemberDetails.tsx`
- ❌ Sistema de tabs não implementado
- ❌ Documentos não implementados na UI
- ❌ Contactos de emergência não implementados
- ❌ Histórico não implementado

**Rotas API:**
- ✅ `/api/v2/membros/{id}` - Existe
- ⚠️ `/api/documentos` - Existe mas não integrado
- ❌ Contactos de emergência - **EM FALTA**
- ❌ Histórico de alterações - **EM FALTA**

**Discrepâncias:**
1. Falta sistema de tabs na página de detalhes
2. Documentos não listados/geridos
3. Contactos de emergência não existem
4. Histórico não implementado

### 2.5 Membros_Desportivo_DadosDesportivos.png
**Componentes Visíveis:**
- Tab "Desportivo" > Subtab "Dados Desportivos"
- Campos:
  * Número de camisola
  * Posição principal
  * Pé dominante
  * Altura
  * Peso
  * Equipas
  * Histórico desportivo

**Estado Atual:**
 **Parcialmente Implementado**
- ⚠️ Dados desportivos aparecem em MemberDetails se for atleta
- ❌ Não há tab dedicado
- ❌ Edição inline não implementada
- ❌ Histórico desportivo não implementado

**Rotas API:**
- ✅ `/api/atletas/{id}` - Existe
- ❌ Histórico desportivo - **EM FALTA**

**Discrepâncias:**
1. Tab/subtab não implementado
2. Edição não está separada por secções
3. Histórico desportivo em falta

### 2.6 Membros_Desportivo_Treinos.png
**Componentes Visíveis:**
- Lista de treinos do membro
- Colunas: Data, Treino, Equipa, Presença, Observações
- Filtros por data/período

**Estado Atual:**
 **Não Implementado**
- Não existe na página de detalhes do membro

**Rotas API:**
- ⚠️ `/api/treinos` existe mas precisa filtro por atleta
- ❌ `/api/atletas/{id}/treinos` - **EM FALTA**

**Discrepâncias:**
1. Componente totalmente em falta
2. Endpoint específico em falta
3. UI não existe

### 2.7 Membros_Desportivo_Presencas.png, RegistoPresencas.png
**Componentes Visíveis:**
- Histórico de presenças do membro
- Estatísticas (% presença, total treinos, faltas)
- Calendário visual com presenças marcadas
- Filtros por período

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ `/api/atletas/{id}/presencas` - **EM FALTA**
- ❌ `/api/atletas/{id}/estatisticas-presenca` - **EM FALTA**

**Discrepâncias:**
1. Componente totalmente em falta
2. Endpoints em falta
3. Calendário visual não existe

### 2.8 Membros_Desportivo_Planeamento.png
**Componentes Visíveis:**
- Planeamento de treinos do atleta
- Timeline/Gantt de macrociclos
- Objetivos e metas

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ Endpoints de planeamento - **EM FALTA**

### 2.9 Membros_Desportivo_Convocatorias.png
**Componentes Visíveis:**
- Lista de convocatórias do atleta
- Status (convocado/não convocado)
- Detalhes do evento

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ `/api/atletas/{id}/convocatorias` - **EM FALTA**

### 2.10 Membros_Desportivo_Resultados.png
**Componentes Visíveis:**
- Resultados de competições do atleta
- Tempos, posições, classificações
- Gráficos de evolução

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ `/api/atletas/{id}/resultados` - **EM FALTA**

### 2.11 Membros_Financeiro.png
**Componentes Visíveis:**
- Tab "Financeiro" com:
  * Resumo financeiro (saldo, total faturado, total pago)
  * Lista de faturas
  * Histórico de pagamentos
  * Gráfico de evolução

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Endpoints existem: `/api/v2/membros/{id}/conta-corrente`
- ❌ UI não implementada na página de membro
- ❌ Não está em tab

**Rotas API:**
- ✅ `/api/v2/membros/{id}/conta-corrente` - Existe
- ✅ `/api/v2/membros/{id}/resumo-financeiro` - Existe

**Discrepâncias:**
1. UI não implementada
2. Não está integrado na página de detalhes
3. Gráficos não implementados

---

## 3. DESPORTIVO (11 imagens)

### 3.1 Desportivo_Dashboard.png
**Componentes Visíveis:**
- Dashboard com:
  * Cards de stats (atletas, equipas, treinos)
  * Próximos treinos
  * Presenças recentes
  * Calendário mensal

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Página Sports.tsx existe
- ❌ Não tem dashboard, apenas tabs
- ❌ Cards de stats não implementados

**Rotas API:**
- ✅ Endpoints existem mas precisam de agregação

**Discrepâncias:**
1. Dashboard visual não implementado
2. Stats agregadas em falta
3. Layout completamente diferente

### 3.2 Desportivo_Treinos.png
**Componentes Visíveis:**
- Lista de treinos
- Colunas: Data, Hora, Equipa, Local, Tipo, Presenças, Ações
- Filtros
- Botão "Novo Treino"

**Estado Atual:**
 **Implementado**
- ✅ TreinosTab.tsx existe
- ✅ Lista e tabela implementadas

**Rotas API:**
- ✅ `/api/treinos` - Existe

**Discrepâncias:**
1. Verificar se todas as colunas estão presentes
2. Design visual pode diferir

### 3.3 Desportivo_Planeamento.png
**Componentes Visíveis:**
- Sistema de planeamento com:
  * Épocas
  * Macrociclos
  * Mesociclos
  * Microciclos
- Timeline visual
- Drill-down hierárquico

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ Endpoints de planeamento - **TODOS EM FALTA**

**Discrepâncias:**
1. Módulo completo em falta
2. Base de dados (tabelas) provavelmente em falta
3. UI complexa não existe

### 3.4 Desportivo_Planeamento_NovaEpoca.png
### 3.5 Desportivo_Planeamento_NovoMacrociclo.png
### 3.6 Desportivo_Planeamento_NovoTreino.png
**Estado Atual:**
 **Não Implementados**
- Forms de criação para planeamento não existem

### 3.7 Desportivo_Competicoes.png
**Componentes Visíveis:**
- Lista de competições
- Informação: nome, época, tipo, equipas participantes
- Classificação/resultados

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ `/api/competicoes` existe
- ❌ UI não implementada no frontend

**Rotas API:**
- ✅ `/api/competicoes` - Existe
- ⚠️ Pode precisar de campos adicionais

**Discrepâncias:**
1. UI não implementada
2. Gestão de classificações pode estar em falta

### 3.8 Desportivo_Presencas.png
### 3.9 Desportivo_Presencas_Registrar_1.png
### 3.10 Desportivo_Presencas_Registrar_2.png
**Componentes Visíveis:**
- Lista de presenças por treino
- Interface de registo rápido (checkboxes)
- Filtros por data/equipa
- Estatísticas de presença

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Endpoint existe: `/api/treinos/{id}/presencas`
- ❌ UI de registo não implementada
- ❌ Interface rápida não existe

**Rotas API:**
- ✅ `POST /api/treinos/{id}/presencas` - Existe
- ✅ `GET /api/treinos/{id}/estatisticas-presenca` - Existe

**Discrepâncias:**
1. UI de registo rápido não implementada
2. Interface de checkboxes não existe
3. Estatísticas não visíveis

### 3.11 Desportivo_Relatorios.png
**Componentes Visíveis:**
- Relatórios desportivos:
  * Presenças por atleta
  * Presenças por equipa
  * Evolução de performance
  * Gráficos e exports

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ Endpoints de relatórios - **EM FALTA**

**Discrepâncias:**
1. Módulo completo em falta
2. Geração de relatórios não implementada
3. Gráficos não existem

---

## 4. EVENTOS (16 imagens)

### 4.1 Eventos_Calendario.png
**Componentes Visíveis:**
- Calendário mensal visual
- Eventos marcados por dia
- Click no dia para ver/criar eventos
- Cores por tipo de evento

**Estado Atual:**
 **Não Implementado**
- Events.tsx não tem vista de calendário

**Rotas API:**
- ✅ `/api/eventos` existe

**Discrepâncias:**
1. Vista de calendário não implementada
2. Precisa biblioteca de calendário
3. UI visual não existe

### 4.2 Eventos_Eventos.png
**Componentes Visíveis:**
- Lista de eventos
- Colunas: Data, Título, Tipo, Local, Inscrições, Ações
- Filtros
- Botão "Novo Evento"

**Estado Atual:**
 **Implementado**
- ✅ Events.tsx implementado
- ✅ Lista e filtros funcionam

**Rotas API:**
- ✅ `/api/eventos` - Existe

**Discrepâncias:**
1. Verificar campos da tabela
2. Design visual

### 4.3 Eventos_Eventos_NovoEvento.png
**Componentes Visíveis:**
- Form de criação com campos:
  * Título
  * Tipo
  * Data/hora início e fim
  * Local
  * Descrição
  * Inscrições (checkbox, limite, prazo)
  * Custo
  * Visibilidade

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ CreateEventoModal.tsx existe
- ⚠️ Pode faltar alguns campos

**Rotas API:**
- ✅ `POST /api/eventos` - Existe

**Discrepâncias:**
1. Verificar se todos os campos estão no form
2. Validação

### 4.4 Eventos_Eventos_EditarEventos.png
**Estado:** ⚠️ Parcial - Similar ao NovoEvento

### 4.5 Eventos_Convocatorias.png
**Componentes Visíveis:**
- Lista de convocatórias
- Informação: evento, data, convocados, status
- Botão "Nova Convocatória"

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ Convocatórias - **ENDPOINTS EM FALTA**

**Discrepâncias:**
1. Módulo completo em falta
2. Tabela BD provavelmente em falta
3. UI não existe

### 4.6 Eventos_Convocatorias_NovaConvocatoria.png
### 4.7 Eventos_Convocatorias_EditarConvocatoria.png
### 4.8 Eventos_Convocatorias_ExportPdf.png
**Estado Atual:**
 **Não Implementados**

**Discrepâncias:**
1. Forms não existem
2. Export PDF não implementado
3. Sistema de templates em falta

### 4.9 Eventos_Config.png
**Componentes Visíveis:**
- Configuração de tipos de eventos
- Lista de tipos existentes
- Botão "Novo Tipo"

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ `/api/tipos-evento` - **EM FALTA**

**Discrepâncias:**
1. Gestão de tipos não implementada
2. Endpoint em falta
3. UI não existe

### 4.10 Eventos_Config_NovoTipo.png
### 4.11 Eventos_Config_EditarTipo.png
**Estado:** ❌ Não implementados

### 4.12 Eventos_Presencas.png (ver secção Presenças)

### 4.13 Eventos_Resultados.png (ver secção Resultados)

### 4.14 Eventos_Relatorios_Geral.png
### 4.15 Eventos_Relatorios_PorAtleta.png
### 4.16 Eventos_Relatorios_PorEvento.png
**Estado Atual:**
 **Não Implementados**

**Rotas API:**
- ❌ Endpoints de relatórios - **EM FALTA**

---

## 5. PRESENÇAS (3 imagens)

### 5.1 Eventos_Presencas.png
**Componentes Visíveis:**
- Lista de presenças em eventos
- Filtros por evento/data
- Estatísticas

**Estado Atual:**
 **Parcialmente Implementado**
- ✅ Endpoint `/api/eventos/{id}/inscrever` existe
- ❌ Gestão de presenças não implementada

**Rotas API:**
- ⚠️ `/api/eventos/{id}/inscrever` - Existe (para inscrições)
- ❌ `/api/eventos/{id}/presencas` - **EM FALTA** (para presenças)

### 5.2 Eventos_Presencas_NovaPresenca.png
### 5.3 Eventos_Presencas_RegistrarPresenca.png
**Estado:** ❌ Não implementados

---

## 6. RESULTADOS (3 imagens)

### 6.1 Eventos_Resultados.png
**Componentes Visíveis:**
- Lista de resultados de competições
- Colunas: Evento, Data, Atleta, Posição, Tempo/Pontos

**Estado Atual:**
 **Não Implementado**

**Rotas API:**
- ❌ Sistema de resultados - **COMPLETAMENTE EM FALTA**

### 6.2 Eventos_Resultados_NovoResultado.png
### 6.3 Eventos_Resultados_EditarResultado.png
**Estado:** ❌ Não implementados

---

## 7. FINANCEIRO (10 imagens)

### 7.1 Financeiro_Dashboard.png
**Componentes Visíveis:**
- Cards: Receitas, Despesas, Saldo, Faturas Pendentes
- Gráfico de evolução mensal
- Lista de movimentos recentes

**Estado Atual:**
 **Não Implementado**
- Financial.tsx não tem dashboard, começa direto na lista

**Rotas API:**
- ❌ Endpoints de dashboard financeiro - **EM FALTA**

### 7.2 Financeiro_Mensalidades.png
**Componentes Visíveis:**
- Lista de mensalidades (faturas recorrentes)
- Colunas: Membro, Mês, Valor, Estado, Ações
- Botão "Gerar Mensalidades"

**Estado Atual:**
 **Implementado**
- ✅ Financial.tsx lista faturas
- ✅ GerarMensalidadesModal existe

**Rotas API:**
- ✅ `/api/v2/faturas` - Existe
- ✅ `POST /api/v2/faturas/gerar-mensalidades` - Existe

### 7.3 Financeiro_Mensalidades_GerarMensalidades.png
**Estado:** ✅ Implementado

### 7.4 Financeiro_Mensalidades_EditarFatura.png
### 7.5 Financeiro_Mensalidades_LiquidarFatura.png
### 7.6 Financeiro_Mensalidades_RegistoManual.png
**Estado Atual:**
 **Parcialmente Implementado**
- ✅ FaturaDetailsModal existe
- ⚠️ Pode faltar funcionalidades

**Rotas API:**
- ✅ `/api/v2/faturas/{id}` - Existe
- ✅ `POST /api/v2/faturas/{id}/pagamentos` - Existe

### 7.7 Financeiro_Movimentos.png
**Componentes Visíveis:**
- Lista de todos os movimentos (receitas + despesas)
- Colunas: Data, Tipo, Descrição, Categoria, Valor, Saldo
- Filtros por tipo/categoria/data

**Estado Atual:**
 **Não Implementado**
- Financial.tsx só mostra faturas, não todos os movimentos

**Rotas API:**
- ❌ `/api/movimentos` genérico - **EM FALTA**

### 7.8 Financeiro_Movimentos_NovoMovimento.png
### 7.9 Financeiro_Movimentos_EditarMovimento.png
### 7.10 Financeiro_Movimentos_LiquidarMovimento.png
**Estado:** ❌ Não implementados

### 7.11 Financeiro_Relatorios.png
**Estado:** ❌ Não implementado

---

## 8. BANCO (5 imagens)

### 8.1 Financeiro_Banco.png
**Componentes Visíveis:**
- Movimentos bancários
- Saldo de conta
- Extrato

**Estado Atual:**
 **Completamente Não Implementado**
- Módulo de banco não existe

**Rotas API:**
- ❌ Sistema de conciliação bancária - **COMPLETAMENTE EM FALTA**

### 8.2 Financeiro_Banco_ImportarExtrato.png
### 8.3 Financeiro_Banco_Catalogar.png
### 8.4 Financeiro_Banco_NovoMovimento.png
### 8.5 Financeiro_Banco_EditarMovimento.png
**Estado:** ❌ Não implementados

---

## 9. CONFIGURAÇÕES (6 imagens)

### 9.1 Configurações.png
**Componentes Visíveis:**
- Menu lateral com categorias:
  * Geral
  * Clube
  * Financeiro
  * Base de Dados
  * Notificações

**Estado Atual:**
 **Não Implementado**
- Não existe página de configurações no frontend

**Rotas API:**
- ⚠️ `/api/v2/configuracao/{userId}` existe mas é para utilizador, não clube

### 9.2 Configuracoes_Geral.png
### 9.3 Configuracoes_Clube.png
### 9.4 Configuracoes_Financeiro.png
### 9.5 Configuracoes_BaseDados.png
### 9.6 Configuracoes_Notificacoes.png
**Estado:** ❌ Não implementados

**Discrepâncias:**
1. Módulo completo de configurações em falta
2. Endpoints de configurações do clube em falta
3. UI não existe

---

## 10. ANÁLISE DETALHADA DE COMPONENTES VISUAIS E CORES

### Cores Identificadas nas Imagens:
1. **Cor Primária**: Azul (#3b82f6 ou similar) - Botões primários, links, highlights
2. **Cor de Sucesso**: Verde (#10b981) - Estados ativos, confirmações
3. **Cor de Aviso**: Amarelo/Âmbar (#f59e0b) - Estados pendentes, avisos
4. **Cor de Erro**: Vermelho (#ef4444) - Estados inativos, erros, cancelamentos
5. **Cor de Info**: Azul claro (#dbeafe) - Badges informativos
6. **Background**: Branco (#ffffff) para cards, cinza claro (#f8fafc) para página
7. **Texto**: Cinza escuro (#1e293b) para texto principal, (#64748b) para secundário
8. **Borders**: Cinza claro (#e2e8f0)

### Layout Patterns Identificados:

#### 1. Card Pattern
- Padding: 16-24px
- Border-radius: 8-12px
- Box-shadow: sutil (0 1px 3px rgba(0,0,0,0.1))
- Background: branco
- Border: opcional, cinza claro

#### 2. Table Pattern
- Header com background ligeiramente diferente
- Rows com hover effect
- Alternating rows (opcional)
- Actions column no final
- Badges inline para estados

#### 3. Modal Pattern
- Overlay escuro semi-transparente
- Modal centrado
- Header com título e botão fechar
- Body com padding generoso
- Footer com botões alinhados à direita
- Max-width: 600-800px (dependendo do conteúdo)

#### 4. Badge Pattern
- Small padding (4px 8px)
- Border-radius: 4px
- Font-size: 12px
- Font-weight: 500-600
- Background + cor de texto coordenados

#### 5. Button Pattern
**Primário:**
- Background: Azul (#3b82f6)
- Color: Branco
- Padding: 10px 20px
- Border-radius: 6px
- Hover: Azul mais escuro

**Secundário/Outline:**
- Background: Transparente
- Border: 1px sólido
- Color: Cor da border
- Hover: Background leve

#### 6. Form Pattern
- Labels acima dos inputs
- Input height: ~40px
- Padding interno: 10px
- Border: 1px sólido cinza claro
- Focus: Border azul + shadow
- Error state: Border vermelho + mensagem

#### 7. Dashboard Stats Card
- Icon grande no topo ou esquerda
- Número principal grande (32-48px)
- Label descritivo abaixo
- Opcional: variação/trend indicator

### Componentes Específicos por Módulo

#### MEMBROS - Detalhes com Tabs
**Layout:**
```
+----------------------------------+
|  [Avatar]  Nome do Membro        |
|            Número Sócio          |
|            [Badge Estado]        |
+----------------------------------+
|  [Pessoal] [Desportivo] [Financ]|
+----------------------------------+
|                                  |
|  Conteúdo do Tab Ativo           |
|                                  |
+----------------------------------+
```

**Tab Pessoal deve conter:**
- Informação pessoal (2 colunas)
- Documentos (lista com ações)
- Contactos emergência (lista)
- Histórico (timeline)

**Tab Desportivo deve conter:**
- Sub-tabs: Dados | Treinos | Presenças | Planeamento | Convocatórias | Resultados
- Cada sub-tab com conteúdo específico

**Tab Financeiro deve conter:**
- Resumo (cards com stats)
- Lista de faturas
- Histórico de pagamentos
- Gráfico de evolução

#### DESPORTIVO - Registo Presenças
**UI Sugerida:**
```
Treino: [Dropdown Treinos]
Data: [Date Picker]
Equipa: [Dropdown Equipas]

+------------------+----------+
| Atleta           | Presença |
+------------------+----------+
| João Silva       | [✓]      |
| Maria Santos     | [✓]      |
| Pedro Costa      | [ ]      |
+------------------+----------+

[Guardar Presenças]
```

#### EVENTOS - Calendário
**Bibliotecas Sugeridas:**
- FullCalendar
- React Big Calendar
- ou custom com CSS Grid

**Features:**
- Vista mensal
- Click em dia para adicionar
- Click em evento para ver detalhes
- Cores por tipo de evento
- Drag & drop (opcional)

#### FINANCEIRO - Dashboard
**Layout:**
```
+--------+--------+--------+--------+
| Receit | Despes | Saldo  | Penden |
| €X     | €Y     | €Z     | N      |
+--------+--------+--------+--------+
+----------------------------------+
|  [Gráfico Evolução Mensal]       |
|                                  |
+----------------------------------+
+----------------------------------+
|  Movimentos Recentes             |
|  [Lista]                         |
+----------------------------------+
```

---

## 11. ENDPOINTS API EM FALTA (DETALHADO)

### Prioridade CRÍTICA (P1):

```
GET    /api/dashboard/stats
       Response: { membros_ativos, atletas_ativos, encarregados, eventos_proximos, receitas_mes }

GET    /api/atletas/{id}/treinos
       Query: data_inicio, data_fim
       Response: { data: [treinos...] }

POST   /api/treinos/{id}/presencas/bulk
       Body: { presencas: [{ atleta_id, presente, observacoes }] }

GET    /api/atletas/{id}/presencas
       Query: data_inicio, data_fim, page
       Response: { data: [presencas...], stats: { total, presente, falta, % } }

GET    /api/v2/membros/{id}/tabs
       Response: { pessoal: {...}, desportivo: {...}, financeiro: {...} }

POST   /api/documentos/upload
       Body: multipart/form-data
       Response: { id, nome, url }

GET    /api/atletas/{id}/estatisticas-presenca
       Query: data_inicio, data_fim
       Response: { total_treinos, presencas, faltas, percentagem }
```

### Prioridade ALTA (P2):

```
# Convocatórias
GET    /api/convocatorias
POST   /api/convocatorias
GET    /api/convocatorias/{id}
PUT    /api/convocatorias/{id}
DELETE /api/convocatorias/{id}
POST   /api/convocatorias/{id}/atletas  (adicionar convocados)
GET    /api/convocatorias/{id}/export-pdf

# Resultados
GET    /api/resultados
POST   /api/resultados
GET    /api/resultados/{id}
PUT    /api/resultados/{id}
DELETE /api/resultados/{id}
GET    /api/atletas/{id}/resultados

# Contactos Emergência
GET    /api/v2/membros/{id}/contactos-emergencia
POST   /api/v2/membros/{id}/contactos-emergencia
PUT    /api/contactos-emergencia/{id}
DELETE /api/contactos-emergencia/{id}

# Histórico
GET    /api/v2/membros/{id}/historico
       Response: { data: [{ data, tipo, descricao, user }] }

# Configurações Clube
GET    /api/configuracoes/clube
PUT    /api/configuracoes/clube
GET    /api/configuracoes/financeiro
PUT    /api/configuracoes/financeiro
GET    /api/configuracoes/notificacoes
PUT    /api/configuracoes/notificacoes

# Movimentos Financeiros
GET    /api/movimentos
POST   /api/movimentos
GET    /api/movimentos/{id}
PUT    /api/movimentos/{id}
DELETE /api/movimentos/{id}
```

### Prioridade MÉDIA (P3):

```
# Planeamento Desportivo
GET    /api/epocas
POST   /api/epocas
GET    /api/macrociclos
POST   /api/macrociclos
GET    /api/mesociclos
POST   /api/mesociclos
GET    /api/microciclos
POST   /api/microciclos
GET    /api/atletas/{id}/planeamento

# Tipos de Evento
GET    /api/tipos-evento
POST   /api/tipos-evento
PUT    /api/tipos-evento/{id}
DELETE /api/tipos-evento/{id}

# Presenças em Eventos
GET    /api/eventos/{id}/presencas
POST   /api/eventos/{id}/presencas/registrar

# Relatórios
GET    /api/relatorios/desportivo
GET    /api/relatorios/eventos
GET    /api/relatorios/financeiro
       Query: tipo, data_inicio, data_fim, formato (json|pdf|excel)
```

### Prioridade BAIXA (P4):

```
# Conciliação Bancária
GET    /api/banco/contas
POST   /api/banco/contas
GET    /api/banco/movimentos
POST   /api/banco/importar-extrato
POST   /api/banco/catalogar/{movimento_id}

# Gestão de Categorias
GET    /api/categorias-financeiras
POST   /api/categorias-financeiras
PUT    /api/categorias-financeiras/{id}
DELETE /api/categorias-financeiras/{id}
```

---

## 12. WORK ITEMS PRIORIZADOS

### SPRINT 1 - Dashboard e Membros (2 semanas)
**Objetivo:** Completar funcionalidades básicas mais usadas

#### WI-001: Dashboard - Acesso Rápido
**Prioridade:** P1
**Estimativa:** 3 pontos
**Tarefas:**
- [ ] Criar componente QuickActions.tsx
- [ ] Adicionar grid de botões com ícones
- [ ] Integrar no Dashboard.tsx
- [ ] Testar navegação

#### WI-002: Dashboard - Stats Completas
**Prioridade:** P1
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Criar endpoint `/api/dashboard/stats`
- [ ] Backend: Agregar dados de encarregados
- [ ] Backend: Agregar receitas do mês
- [ ] Frontend: Atualizar Dashboard.tsx para usar novo endpoint
- [ ] Testar e validar números

#### WI-003: Membros - Vista em Cards
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Criar componente MemberCard.tsx
- [ ] Criar componente MemberGrid.tsx
- [ ] Adicionar toggle Lista/Cards em Members.tsx
- [ ] Salvar preferência do utilizador (localStorage)
- [ ] Ajustar responsividade
- [ ] Testar

#### WI-004: Membros - Sistema de Tabs
**Prioridade:** P1
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Refatorar MemberDetails.tsx para usar tabs
- [ ] Criar MemberPersonalTab.tsx
- [ ] Criar MemberSportsTab.tsx (com sub-tabs)
- [ ] Criar MemberFinancialTab.tsx
- [ ] Implementar navegação entre tabs
- [ ] Testar transições

#### WI-005: Membros - Documentos
**Prioridade:** P2
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Backend: Endpoint upload `/api/documentos/upload`
- [ ] Backend: Associar documentos a membros
- [ ] Frontend: Componente DocumentList.tsx
- [ ] Frontend: Componente DocumentUpload.tsx
- [ ] Integrar em MemberPersonalTab
- [ ] Implementar download
- [ ] Testar upload e listagem

#### WI-006: Membros - Contactos de Emergência
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Migration para tabela contactos_emergencia
- [ ] Backend: Endpoints CRUD
- [ ] Frontend: Componente EmergencyContacts.tsx
- [ ] Frontend: Modal para adicionar/editar
- [ ] Integrar em MemberPersonalTab
- [ ] Testar

### SPRINT 2 - Desportivo (2 semanas)

#### WI-007: Desportivo - Dashboard
**Prioridade:** P2
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Criar componente SportsDashboard.tsx
- [ ] Cards de estatísticas
- [ ] Lista de próximos treinos
- [ ] Widget de presenças recentes
- [ ] Integrar em Sports.tsx como tab ou vista inicial
- [ ] Testar

#### WI-008: Presenças - Registo Rápido
**Prioridade:** P1
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Backend: Endpoint bulk `/api/treinos/{id}/presencas/bulk`
- [ ] Frontend: Componente RegistoPresencasRapido.tsx
- [ ] Interface com checkboxes
- [ ] Seleção de treino/data/equipa
- [ ] Validação e feedback
- [ ] Testar performance com muitos atletas

#### WI-009: Atleta - Histórico de Treinos
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Endpoint `/api/atletas/{id}/treinos`
- [ ] Frontend: Componente AtletaTreinosTab.tsx
- [ ] Tabela com treinos
- [ ] Filtros por data
- [ ] Integrar em MemberSportsTab
- [ ] Testar

#### WI-010: Atleta - Histórico de Presenças
**Prioridade:** P1
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Backend: Endpoint `/api/atletas/{id}/presencas`
- [ ] Backend: Endpoint stats `/api/atletas/{id}/estatisticas-presenca`
- [ ] Frontend: Componente AtletaPresencasTab.tsx
- [ ] Cards com estatísticas (%, total, faltas)
- [ ] Tabela com histórico
- [ ] Filtros por período
- [ ] Calendário visual (opcional)
- [ ] Integrar em MemberSportsTab
- [ ] Testar

#### WI-011: Competições - UI
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Criar componente CompeticoesTab.tsx
- [ ] Listar competições
- [ ] Modal para criar/editar
- [ ] Integrar em Sports.tsx
- [ ] Testar

### SPRINT 3 - Eventos e Convocatórias (2 semanas)

#### WI-012: Eventos - Vista Calendário
**Prioridade:** P2
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Instalar biblioteca de calendário (FullCalendar ou React Big Calendar)
- [ ] Criar componente EventosCalendario.tsx
- [ ] Carregar eventos do mês
- [ ] Click para ver detalhes
- [ ] Click para criar evento
- [ ] Cores por tipo de evento
- [ ] Toggle entre vista lista/calendário
- [ ] Testar

#### WI-013: Convocatórias - Backend
**Prioridade:** P1
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Migration tabela convocatorias
- [ ] Migration tabela convocatoria_atleta (pivot)
- [ ] Model Convocatoria.php
- [ ] Controller ConvocatoriaController.php
- [ ] Endpoints CRUD
- [ ] Endpoint adicionar/remover atletas
- [ ] Validações
- [ ] Testar com Postman

#### WI-014: Convocatórias - Frontend
**Prioridade:** P1
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Criar módulo convocatorias
- [ ] Componente ConvocatoriasList.tsx
- [ ] Modal ConvocatoriaForm.tsx
- [ ] Modal ConvocatoriaDetails.tsx
- [ ] Seleção de atletas (multi-select)
- [ ] Integrar em Events.tsx como tab
- [ ] Testar

#### WI-015: Convocatórias - Export PDF
**Prioridade:** P3
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Instalar biblioteca PDF (dompdf ou similar)
- [ ] Backend: Template PDF de convocatória
- [ ] Backend: Endpoint `/api/convocatorias/{id}/export-pdf`
- [ ] Frontend: Botão de export
- [ ] Testar download

#### WI-016: Tipos de Evento - Config
**Prioridade:** P3
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Migration tabela tipos_evento
- [ ] Backend: Endpoints CRUD
- [ ] Frontend: Página ConfigEventos.tsx
- [ ] CRUD de tipos
- [ ] Testar

### SPRINT 4 - Financeiro (2 semanas)

#### WI-017: Financeiro - Dashboard
**Prioridade:** P2
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Backend: Endpoint `/api/financeiro/dashboard`
- [ ] Backend: Agregar receitas, despesas, saldo
- [ ] Frontend: Componente FinanceiroDashboard.tsx
- [ ] Cards com stats
- [ ] Gráfico de evolução mensal (Chart.js ou Recharts)
- [ ] Lista de movimentos recentes
- [ ] Integrar em Financial.tsx
- [ ] Testar

#### WI-018: Movimentos Financeiros
**Prioridade:** P2
**Estimativa:** 13 pontos
**Tarefas:**
- [ ] Backend: Migration tabela movimentos
- [ ] Backend: Model Movimento.php
- [ ] Backend: Controller MovimentoController.php
- [ ] Backend: Endpoints CRUD
- [ ] Backend: Sistema de categorias
- [ ] Frontend: Componente Movimentos.tsx
- [ ] Frontend: Filtros (tipo, categoria, data)
- [ ] Frontend: Modal criar/editar movimento
- [ ] Frontend: Cálculo de saldo acumulado
- [ ] Integrar em Financial.tsx como tab
- [ ] Testar

#### WI-019: Membro - Tab Financeiro
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Criar MemberFinancialTab.tsx
- [ ] Usar endpoints existentes conta-corrente e resumo
- [ ] Cards com resumo
- [ ] Tabela de faturas do membro
- [ ] Histórico de pagamentos
- [ ] Gráfico de evolução (opcional)
- [ ] Integrar em MemberDetails
- [ ] Testar

### SPRINT 5 - Resultados e Relatórios (2 semanas)

#### WI-020: Resultados - Backend
**Prioridade:** P2
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Migration tabela resultados
- [ ] Model Resultado.php
- [ ] Controller ResultadoController.php
- [ ] Endpoints CRUD
- [ ] Associação com eventos/competições
- [ ] Associação com atletas
- [ ] Validações
- [ ] Testar

#### WI-021: Resultados - Frontend
**Prioridade:** P2
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Componente Resultados.tsx
- [ ] Tabela de resultados
- [ ] Modal criar/editar resultado
- [ ] Filtros (evento, atleta, data)
- [ ] Integrar em Events.tsx como tab
- [ ] Testar

#### WI-022: Atleta - Resultados
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Endpoint `/api/atletas/{id}/resultados`
- [ ] Frontend: Componente AtletaResultadosTab.tsx
- [ ] Tabela de resultados do atleta
- [ ] Gráfico de evolução (opcional)
- [ ] Integrar em MemberSportsTab
- [ ] Testar

#### WI-023: Relatórios - Estrutura Base
**Prioridade:** P3
**Estimativa:** 13 pontos
**Tarefas:**
- [ ] Backend: Sistema de geração de relatórios
- [ ] Backend: Endpoints por tipo (desportivo, eventos, financeiro)
- [ ] Backend: Export JSON, PDF, Excel
- [ ] Frontend: Página Relatorios.tsx
- [ ] Frontend: Seleção de tipo de relatório
- [ ] Frontend: Filtros por período
- [ ] Frontend: Preview e download
- [ ] Implementar 2-3 relatórios básicos
- [ ] Testar

### SPRINT 6 - Planeamento e Configurações (2 semanas)

#### WI-024: Planeamento - Backend
**Prioridade:** P3
**Estimativa:** 13 pontos
**Tarefas:**
- [ ] Migrations: epocas, macrociclos, mesociclos, microciclos
- [ ] Models e relationships
- [ ] Controllers
- [ ] Endpoints CRUD para cada nível
- [ ] Validações hierárquicas
- [ ] Testar

#### WI-025: Planeamento - Frontend
**Prioridade:** P3
**Estimativa:** 13 pontos
**Tarefas:**
- [ ] Componente Planeamento.tsx
- [ ] Vista hierárquica (tree ou accordion)
- [ ] Drill-down entre níveis
- [ ] Forms para criar cada nível
- [ ] Timeline visual (opcional mas desejável)
- [ ] Integrar em Sports.tsx
- [ ] Testar

#### WI-026: Configurações - Página Base
**Prioridade:** P2
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Criar página Configuracoes.tsx
- [ ] Sidebar com categorias
- [ ] Estrutura de navegação
- [ ] Adicionar rota
- [ ] Testar navegação

#### WI-027: Configurações - Clube
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Tabela config_clube
- [ ] Backend: Endpoints GET/PUT
- [ ] Frontend: ConfigClube.tsx
- [ ] Form com dados do clube
- [ ] Upload de logo
- [ ] Testar

#### WI-028: Configurações - Financeiro
**Prioridade:** P2
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Config financeiro (valor mensalidade default, etc)
- [ ] Backend: Endpoints GET/PUT
- [ ] Frontend: ConfigFinanceiro.tsx
- [ ] Form com configurações
- [ ] Testar

#### WI-029: Configurações - Notificações
**Prioridade:** P3
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Config notificações
- [ ] Backend: Endpoints GET/PUT
- [ ] Frontend: ConfigNotificacoes.tsx
- [ ] Checkboxes para tipos de notificação
- [ ] Testar

### SPRINT 7 - Banco e Polimentos (2 semanas)

#### WI-030: Banco - Conciliação Básica
**Prioridade:** P4
**Estimativa:** 13 pontos
**Tarefas:**
- [ ] Backend: Tabela contas_bancarias
- [ ] Backend: Tabela movimentos_bancarios
- [ ] Backend: Endpoints CRUD
- [ ] Backend: Importação de extrato (CSV inicial)
- [ ] Backend: Catalogação (associar a movimento/fatura)
- [ ] Frontend: Página Banco.tsx
- [ ] Frontend: Lista de movimentos bancários
- [ ] Frontend: Upload de extrato
- [ ] Frontend: Interface de catalogação
- [ ] Integrar em Financial.tsx
- [ ] Testar

#### WI-031: Polimentos Visuais
**Prioridade:** P3
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Revisar todas as cores e garantir consistência
- [ ] Ajustar espaçamentos e padding
- [ ] Animações subtis (transições, hover effects)
- [ ] Loading states em todos os componentes
- [ ] Empty states com ilustrações/mensagens
- [ ] Skeleton loaders
- [ ] Testar responsividade em mobile
- [ ] Testar acessibilidade básica

#### WI-032: Avatar e Upload de Fotos
**Prioridade:** P3
**Estimativa:** 5 pontos
**Tarefas:**
- [ ] Backend: Storage de imagens
- [ ] Backend: Endpoint upload avatar
- [ ] Backend: Redimensionamento de imagens
- [ ] Frontend: Componente AvatarUpload.tsx
- [ ] Integrar em MemberForm e MemberDetails
- [ ] Mostrar avatar em lista/cards de membros
- [ ] Testar

#### WI-033: Histórico de Alterações
**Prioridade:** P3
**Estimativa:** 8 pontos
**Tarefas:**
- [ ] Backend: Sistema de audit log
- [ ] Backend: Endpoint `/api/v2/membros/{id}/historico`
- [ ] Backend: Registar alterações automaticamente
- [ ] Frontend: Componente HistoricoAlteracoes.tsx
- [ ] Timeline visual
- [ ] Filtros por tipo de alteração
- [ ] Integrar em MemberPersonalTab
- [ ] Testar

---

## 13. ESTIMATIVAS TOTAIS

### Resumo por Sprint:
- **Sprint 1**: 34 pontos (Dashboard e Membros)
- **Sprint 2**: 34 pontos (Desportivo)
- **Sprint 3**: 34 pontos (Eventos e Convocatórias)
- **Sprint 4**: 26 pontos (Financeiro)
- **Sprint 5**: 34 pontos (Resultados e Relatórios)
- **Sprint 6**: 49 pontos (Planeamento e Configurações)
- **Sprint 7**: 34 pontos (Banco e Polimentos)

**TOTAL**: ~245 pontos de história
**Duração Estimada**: 14 semanas (3.5 meses) com equipa de 2-3 developers

### Priorização Sugerida para MVP:
Se o objetivo for um MVP rápido, focar em:
1. Sprint 1 (WI-001 a WI-006) - Fundação
2. Sprint 2 (WI-008, WI-010) - Presenças (funcionalidade crítica)
3. Sprint 4 (WI-017, WI-019) - Financeiro básico
4. Parte do Sprint 3 (WI-013, WI-014) - Convocatórias

Isto daria um MVP funcional em ~6-8 semanas.

---

## 14. RECOMENDAÇÕES TÉCNICAS

### Frontend:
1. **Biblioteca de Componentes**: Considerar usar Shadcn/ui ou similar para acelerar
2. **Calendário**: React Big Calendar ou FullCalendar
3. **Gráficos**: Recharts ou Chart.js
4. **Forms**: React Hook Form + Zod para validação
5. **Estado Global**: Zustand ou Context API (já têm algum)
6. **Tabelas**: TanStack Table para tabelas complexas
7. **Date Pickers**: react-datepicker ou similar
8. **File Upload**: react-dropzone

### Backend:
1. **File Storage**: Laravel Storage com S3 ou local
2. **PDF Generation**: DomPDF ou Laravel Snappy
3. **Excel Export**: Laravel Excel
4. **Image Processing**: Intervention Image
5. **Audit Log**: Considerar package `owen-it/laravel-auditing`
6. **Permissions**: Spatie Laravel Permission (se já não estiver)

### Testing:
1. Testes E2E com Playwright (já têm estrutura)
2. Testes unitários frontend com Vitest
3. Testes backend com PHPUnit
4. Cobertura mínima de 70%

### Performance:
1. Lazy loading de componentes pesados
2. Pagination em todas as listas
3. Debounce em pesquisas
4. Cache de queries frequentes (React Query)
5. Indexes na base de dados para queries complexas

---

## 15. CONCLUSÃO

Este relatório analisou **65 imagens** de referência e identificou:

- **10 módulos principais** com diferentes níveis de implementação
- **33 work items priorizados** para desenvolvimento
- **~40%** das funcionalidades ainda não implementadas
- **~45%** parcialmente implementadas
- **~15%** totalmente implementadas

As maiores lacunas são:
1. **Planeamento Desportivo** - Sistema completo em falta
2. **Convocatórias e Resultados** - Módulos em falta
3. **Conciliação Bancária** - Sistema completo em falta
4. **Configurações** - Página e endpoints em falta
5. **Sistema de Tabs em Membros** - Refatoração necessária
6. **Relatórios** - Sistema completo em falta

As funcionalidades melhor implementadas:
1. ✅ **Lista de Membros** - Funcional
2. ✅ **Lista de Eventos** - Funcional
3. ✅ **Financeiro (Mensalidades)** - Funcional
4. ✅ **Lista de Treinos** - Funcional

**Próximo Passo Recomendado**: Começar pelo Sprint 1, focando em completar o Dashboard e o sistema de tabs em Membros, que são funcionalidades base que impactam toda a experiência do utilizador.

