# Comparação: Arquitetura Planejada vs. Implementação Atual
**Data:** 21 de Janeiro de 2026

## 📐 Arquitetura da Base de Dados (Fluxograma)

Este documento compara a arquitetura definida no fluxograma da base de dados com o que está atualmente implementado.

---

## 🗂️ MÓDULO: GESTÃO DE MEMBROS

### Tabelas Planejadas na Arquitetura

#### Core - Utilizadores e Pessoas
| Tabela | Status | Notas |
|--------|--------|-------|
| `users` | ✅ Implementada | Tabela base de autenticação Laravel |
| `pessoas` | ⚠️ Model existe | Sem migration correspondente |
| `dados_pessoais` | ✅ Implementada | Criada recentemente |
| `dados_configuracao` | ✅ Implementada | RGPD e consentimentos |

#### Membros
| Tabela | Status | Notas |
|--------|--------|-------|
| `membros` | ⚠️ Model existe | Sem migration! CRÍTICO |
| `tipos_membro` | ⚠️ Model existe (`TipoMembro`) | Sem migration |
| `membros_tipos` | ⚠️ Model existe (`MembroTipo`) | Tabela pivot - sem migration |
| `historico_estados` | ❌ Não existe | Auditoria de mudanças de estado |

#### Atletas e Encarregados
| Tabela | Status | Notas |
|--------|--------|-------|
| `atletas` | ⚠️ Model existe | Sem migration |
| `encarregados_educacao` | ❌ Não existe | Precisa ser criada |
| `atletas_encarregados` | ❌ Não existe | Relação N:N entre atletas e EE |
| `relacoes_pessoas` | ⚠️ Model existe (`RelacaoPessoa`) | Sem migration |

#### Documentos
| Tabela | Status | Notas |
|--------|--------|-------|
| `documentos` | ❌ Não existe | Tabela polimórfica para qualquer entidade |
| `tipos_documento` | ❌ Não existe | Categoria de documentos |

### Relações Esperadas (Eloquent)
```
User (1) -----> (1) DadosPessoais
User (1) -----> (1) DadosConfiguracao
User (1) -----> (1) Pessoa
Pessoa (1) ---> (1) Membro
Membro (N) ---> (N) TipoMembro (via membros_tipos)
Membro (1) ---> (1) Atleta
Atleta (N) ---> (N) EncarregadoEducacao (via atletas_encarregados)
```

**Status Relações:** ⚠️ Parcialmente definidas nos models existentes, mas sem suporte de migrations

---

## 🏃 MÓDULO: GESTÃO DESPORTIVA

### Tabelas Planejadas na Arquitetura

#### Modalidades e Equipas
| Tabela | Status | Notas |
|--------|--------|-------|
| `modalidades` | ❌ Não existe | Desportos oferecidos pelo clube |
| `equipas` | ❌ Não existe | Equipas por modalidade/escalão |
| `escaloes` | ⚠️ Model existe (`Escalao`) | Sem migration |
| `atletas_escaloes` | ⚠️ Model existe (`AtletaEscalao`) | Sem migration |
| `atletas_equipas` | ❌ Não existe | Relação N:N entre atletas e equipas |

#### Treinos e Presenças
| Tabela | Status | Notas |
|--------|--------|-------|
| `treinos` | ❌ Não existe | Sessões de treino agendadas |
| `presencas_treino` | ❌ Não existe | Controle de presenças |
| `tipos_presenca` | ❌ Não existe | Presente/Falta/Justificada/Atrasado |

#### Competições
| Tabela | Status | Notas |
|--------|--------|-------|
| `competicoes` | ❌ Não existe | Jogos, torneios, provas |
| `convocatorias` | ❌ Não existe | Atletas convocados para competições |
| `resultados_competicao` | ❌ Não existe | Pontuações e classificações |
| `tipos_competicao` | ❌ Não existe | Jogo/Torneio/Treino-jogo/etc |

#### Dados Desportivos
| Tabela | Status | Notas |
|--------|--------|-------|
| `dados_desportivos` | ⚠️ Model existe | Sem migration |
| `estatisticas_atleta` | ❌ Não existe | Golos, assistências, cartões, etc |

### Relações Esperadas (Eloquent)
```
Modalidade (1) ---> (N) Equipa
Equipa (N) ------> (N) Atleta (via atletas_equipas)
Equipa (1) ------> (N) Treino
Treino (1) ------> (N) PresencaTreino
Atleta (1) ------> (N) PresencaTreino
Competicao (1) --> (N) Convocatoria
Atleta (1) ------> (N) Convocatoria
Atleta (1) ------> (1) DadosDesportivos
Atleta (1) ------> (N) EstatisticaAtleta
```

**Status Relações:** ❌ Nada implementado

---

## 📅 MÓDULO: GESTÃO DE EVENTOS

### Tabelas Planejadas na Arquitetura

| Tabela | Status | Notas |
|--------|--------|-------|
| `eventos` | ❌ Não existe | Eventos gerais do clube |
| `tipos_evento` | ❌ Não existe | Categoria: Social/Desportivo/Formação/etc |
| `inscricoes_evento` | ❌ Não existe | Inscrições de membros em eventos |
| `estados_inscricao` | ❌ Não existe | Pendente/Confirmada/Cancelada |

### Relações Esperadas (Eloquent)
```
TipoEvento (1) ---> (N) Evento
Evento (1) -------> (N) InscricaoEvento
Membro (1) -------> (N) InscricaoEvento
```

**Status Relações:** ❌ Nada implementado

---

## 💰 MÓDULO: GESTÃO FINANCEIRA

### Tabelas Planejadas na Arquitetura

#### Faturas e Pagamentos
| Tabela | Status | Notas |
|--------|--------|-------|
| `faturas` | ❌ Não existe | Invoices geradas |
| `itens_fatura` | ❌ Não existe | Linhas de cada fatura |
| `estados_fatura` | ❌ Não existe | Pendente/Paga/Vencida/Cancelada |
| `pagamentos` | ❌ Não existe | Registos de pagamento |
| `metodos_pagamento` | ❌ Não existe | MB/MBWay/Transferência/Dinheiro/etc |

#### Movimentos Financeiros
| Tabela | Status | Notas |
|--------|--------|-------|
| `movimentos_financeiros` | ❌ Não existe | Receitas e despesas |
| `tipos_movimento` | ❌ Não existe | Receita/Despesa |
| `categorias_movimento` | ❌ Não existe | Quota/Material/Transporte/etc |
| `centros_custo` | ❌ Não existe | Departamentos ou modalidades |

#### Contas Bancárias
| Tabela | Status | Notas |
|--------|--------|-------|
| `contas_bancarias` | ❌ Não existe | Contas do clube |
| `movimentos_bancarios` | ❌ Não existe | Extrato bancário importado |
| `reconciliacoes` | ❌ Não existe | Conciliação de movimentos |

### Relações Esperadas (Eloquent)
```
Membro (1) ----------> (N) Fatura
Fatura (1) ----------> (N) ItemFatura
Fatura (1) ----------> (N) Pagamento
MetodoPagamento (1) -> (N) Pagamento
CentroCusto (1) -----> (N) MovimentoFinanceiro
CategoriaMovimento (1) -> (N) MovimentoFinanceiro
ContaBancaria (1) ---> (N) MovimentoBancario
```

**Status Relações:** ❌ Nada implementado

---

## 📊 RESUMO VISUAL: TABELAS vs STATUS

### Legenda
- ✅ **Implementado** - Migration e Model existem
- ⚠️ **Parcial** - Model existe mas sem migration
- ❌ **Falta** - Nada implementado

### Por Módulo

#### Gestão de Membros (8/15 tabelas)
```
✅ users
✅ dados_pessoais  
✅ dados_configuracao
⚠️ pessoas
⚠️ membros (CRÍTICO)
⚠️ tipos_membro
⚠️ membros_tipos
⚠️ atletas
⚠️ relacoes_pessoas
❌ encarregados_educacao
❌ atletas_encarregados
❌ historico_estados
❌ documentos
❌ tipos_documento
```
**Progress:** 3/14 = 21%

#### Gestão Desportiva (0/14 tabelas)
```
⚠️ escaloes
⚠️ atletas_escaloes
⚠️ dados_desportivos
❌ modalidades (CRÍTICO)
❌ equipas (CRÍTICO)
❌ atletas_equipas
❌ treinos (CRÍTICO)
❌ presencas_treino (CRÍTICO)
❌ tipos_presenca
❌ competicoes
❌ convocatorias
❌ resultados_competicao
❌ tipos_competicao
❌ estatisticas_atleta
```
**Progress:** 0/14 = 0%

#### Gestão de Eventos (0/4 tabelas)
```
❌ eventos
❌ tipos_evento
❌ inscricoes_evento
❌ estados_inscricao
```
**Progress:** 0/4 = 0%

#### Gestão Financeira (0/14 tabelas)
```
❌ faturas (CRÍTICO)
❌ itens_fatura
❌ estados_fatura
❌ pagamentos (CRÍTICO)
❌ metodos_pagamento
❌ movimentos_financeiros (CRÍTICO)
❌ tipos_movimento
❌ categorias_movimento
❌ centros_custo
❌ contas_bancarias
❌ movimentos_bancarios
❌ reconciliacoes
```
**Progress:** 0/14 = 0%

### Total Geral: 3/46 tabelas = **6.5%**

---

## 🎯 DIFERENÇAS CRÍTICAS IDENTIFICADAS

### 1. **Nomenclatura Inconsistente**
- Arquitetura parece usar inglês em alguns lugares
- Models atuais usam português
- Precisa padronização

### 2. **Modelos Órfãos**
Existem 10 models sem migrations:
- Pessoa
- Membro ⚠️ **CRÍTICO**
- Atleta
- AtletaEscalao
- Consentimento
- DadosDesportivos
- Escalao
- MembroTipo
- RelacaoPessoa
- TipoMembro
- Utilizador

### 3. **Tabelas Essenciais em Falta**
Sem estas, o sistema não funciona:
- `membros` - Core do sistema
- `modalidades` - Sem modalidades, não há desporto
- `equipas` - Sem equipas, não há organização
- `treinos` - Sem treinos, não há registo de atividade
- `presencas_treino` - Sem isto, não há controle
- `faturas` - Sem isto, não há financeiro
- `pagamentos` - Sem isto, não há receitas

### 4. **Relações Indefinidas**
As foreign keys entre tabelas não estão estabelecidas, impossibilitando:
- Integrity constraints
- Cascade deletes
- Joins eficientes
- Consultas relacionais

### 5. **Falta de Estrutura de Auditoria**
Não existem tabelas para:
- Histórico de estados
- Logs de alterações
- Auditoria de acesso
- Versionamento de dados

---

## 📋 AÇÕES NECESSÁRIAS PARA ALINHAR COM ARQUITETURA

### Fase 1: Correção de Fundação (URGENTE)
1. **Decidir estratégia de nomenclatura** (português vs inglês)
2. **Criar migrations para models existentes**
3. **Estabelecer foreign keys e índices**
4. **Validar relacionamentos Eloquent**

### Fase 2: Completar Módulo Membros (ALTA PRIORIDADE)
5. **Criar tabelas em falta:**
   - encarregados_educacao
   - atletas_encarregados
   - documentos
   - tipos_documento
   - historico_estados

### Fase 3: Módulo Desportivo (ALTA PRIORIDADE)
6. **Criar estrutura completa:**
   - modalidades
   - equipas
   - atletas_equipas
   - treinos
   - presencas_treino
   - competicoes
   - convocatorias

### Fase 4: Módulo Financeiro (MÉDIA PRIORIDADE)
7. **Criar estrutura completa:**
   - faturas + itens_fatura
   - pagamentos
   - movimentos_financeiros
   - centros_custo

### Fase 5: Módulo Eventos (MÉDIA PRIORIDADE)
8. **Criar estrutura completa:**
   - eventos
   - tipos_evento
   - inscricoes_evento

---

## 🚦 SEMÁFORO DE ESTADO

### 🔴 VERMELHO - Sistema NÃO funcional
- **Módulo Membros:** Parcialmente implementado (30%)
- **Módulo Desportivo:** Não funcional (0%)
- **Módulo Financeiro:** Não funcional (0%)
- **Módulo Eventos:** Não funcional (0%)

### 🟡 AMARELO - Em desenvolvimento
- **Autenticação:** Funcional (100%)
- **Infraestrutura:** Funcional (90%)
- **Frontend Base:** Funcional (80%)

### 🟢 VERDE - Completo e funcional
- **Laravel Setup:** ✅
- **React Setup:** ✅
- **Layouts Base:** ✅

---

## 📈 ROADMAP PARA ALINHAMENTO

### Semana 1-2: Foundation Fix
- ✅ Criar migrations para models existentes
- ✅ Estabelecer todas as foreign keys
- ✅ Criar tabelas críticas em falta (membros core)

### Semana 3-4: Módulo Membros Completo
- ✅ Completar todas as tabelas
- ✅ Implementar controllers
- ✅ Frontend funcional

### Semana 5-6: Módulo Desportivo
- ✅ Todas as tabelas
- ✅ Controllers e API
- ✅ Frontend básico

### Semana 7-8: Módulo Financeiro
- ✅ Estrutura completa
- ✅ Automações
- ✅ Relatórios

### Semana 9: Módulo Eventos + Polish
- ✅ Estrutura de eventos
- ✅ Testes
- ✅ Otimizações

---

## 🎓 CONCLUSÃO

**Estado Atual:** O sistema tem apenas **6.5% das tabelas necessárias** implementadas.

**Bloqueador Principal:** Faltam migrations para os models existentes e tabelas core do sistema.

**Tempo Estimado para Alinhamento:** 8-10 semanas de desenvolvimento focado.

**Próxima Ação Crítica:** Criar migrations para as tabelas de membros (core do sistema).

---

*Este documento reflete o estado em 21/01/2026 e deve ser atualizado conforme o progresso.*
