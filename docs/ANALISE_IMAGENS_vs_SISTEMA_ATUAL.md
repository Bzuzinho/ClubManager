# Análise: Imagens de Referência vs Sistema Atual

Data: 26/01/2026

## 📊 Sumário Executivo

Após análise das imagens de referência em `docs/Imagens_prints_26012026/` e comparação com o código atual do sistema, identifiquei discrepâncias significativas em layout, componentes, cores e funcionalidades.

---

## 🎯 Dashboard

### Imagens Analisadas
- `Dashboard.png`
- `Dashboard_AcessoRapido.png`

### Estado Atual
✅ **Implementado Parcialmente**
- Cards de estatísticas existem
- Próximos eventos existe
- Atividade recente existe

❌ **Falta Implementar**
- **Acesso Rápido com ícones grandes** (visível em `Dashboard_AcessoRapido.png`)
  - Ícones coloridos e grandes para: Membros 👥, Desportivo ⚽, Eventos 📅, Financeiro 💶
  - Grid 2x2 ou 2x3 conforme imagens
- **Gráficos visuais** não implementados
- **Dashboard modular** (Membros, Desportivo, Financeiro têm dashboards próprios nas imagens)

### Rotas API
✅ `/v2/membros` - OK
✅ `/atletas` - OK
✅ `/eventos` - OK
✅ `/v2/faturas` - OK
❌ Endpoint específico `/api/dashboard/stats` - **NÃO EXISTE**

---

## 👥 Módulo MEMBROS

### Imagens Analisadas
- `Membros_Dashboard_Lista.png`
- `Membros_Dashboard_Cards.png`
- `Membro_Pessoal.png`
- `Novo Membro.png`
- `Membros_Desportivo_*.png` (7 imagens)
- `Membros_Financeiro.png`

### 1. Lista de Membros

#### Estado Atual (`Members.tsx`)
✅ Lista com cards
✅ Pesquisa por nome/email
✅ Filtros (estado, tipo)
✅ Botão "Novo Membro"

❌ **Diferenças vs Imagens**
- **Vista Cards vs Lista**: Imagens mostram opção toggle entre CARDS e LISTA
  - `Membros_Dashboard_Cards.png` → cards grandes com foto, nome, nº sócio, tipos
  - `Membros_Dashboard_Lista.png` → tabela compacta
  - **ATUAL**: Só tem cards pequenos
- **Cores dos badges de estado**:
  - Imagem mostra: Verde vibrante (#10b981) ✅ correto
  - Código atual: correto
- **Botão "Importar"**: Existe no código ✅ mas provavelmente não funcional

### 2. Perfil do Membro (Detalhes)

#### Tabs Esperadas (conforme imagens)
1. **Pessoal** (`Membro_Pessoal.png`)
   - Campos: Foto, Nome, NIF, Data Nascimento, Morada, Contactos, Encarregado (se menor)
   - **ESTADO**: ❌ NÃO IMPLEMENTADO (frontend básico existe mas incompleto)

2. **Desportivo** (7 sub-tabs!)
   - `Membros_Desportivo_DadosDesportivos.png` → Nº Federação, Escalão, Posição, etc
   - `Membros_Desportivo_Treinos.png` → Histórico de treinos
   - `Membros_Desportivo_RegistoPresencas.png` → Calendário presença
   - `Membros_Desportivo_Planeamento.png` → Planos de treino
   - `Membros_Desportivo_Convocatorias.png` → Lista convocatórias
   - `Membros_Desportivo_Resultados.png` → Resultados competição
   - **ESTADO**: ❌ NÃO IMPLEMENTADO

3. **Financeiro** (`Membros_Financeiro.png`)
   - Conta corrente
   - Histórico pagamentos
   - Faturas pendentes
   - **ESTADO**: ❌ NÃO IMPLEMENTADO no detalhe do membro

### Rotas API Necessárias
✅ `/v2/membros` - OK
✅ `/v2/membros/{id}` - OK
❌ `/v2/membros/{id}/dados-desportivos` - **NÃO EXISTE**
❌ `/v2/membros/{id}/treinos` - **NÃO EXISTE**
❌ `/v2/membros/{id}/presencas` - **NÃO EXISTE**
❌ `/v2/membros/{id}/convocatorias` - **NÃO EXISTE**
✅ `/v2/membros/{id}/conta-corrente` - **EXISTE na API**
✅ `/v2/membros/{id}/resumo-financeiro` - **EXISTE na API**

### Formulário "Novo Membro"

#### Conforme Imagem `Novo Membro.png`
Campos esperados:
- **Pessoa** (dropdown ou criar nova)
- **Número de Sócio** (auto ou manual)
- **Estado** (dropdown: Ativo, Inativo, Pendente, Suspenso)
- **Data de Inscrição** (date picker)
- **Tipos de Membro** (checkboxes: Atleta, Dirigente, Encarregado de Educação, Funcionário, Treinador)
- **Observações** (textarea)

**ESTADO**: ✅ `MemberForm.tsx` existe, mas precisa verificar campos

---

## ⚽ Módulo DESPORTIVO

### Imagens Analisadas
- `Desportivo_Dashboard.png`
- `Desportivo_Treinos.png`
- `Desportivo_Presencas.png`
- `Desportivo_Presencas_Registrar_1.png`
- `Desportivo_Presencas_Registrar_2.png`
- `Desportivo_Competicoes.png`
- `Desportivo_Planeamento.png`
- `Desportivo_Planeamento_NovaEpoca.png`
- `Desportivo_Planeamento_NovoMacrociclo.png`
- `Desportivo_Planeamento_NovoTreino.png`
- `Desportivo_Relatorios.png`

### Estado Atual (`Sports.tsx`)

#### Implementado
✅ **Tab Atletas**: Lista de atletas
✅ **Tab Equipas**: Cards de equipas
✅ **Tab Treinos**: Lista filtrada por data/equipa

#### ❌ Falta Implementar (conforme imagens)

1. **Dashboard Desportivo** (`Desportivo_Dashboard.png`)
   - Cards: Atletas Ativos, Equipas, Treinos Semana, Jogos Próximos
   - Gráfico de presenças
   - Calendário mensal

2. **Presenças** (`Desportivo_Presencas.png`)
   - Seleção de treino (data, hora, equipa)
   - Tabela com TODOS atletas da equipa
   - Checkboxes: Presente ✅, Falta ❌, Justificada 📝
   - Botão "Guardar Presenças"
   - **ATUAL**: Não existe esta funcionalidade

3. **Planeamento** (3 níveis!)
   - `Desportivo_Planeamento_NovaEpoca.png` → Época, data início/fim
   - `Desportivo_Planeamento_NovoMacrociclo.png` → Macrociclo (período de treino)
   - `Desportivo_Planeamento_NovoTreino.png` → Treino individual com objetivos
   - **ATUAL**: Não existe

4. **Competições** (`Desportivo_Competicoes.png`)
   - Lista de jogos/provas
   - Resultado, adversário, local
   - Convocatórias associadas
   - **ATUAL**: Não existe frontend

5. **Relatórios** (`Desportivo_Relatorios.png`)
   - Relatórios de presença
   - Relatórios de performance
   - **ATUAL**: Não existe

### Rotas API
✅ `/atletas` - OK
✅ `/equipas` - OK
✅ `/treinos` - OK
✅ `/competicoes` - OK
✅ `/treinos/{id}/presencas` - POST para registar **EXISTE**
✅ `/treinos/{id}/estatisticas-presenca` - **EXISTE**
❌ `/planeamento/epocas` - **NÃO EXISTE**
❌ `/planeamento/macrociclos` - **NÃO EXISTE**
❌ `/relatorios/presencas` - **NÃO EXISTE**
❌ `/relatorios/performance` - **NÃO EXISTE**

---

## 📅 Módulo EVENTOS

### Imagens Analisadas
- `Eventos_Calendario.png`
- `Eventos_Eventos.png`
- `Eventos_Eventos_NovoEvento.png`
- `Eventos_Eventos_EditarEventos.png`
- `Eventos_Convocatorias.png`
- `Eventos_Convocatorias_NovaConvocatoria.png`
- `Eventos_Convocatorias_EditarConvocatoria.png`
- `Eventos_Convocatorias_ExportPdf.png`
- `Eventos_Config.png`
- `Eventos_Config_NovoTipo.png`
- `Eventos_Config_EditarTipo.png`

### Estado Atual (`Events.tsx`)

#### Implementado
✅ Lista de eventos (cards visuais)
✅ Modal "Novo Evento"
✅ Modal "Detalhes do Evento"
✅ Inscrições em eventos

#### ❌ Falta Implementar

1. **Calendário Visual** (`Eventos_Calendario.png`)
   - Vista mensal tipo Google Calendar
   - Eventos coloridos por tipo
   - Drag & drop (opcional)
   - **ATUAL**: Só lista linear

2. **Convocatórias** (sistema completo!)
   - `Eventos_Convocatorias.png` → Lista convocatórias
   - `Eventos_Convocatorias_NovaConvocatoria.png` → Form: Jogo, Data, Atletas, Observações
   - `Eventos_Convocatorias_EditarConvocatoria.png` → Editar convocatória
   - `Eventos_Convocatorias_ExportPdf.png` → Exportar PDF formatado
   - **ATUAL**: ❌ NÃO EXISTE

3. **Configuração de Tipos de Evento** (`Eventos_Config.png`)
   - Criar/Editar tipos personalizados
   - Definir cor, ícone, se requer inscrição
   - **ATUAL**: ❌ NÃO EXISTE no frontend

### Rotas API
✅ `/eventos` - OK
✅ `/eventos/{id}` - OK
✅ `/eventos/{id}/inscrever` - POST **EXISTE**
❌ `/convocatorias` - **NÃO EXISTE** (rota `/competicoes/{id}/convocar` existe mas não é o mesmo)
❌ `/convocatorias/{id}/pdf` - **NÃO EXISTE**
❌ `/tipos-evento` - **NÃO EXISTE** (tabela existe: `tipos_evento`)

---

## 💶 Módulo FINANCEIRO

### Imagens Analisadas
- `Financeiro_Dashboard.png`
- `Financeiro_Mensalidades.png`
- `Financeiro_Mensalidades_GerarMensalidades.png`
- `Financeiro_Mensalidades_EditarFatura.png`
- `Financeiro_Mensalidades_LiquidarFatura.png`
- `Financeiro_Mensalidades_RegistoManual.png`
- `Financeiro_Movimentos.png`
- `Financeiro_Movimentos_NovoMovimento.png`
- `Financeiro_Movimentos_EditarMovimento.png`
- `Financeiro_Movimentos_LiquidarMovimento.png`

### Estado Atual (`Financial.tsx`)

#### Implementado
✅ Lista de faturas (tabela)
✅ Filtros (estado, mês)
✅ Modal "Criar Fatura"
✅ Modal "Gerar Mensalidades"
✅ Modal "Detalhes da Fatura"

#### ❌ Falta Implementar

1. **Dashboard Financeiro** (`Financeiro_Dashboard.png`)
   - Cards: Receitas Mês, Despesas Mês, Saldo, Pendentes
   - Gráfico de barras (receitas/despesas mensal)
   - Gráfico pizza (distribuição despesas)
   - **ATUAL**: ❌ NÃO EXISTE

2. **Movimentos** (sistema completo!)
   - `Financeiro_Movimentos.png` → Lista TODAS transações (não só faturas)
   - Tipos: Receita, Despesa, Transferência
   - Categorias: Mensalidades, Torneios, Material, Salários, etc.
   - **ATUAL**: ❌ NÃO EXISTE (só faturas/mensalidades)

3. **Registos Manuais** (`Financeiro_Mensalidades_RegistoManual.png`)
   - Pagamento em dinheiro/MB/transferência
   - Comprovativo (upload)
   - **ATUAL**: Parcialmente (modal existe mas falta upload)

### Rotas API
✅ `/v2/faturas` - OK
✅ `/v2/faturas/{id}` - OK
✅ `/v2/faturas/gerar-mensalidades` - POST **EXISTE**
✅ `/v2/faturas/{id}/pagamentos` - POST **EXISTE**
❌ `/movimentos` - **NÃO EXISTE**
❌ `/categorias-financeiras` - **NÃO EXISTE**
❌ `/dashboard/financeiro` - **NÃO EXISTE**

---

## 🏦 Módulo BANCO

### Imagens Analisadas
(Pasta `Banco/` vazia ou sem imagens listadas)

**ESTADO**: ⚠️ Não analisado

---

## ⚙️ Módulo CONFIGURAÇÕES

### Imagens Analisadas
- `Configurações.png`

### Esperado (baseado em sistema típico)
- Dados do clube
- Utilizadores e permissões
- Tipos de membro
- Modalidades/escalões
- Integra��ões

**ESTADO**: ❌ NÃO IMPLEMENTADO no frontend

### Rotas API
✅ `/v2/configuracao/{userId}` - **EXISTE**
❌ `/configuracoes/clube` - **NÃO EXISTE**
❌ `/configuracoes/tipos-membro` - Existe tabela mas falta CRUD completo

---

## 📊 Módulo PRESENÇAS

### Imagens Analisadas
(Pasta `Presenças/` sem imagens listadas explicitamente, mas presente em Desportivo)

Ver secção **Desportivo → Presenças**

---

## 📈 Módulo RELATÓRIOS

### Imagens Analisadas
(Pasta `Relatorios/` sem imagens listadas)

**ESTADO**: ❌ NÃO IMPLEMENTADO

### Esperado
- Relatórios de presenças
- Relatórios financeiros
- Relatórios de performance
- Exportação PDF/Excel

---

## 📋 Módulo RESULTADOS

### Imagens Analisadas
(Pasta `Resultados/` sem imagens listadas)

Ver secção **Desportivo → Competições**

---

## 🎨 ANÁLISE DE DESIGN & UX

### Cores (baseado nas imagens)

#### Tema Geral
- **Primária**: Azul (#3b82f6 ou similar) - Botões principais
- **Sucesso**: Verde (#10b981) - Estados "Ativo", "Paga"
- **Aviso**: Amarelo/Laranja (#f59e0b) - Estados "Pendente"
- **Erro**: Vermelho (#ef4444) - Estados "Cancelada", "Suspenso"
- **Neutro**: Cinza (#6b7280) - Estados "Inativo"

#### Código Atual
✅ Cores estão corretas em `Members.tsx` e `Financial.tsx`
⚠️ Falta consistência em todos os módulos

### Componentes UI

#### Implementados
✅ Cards
✅ Tabelas
✅ Modais
✅ Botões
✅ Forms básicos
✅ Badges de estado

#### ❌ Falta Implementar
- **Calendário** (mensal, tipo FullCalendar)
- **Gráficos** (Chart.js ou similar)
- **Toggle Lista/Cards**
- **Drag & Drop**
- **Upload de ficheiros com preview**
- **Tabs aninhados** (ex: Membro → Desportivo → Treinos)
- **Exportação PDF**
- **Datepickers avançados**
- **Autocomplete/Select com pesquisa**

---

## 🔌 ANÁLISE DE ROTAS & API

### Arquitetura Atual

#### ✅ Pontos Fortes
1. **Rotas v2** bem estruturadas (membros, faturas, configuração)
2. **Middleware** `ensure.club.context` correto
3. **Auth Sanctum** funcional
4. **CORS** configurado para GitHub Codespaces

#### ❌ Problemas Identificados

1. **Rotas Antigas vs Novas**
   - Existe duplicação: `/membros` (antiga) + `/v2/membros` (nova)
   - `/pessoas` tenta aceder tabela inexistente
   - `/tipos-membro` com erro (tabela existe mas controller tem bug)

2. **Rotas em Falta** (conforme funcionalidades nas imagens)
   - `/movimentos` (Financeiro)
   - `/categorias-financeiras` (Financeiro)
   - `/convocatorias` (Eventos/Desportivo)
   - `/planeamento/*` (Desportivo)
   - `/relatorios/*` (Global)
   - `/tipos-evento` CRUD (Eventos)
   - `/dashboard/*` endpoints específicos

3. **Endpoints PostgreSQL vs Código**
   - Alguns models referem relações que não existem na BD
   - Ex: `atleta.dadosDesportivos` (tabela `dados_desportivos_atleta` não existe)

---

## ✅ PRIORIZAÇÃO DE WORK ITEMS

### 🔴 CRÍTICO (Bloqueiam uso básico)

1. **Corrigir erros PostgreSQL**
   - ✅ FEITO: `User.php` - wherePivot boolean
   - ✅ FEITO: `MembrosController.php` - remover `atleta.dadosDesportivos`
   - ❌ TODO: Remover/comentar rotas `/pessoas` e `/tipos-membro` antigas

2. **Perfil do Membro - Tabs Básicos**
   - Implementar tab "Pessoal" com todos os campos
   - Implementar tab "Financeiro" (conta corrente já tem API)

### 🟠 ALTO (Funcionalidades principais)

3. **Desportivo - Presenças**
   - Interface para registar presenças em massa
   - Usar endpoint `/treinos/{id}/presencas` existente

4. **Financeiro - Dashboard**
   - Cards de resumo (receitas, despesas, saldo)
   - Gráficos básicos

5. **Eventos - Calendário**
   - Vista mensal com biblioteca (ex: FullCalendar)
   - Código backend já existe

6. **Toggle Lista/Cards nos Membros**
   - Implementar 2 vistas conforme imagens

### 🟡 MÉDIO (Melhorias UX)

7. **Convocatórias (Eventos/Desportivo)**
   - CRUD completo
   - Exportação PDF
   - Precisa: Backend + Frontend

8. **Movimentos Financeiros**
   - Sistema completo receitas/despesas
   - Precisa: Backend + Frontend

9. **Configurações**
   - Interface para gerir tipos membro, modalidades, etc.
   - Backend parcial existe

### 🟢 BAIXO (Nice to have)

10. **Planeamento Desportivo**
    - Épocas, Macrociclos
    - Sistema complexo

11. **Relatórios**
    - PDFs, Excel
    - Gráficos avançados

12. **Design Polish**
    - Animações
    - Drag & drop
    - Upload com preview

---

## 📝 RECOMENDAÇÕES TÉCNICAS

### Frontend
1. **Instalar bibliotecas**:
   ```bash
   npm install @fullcalendar/react @fullcalendar/daygrid
   npm install chart.js react-chartjs-2
   npm install react-dropzone
   ```

2. **Refatorar estrutura** de `MemberProfile.tsx`:
   ```tsx
   /membros/{id}
     ├── Tab: Pessoal
     ├── Tab: Desportivo
     │   ├── SubTab: Dados
     │   ├── SubTab: Treinos
     │   ├── SubTab: Presenças
     │   └── SubTab: Convocatórias
     └── Tab: Financeiro
         ├── Conta Corrente
         └── Histórico
   ```

3. **Criar componentes reutilizáveis**:
   - `<Calendar />` para eventos e presenças
   - `<Chart />` para gráficos dashboards
   - `<FileUpload />` para comprovantes
   - `<ExportButton />` para PDFs

### Backend
1. **Criar controllers em falta**:
   - `MovimentosController` (receitas/despesas)
   - `ConvocatoriasController` (convocatórias)
   - `PlaneamentoController` (épocas/macrociclos)
   - `RelatoriosController` (PDFs)
   - `TiposEventoController` (CRUD tipos evento)

2. **Criar endpoints dashboard**:
   ```php
   Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
   Route::get('/dashboard/financeiro', [DashboardController::class, 'financeiro']);
   Route::get('/dashboard/desportivo', [DashboardController::class, 'desportivo']);
   ```

3. **Corrigir models**:
   - Remover relações que não existem na BD
   - Adicionar casts corretos para PostgreSQL
   - Documentar relações esperadas

### Database
1. **Verificar tabelas**:
   - ✅ `clubs`, `users`, `membros` - OK
   - ⚠️ `pessoas` - **NÃO EXISTE**, remover dependências
   - ⚠️ `dados_desportivos_atleta` - não existe, usar `dados_desportivos`
   - ✅ `tipos_evento`, `tipos_membro`, `escaloes` - EXISTEM

2. **Migrations em falta**:
   - `movimentos` (tabela financeira geral)
   - `categorias_financeiras`
   - `convocatorias`
   - `planeamento_epocas`
   - `planeamento_macrociclos`

---

## 🎯 CONCLUSÃO

**Sistema está 40% completo** em relação às imagens de referência.

### ✅ O que funciona bem
- Autenticação
- Lista de membros básica
- Lista de eventos básica
- Lista de faturas
- Estrutura de rotas v2

### ❌ Gaps principais
1. **Perfil do Membro** incompleto (falta 70% das tabs)
2. **Presenças** não implementado (frontend inexistente)
3. **Dashboards** sem gráficos
4. **Calendário** inexistente
5. **Convocatórias** não implementado
6. **Movimentos Financeiros** não implementado
7. **Configurações** não implementado

### 🚀 Próximos Passos Recomendados
1. Corrigir erros críticos PostgreSQL
2. Implementar tab "Pessoal" do membro (alta prioridade, baixa complexidade)
3. Implementar presenças (alta prioridade, média complexidade)
4. Adicionar gráficos aos dashboards (impacto visual grande)
5. Implementar calendário de eventos (diferencial UX)

---

**Documento gerado automaticamente pela análise de código e imagens de referência**  
**Última atualização**: 26/01/2026 - 10:15 UTC
