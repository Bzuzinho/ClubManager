# ClubManager — Especificação Técnica DEFINITIVA (Copilot build “de raiz”)

> **Este documento substitui todos os anteriores.**  
> Serve para o Copilot (ou equipa) **reescrever o projeto de raiz** com arquitetura final, regras fechadas e alterações aplicadas.  
> Inclui: **multi-clube (opcional mas suportado), normalização de relações, política de apagamento, permissões, índices, serviços, CRUDes, dashboards e queries**.

---

## 0) Decisões finais (para não haver mais “depende”)

### 0.1 Entidade base
- **`users` é a base única** de identidade (com ou sem login).
- **Membro é um perfil**: `membros.user_id` (1:1).

### 0.2 Multi-clube (tenancy)
- O sistema **suporta múltiplos clubes** desde o início.
- Quase todas as tabelas operacionais/config têm `club_id`.
- **Regras:**
  - Um `user` pode pertencer a vários clubes → ponte `club_users`.
  - Um `membro` é **por clube**: `membros (club_id, user_id)` unique.

> Se quiseres usar “single club” no BSCN, cria 1 clube e pronto. Mas a BD já fica escalável.

### 0.3 Estratégia de “apagamento” (best practice para escala)
- **Não se apaga** entidades críticas (pessoas/membros/faturas/pagamentos/resultados).  
- Usa `estado` + `ativo` + `data_fim` para fechar ciclos.
- `softDeletes()` só em entidades “administráveis” e de baixa criticidade (ex.: templates, campanhas, configs opcionais).

**Resultado:** não ficas preso por `unique` + soft delete e não perdes histórico.

### 0.4 Permissões: hierarquia clara (sem duplicação)
- **Fonte de verdade:** `roles`/`permissions` (Spatie) **para autorização**.
- `tipos_utilizador` é **classificação funcional** (atleta/encarregado/treinador…) e **filtros/UI**, NÃO é autorização.
- `permissoes_tipo_utilizador` **é removida** para evitar conflito.

> Se quiseres uma matriz “por módulo”, isso vive em `permissions` (ex.: `financeiro.ver`, `financeiro.editar`, etc.).

### 0.5 Normalização de targets (pessoa = user)
- Tudo o que aponta para “pessoa” aponta para **`user_id`**.
- `membro_id` só aparece onde é explicitamente “perfil de membro” (financeiro, grupos, atleta, presenças).
- `atleta_id` onde é “desportivo puro” (resultados).

### 0.6 Financeiro: estado derivado (consistência)
- `faturas.estado_pagamento` é **derivado** por regra:
  - `pago` se `sum(pagamentos) >= valor_total`
  - `parcial` se `0 < sum(pagamentos) < valor_total`
  - `pendente` se `sum(pagamentos) = 0` e não venceu
  - `atraso` se venceu e não pago
- Pode ser materializado num campo `status_cache` (opcional) atualizado por job/service.

### 0.7 Índices obrigatórios (para escalar)
- Index em todas as FKs.
- Index em colunas de filtro: datas, estados, `club_id`.
- Uniques definidos com `club_id` quando aplicável.

---

## 1) Domínios/Módulos (encadeamento e responsabilidades)

### 1.1 Módulos
1. **Core/Auth** (users, roles, sessions/tokens)
2. **Clube & Configuração** (clubes, escalões, provas, mensalidades, etc.)
3. **Pessoas/Membros** (ficha, RGPD, relações, tipos)
4. **Desportivo** (atletas, épocas, planeamento, resultados)
5. **Atividades & Eventos** (grupos, treinos, presenças, eventos)
6. **Financeiro** (faturas, itens, pagamentos, conta corrente, lançamentos)
7. **Inventário** (artigos, materiais, stock, empréstimos, manutenções)
8. **Comunicação** (templates, segmentos, campanhas, envios)
9. **Dashboards/Relatórios** (KPIs, exports)
10. **Documentos & Auditoria** (uploads, logs)

### 1.2 Dependências
- Primeiro: `clubs`, `club_users`, `escaloes`, `provas`, `mensalidades`, `centros_custo`
- Depois: `membros` e `atletas`
- Depois: `grupos/treinos/eventos`
- Depois: `financeiro` e `inventário`
- Depois: `comunicação` e `dashboards`

---

## 2) Modelo de dados DEFINITIVO (tabelas, campos, PK/FK, índices)

> Convenções:
> - Todas as tabelas “por clube” têm `club_id` e index.
> - FK sempre indexada.
> - `timestamps` em tudo.
> - Sem `softDeletes` em entidades críticas (pessoas/membros/faturas/pagamentos/resultados).

---

### 2.1 Core / Tenancy / Auth

#### clubs
- PK `id`
- `nome_fiscal`
- `abreviatura`
- `nif`
- `morada` (nullable)
- `contacto_telefonico` (nullable)
- `email` (nullable)
- `logo_ficheiro_id` (nullable -> ficheiros.id)
- `ativo` (bool default true)
- `created_at`, `updated_at`
**Índices:** `nif` unique (opcional), `ativo`

#### club_users (membership do user no clube)
- PK `id`
- FK `club_id -> clubs.id`
- FK `user_id -> users.id`
- `role_no_clube` (nullable) *(opcional, se quiseres role por clube; caso contrário usa Spatie global)*
- `ativo` (bool)
- `data_inicio` (nullable)
- `data_fim` (nullable)
- timestamps
**Uniques:** unique(`club_id`,`user_id`,`data_inicio`)
**Índices:** (`club_id`,`user_id`), `ativo`

#### users
- PK `id`
- `name`
- `email` (unique, nullable) *(se multi-clube e queres email repetido por clube, então muda para unique composto com club_users; por omissão mantém global)*
- `password` (nullable)
- `telefone` (nullable)
- `ativo` (bool default true)
- `last_login_at` (nullable)
- timestamps
**Índices:** `ativo`, `email` unique

#### roles / permissions / model_has_roles / role_has_permissions
- Spatie padrão
- **Permissões por módulo:** criar permissions com slugs do tipo:
  - `membros.ver`, `membros.editar`, `membros.eliminar`
  - `financeiro.ver`, `financeiro.editar`, etc.
  - `inventario.ver`…
  - `eventos.ver`…

#### personal_access_tokens (se API)
- Sanctum padrão

---

### 2.2 Configuração (Pressupostos por clube)

#### escaloes
- PK `id`
- FK `club_id -> clubs.id`
- `nome` (string)
- `idade_minima` (nullable)
- `idade_maxima` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`nome`)
**Índices:** (`club_id`), `ativo`

#### tipos_utilizador
- PK `id`
- FK `club_id -> clubs.id`
- `nome` (string)
- `descricao` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`nome`)
**Índices:** (`club_id`), `ativo`

#### provas
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `distancia_m`
- `modalidade`
- `individual` (bool)
- `ativo` (bool)
- timestamps
**Índices:** (`club_id`), `ativo`

#### mensalidades
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `regularidade_por_semana` (int)
- FK `escalao_id -> escaloes.id`
- `valor` decimal(10,2)
- `ativo` (bool)
- timestamps
**Índices:** (`club_id`), `escalao_id`, `ativo`

#### bancos
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `iban`
- `swift_bic` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`iban`)
**Índices:** (`club_id`), `ativo`

#### centros_custo
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `tipo`
- `descricao` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`nome`)
**Índices:** (`club_id`), `tipo`, `ativo`

#### patronos
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `nif` (nullable)
- `morada` (nullable)
- `contacto` (nullable)
- `email` (nullable)
- timestamps
**Índices:** (`club_id`), `nif`

#### fornecedores
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `morada` (nullable)
- `nif` (nullable)
- `contacto` (nullable)
- `email` (nullable)
- `ativo` (bool)
- timestamps
**Índices:** (`club_id`), `ativo`, `nif`

#### armazens
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `localizacao` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`nome`)
**Índices:** (`club_id`), `ativo`

#### categorias_artigos
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `descricao` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`nome`)

#### artigos
- PK `id`
- FK `club_id -> clubs.id`
- `codigo` (string)
- `nome`
- FK `fornecedor_id` (nullable) -> fornecedores.id
- FK `armazem_id` (nullable) -> armazens.id
- FK `categoria_id` (nullable) -> categorias_artigos.id
- `valor` decimal(10,2)
- `imposto_percent` decimal(5,2) nullable
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`codigo`)
**Índices:** (`club_id`), fornecedor_id, armazem_id, categoria_id, ativo

#### notificacoes_tipos
- PK `id`
- `slug` (unique global: genericas, pagamentos_novos, atividades)
- `nome`
- `descricao` (nullable)
- timestamps

#### notificacoes_config (por clube)
- PK `id`
- FK `club_id -> clubs.id`
- FK `tipo_id -> notificacoes_tipos.id`
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`tipo_id`)

#### notificacoes_emails_envio
- PK `id`
- FK `club_id -> clubs.id`
- FK `notificacao_config_id -> notificacoes_config.id`
- `email_envio`
- `nome_remetente` (nullable)
- `ativo` (bool)
- `prioridade` (int default 1)
- timestamps
**Uniques:** unique(`notificacao_config_id`,`email_envio`)

---

### 2.3 Pessoas / Membros (por clube)

#### dados_pessoais
- PK `id`
- FK `user_id -> users.id`
- `nome_completo`
- `data_nascimento` (nullable)
- `nif` (nullable)
- `cc` (nullable)
- `morada` (nullable)
- `codigo_postal` (nullable)
- `localidade` (nullable)
- `nacionalidade` (nullable)
- `sexo` (nullable)
- `contacto_telefonico` (nullable)
- `email_secundario` (nullable)
- timestamps
**Uniques:** unique(`user_id`)
**Índices:** `nif`, `data_nascimento`

#### membros
- PK `id`
- FK `club_id -> clubs.id`
- FK `user_id -> users.id`
- `numero_socio` (nullable)
- `estado` (ativo/inativo/suspenso)
- `data_adesao` (nullable)
- `data_fim` (nullable)
- `observacoes` (nullable)
- timestamps
**Uniques:** unique(`club_id`,`user_id`), unique(`club_id`,`numero_socio`) *(numero_socio nullable)*
**Índices:** (`club_id`), `estado`, `data_adesao`

#### dados_configuracao
- PK `id`
- FK `club_id -> clubs.id`
- FK `user_id -> users.id`
- `rgpd` bool
- `data_rgpd` (nullable)
- `consentimento` bool
- `data_consentimento` (nullable)
- `afiliacao` bool
- `data_afiliacao` (nullable)
- `declaracao_transporte` bool
- `email_utilizador` (nullable) *(para login/convites; não precisa ser unique global)*
- timestamps
**Uniques:** unique(`club_id`,`user_id`)
**Índices:** (`club_id`)

#### user_tipos_utilizador
- PK `id`
- FK `club_id -> clubs.id`
- FK `user_id -> users.id`
- FK `tipo_utilizador_id -> tipos_utilizador.id`
- `data_inicio` (nullable)
- `data_fim` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`user_id`,`tipo_utilizador_id`,`data_inicio`)
**Índices:** (`club_id`), user_id, tipo_utilizador_id, ativo

#### relacoes_users
- PK `id`
- FK `club_id -> clubs.id`
- FK `user_origem_id -> users.id`
- FK `user_destino_id -> users.id`
- `tipo_relacao` (encarregado/atleta/…)
- `data_inicio` (nullable)
- `data_fim` (nullable)
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`user_origem_id`,`user_destino_id`,`tipo_relacao`,`data_inicio`)
**Índices:** (`club_id`), user_origem_id, user_destino_id, tipo_relacao, ativo

---

### 2.4 Desportivo (por clube)

#### atletas
- PK `id`
- FK `club_id -> clubs.id`
- FK `membro_id -> membros.id`
- `ativo` (bool)
- timestamps
**Uniques:** unique(`club_id`,`membro_id`)
**Índices:** (`club_id`), membro_id, ativo

#### dados_desportivos
- PK `id`
- FK `club_id -> clubs.id`
- FK `atleta_id -> atletas.id`
- `num_federacao` (nullable)
- `numero_pmb` (nullable)
- `data_inscricao` (nullable)
- FK `escalao_atual_id -> escaloes.id` (nullable)
- `data_atestado_medico` (nullable)
- `informacoes_medicas` (nullable)
- timestamps
**Uniques:** unique(`club_id`,`atleta_id`)

#### atleta_escaloes
- PK `id`
- FK `club_id -> clubs.id`
- FK `atleta_id -> atletas.id`
- FK `escalao_id -> escaloes.id`
- `data_inicio`
- `data_fim` (nullable)
- timestamps
**Uniques:** unique(`club_id`,`atleta_id`,`escalao_id`,`data_inicio`)
**Índices:** (`club_id`), atleta_id, escalao_id

#### epocas
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `ano_temporada`
- `data_inicio`
- `data_fim`
- `estado`
- timestamps
**Uniques:** unique(`club_id`,`ano_temporada`)
**Índices:** (`club_id`), estado

#### macrociclos / mesociclos / microciclos
- todos com `club_id` + FK para o pai + índices em datas

---

### 2.5 Atividades / Treinos / Eventos (por clube)

#### grupos
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- FK `escalao_id -> escaloes.id` (nullable)
- FK `treinador_user_id -> users.id` (nullable)
- `horario` (nullable)
- `local` (nullable)
- `ativo` bool
- timestamps
**Uniques:** unique(`club_id`,`nome`)
**Índices:** (`club_id`), escalao_id, treinador_user_id, ativo

#### grupo_membros
- PK `id`
- FK `club_id -> clubs.id`
- FK `grupo_id -> grupos.id`
- FK `membro_id -> membros.id`
- `data_inicio` (nullable)
- `data_fim` (nullable)
- `ativo` bool
- timestamps
**Uniques:** unique(`club_id`,`grupo_id`,`membro_id`,`data_inicio`)
**Índices:** (`club_id`), grupo_id, membro_id, ativo

#### treinos
- PK `id`
- FK `club_id -> clubs.id`
- FK `grupo_id -> grupos.id`
- FK `microciclo_id -> microciclos.id` (nullable)
- `data_agendada` datetime
- `descricao` (nullable)
- `conteudo` (nullable, longtext)
- `estado` (planeado/realizado/fechado)
- timestamps
**Índices:** (`club_id`), grupo_id, data_agendada, estado

#### presencas
- PK `id`
- FK `club_id -> clubs.id`
- FK `treino_id -> treinos.id`
- FK `membro_id -> membros.id`
- `estado` (presente/falta/justificada)
- `observacoes` (nullable)
- timestamps
**Uniques:** unique(`club_id`,`treino_id`,`membro_id`)
**Índices:** (`club_id`), treino_id, membro_id, estado

#### eventos_tipos
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- timestamps
**Uniques:** unique(`club_id`,`nome`)

#### eventos
- PK `id`
- FK `club_id -> clubs.id`
- FK `tipo_id -> eventos_tipos.id`
- `titulo`
- `descricao` (nullable)
- `data_inicio` datetime
- `data_fim` (nullable)
- `local` (nullable)
- `transporte` (nullable)
- `logistica` (nullable)
- FK `patrono_id -> patronos.id` (nullable)
- FK `centro_custo_id -> centros_custo.id` (nullable)
- `estado` (rascunho/publicado/fechado)
- timestamps
**Índices:** (`club_id`), tipo_id, data_inicio, estado

#### eventos_participantes
- PK `id`
- FK `club_id -> clubs.id`
- FK `evento_id -> eventos.id`
- FK `user_id -> users.id`
- FK `membro_id -> membros.id` (nullable) *(opcional; se estiver preenchido, tem de ser do mesmo user no mesmo club)*
- `estado_confirmacao` (confirmado/pendente/recusado)
- `justificacao` (nullable)
- timestamps
**Uniques:** unique(`club_id`,`evento_id`,`user_id`)
**Índices:** (`club_id`), evento_id, user_id, estado_confirmacao

#### resultados
- PK `id`
- FK `club_id -> clubs.id`
- FK `evento_id -> eventos.id`
- FK `atleta_id -> atletas.id`
- FK `prova_id -> provas.id`
- FK `epoca_id -> epocas.id` (nullable)
- `piscina` (nullable)
- `tempo` (nullable)
- `classificacao` (nullable)
- `pontos` (nullable)
- `notas` (nullable)
- timestamps
**Índices:** (`club_id`), evento_id, atleta_id, prova_id, epoca_id

---

### 2.6 Financeiro (por clube)

#### dados_financeiros
- PK `id`
- FK `club_id -> clubs.id`
- FK `membro_id -> membros.id`
- FK `mensalidade_id -> mensalidades.id` (nullable)
- `dia_cobranca` (nullable)
- `observacoes` (nullable)
- timestamps
**Uniques:** unique(`club_id`,`membro_id`)
**Índices:** (`club_id`), membro_id

#### faturas
- PK `id`
- FK `club_id -> clubs.id`
- FK `membro_id -> membros.id`
- `data_emissao` date
- `mes` (YYYY-MM, nullable, index)
- `data_inicio_periodo` (nullable)
- `data_fim_periodo` (nullable)
- `valor_total` decimal(10,2)
- `status_cache` (nullable) *(opcional)*
- `numero_recibo` (nullable)
- `referencia_pagamento` (nullable)
- FK `centro_custo_id -> centros_custo.id` (nullable)
- timestamps
**Índices:** (`club_id`), membro_id, mes, data_emissao, centro_custo_id

#### catalogo_fatura_itens
- PK `id`
- FK `club_id -> clubs.id`
- `descricao`
- `valor_unitario`
- `imposto_percentual`
- `tipo`
- `ativo`
- timestamps
**Índices:** (`club_id`), ativo, tipo

#### fatura_itens
- PK `id`
- FK `club_id -> clubs.id`
- FK `fatura_id -> faturas.id`
- FK `catalogo_item_id -> catalogo_fatura_itens.id` (nullable)
- `descricao`
- `valor_unitario`
- `quantidade`
- `imposto_percentual`
- `total_linha`
- FK `centro_custo_id -> centros_custo.id` (nullable)
- timestamps
**Índices:** (`club_id`), fatura_id, catalogo_item_id, centro_custo_id

#### pagamentos
- PK `id`
- FK `club_id -> clubs.id`
- FK `fatura_id -> faturas.id`
- `data_pagamento` date
- `valor` decimal(10,2)
- `metodo`
- `referencia` (nullable)
- FK `banco_id -> bancos.id` (nullable)
- FK `ficheiro_comprovativo_id -> ficheiros.id` (nullable)
- timestamps
**Índices:** (`club_id`), fatura_id, data_pagamento, metodo

#### lancamentos_financeiros
- PK `id`
- FK `club_id -> clubs.id`
- `data` date
- `descricao`
- `tipo` (receita/despesa)
- `valor`
- FK `centro_custo_id -> centros_custo.id` (nullable)
- FK `fatura_id -> faturas.id` (nullable)
- FK `membro_id -> membros.id` (nullable)
- timestamps
**Índices:** (`club_id`), data, tipo, centro_custo_id, fatura_id

---

### 2.7 Inventário (por clube)

#### materiais
- PK `id`
- FK `club_id -> clubs.id`
- FK `artigo_id -> artigos.id` (nullable)
- `designacao` (nullable)
- `stock` int
- `preco` (nullable)
- `garantia_ate` (nullable)
- `notas` (nullable)
- timestamps
**Índices:** (`club_id`), artigo_id, stock

#### movimentos_stock
- PK `id`
- FK `club_id -> clubs.id`
- FK `material_id -> materiais.id`
- `tipo` (entrada/saida/ajuste)
- `quantidade`
- `data` datetime
- `referencia` (nullable)
- FK `membro_id -> membros.id` (nullable)
- FK `user_id -> users.id` (nullable)
- timestamps
**Índices:** (`club_id`), material_id, data, tipo

#### emprestimos
- PK `id`
- FK `club_id -> clubs.id`
- FK `material_id -> materiais.id`
- FK `membro_id -> membros.id`
- `quantidade`
- `data_saida` date
- `data_devolucao` (nullable)
- `estado` (ativo/devolvido/perdido)
- timestamps
**Índices:** (`club_id`), material_id, membro_id, estado

#### manutencoes
- PK `id`
- FK `club_id -> clubs.id`
- FK `material_id -> materiais.id`
- `data` date
- FK `fornecedor_id -> fornecedores.id` (nullable)
- `custo` (nullable)
- `notas` (nullable)
- timestamps
**Índices:** (`club_id`), material_id, data

---

### 2.8 Comunicação (por clube)

#### modelos_email
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `assunto`
- `corpo_template`
- `ativo`
- timestamps
**Uniques:** unique(`club_id`,`nome`)

#### segmentos
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `descricao` (nullable)
- `filtros` (JSON)
- timestamps
**Uniques:** unique(`club_id`,`nome`)

#### campanhas
- PK `id`
- FK `club_id -> clubs.id`
- `nome`
- `canal` (email/sms)
- FK `segmento_id -> segmentos.id` (nullable)
- `agendado_para` (nullable)
- `estado` (rascunho/agendada/enviada/cancelada)
- timestamps
**Índices:** (`club_id`), canal, estado, agendado_para

#### envios
- PK `id`
- FK `club_id -> clubs.id`
- FK `campanha_id -> campanhas.id`
- FK `user_id -> users.id`
- `canal`
- `assunto` (nullable)
- `conteudo_enviado` (nullable)
- `estado` (pendente/enviado/erro)
- `provider_message_id` (nullable)
- `enviado_em` (nullable)
- timestamps
**Índices:** (`club_id`), campanha_id, user_id, estado, enviado_em

---

### 2.9 Documentos (transversal)

#### ficheiros
- PK `id`
- `disk`
- `path`
- `original_name` (nullable)
- `mime` (nullable)
- `size_bytes` (nullable)
- `checksum` (nullable)
- FK `uploaded_by -> users.id` (nullable)
- timestamps
**Índices:** uploaded_by, checksum

#### entidade_ficheiros
- PK `id`
- `entidade_type` (morph)
- `entidade_id`
- FK `ficheiro_id -> ficheiros.id`
- `tipo`
- `data_documento` (nullable)
- timestamps
**Uniques:** unique(entidade_type, entidade_id, ficheiro_id, tipo)
**Índices:** entidade_type, entidade_id, ficheiro_id

---

### 2.10 Auditoria / Sistema

#### auditoria
- PK `id`
- FK `club_id -> clubs.id` (nullable) *(se ação associada a clube)*
- FK `user_id -> users.id` (nullable)
- `acao`
- `entidade_type` (morph)
- `entidade_id`
- `antes` JSON nullable
- `depois` JSON nullable
- `ip` nullable
- `user_agent` nullable
- timestamps
**Índices:** club_id, user_id, entidade_type, entidade_id, created_at

`jobs` / `failed_jobs`: padrão Laravel.

---

## 3) Services (lógica obrigatória, controllers leves)

### 3.1 Estrutura recomendada
- `App\Services\Tenancy\ClubContext`
- `App\Services\Membros\MembroService`
- `App\Services\Desportivo\AtletaService`
- `App\Services\Treinos\TreinoService`
- `App\Services\Treinos\PresencasService`
- `App\Services\Eventos\EventoService`
- `App\Services\Financeiro\FaturacaoService`
- `App\Services\Financeiro\ContaCorrenteService`
- `App\Services\Inventario\StockService`
- `App\Services\Comunicacao\CampanhaService`
- `App\Services\Documentos\FicheirosService`
- `App\Services\Auditoria\AuditoriaService`

### 3.2 Regras críticas (pseudo)
#### ClubContext (tenancy)
- Determinar `club_id` ativo do utilizador (session)
- Aplicar global scopes (opcional) ou filtros obrigatórios nos queries

#### Criar Membro (MembroService)
1) garantir `User`
2) garantir `club_users` ativo (associar user ao clube)
3) upsert `dados_pessoais`
4) create `membros` (club_id,user_id)
5) upsert `dados_configuracao`
6) attach `user_tipos_utilizador`
7) se tipo=atleta → criar `atletas` + `dados_desportivos` + `atleta_escaloes`
8) se mensalidade → criar/atualizar `dados_financeiros`

#### Gerar Faturas (FaturacaoService)
- Input: club_id, membro_id, mes_inicio
- regra: gerar de mes_inicio até Julho (parametrizar depois)
- criar `faturas` + `fatura_itens` via catálogo
- calcular total server-side
- opcional: criar lançamento “expectável”

#### Conta Corrente (ContaCorrenteService)
- saldo em aberto por fatura = total - pagamentos
- status derivado (pendente/parcial/pago/atraso)
- atraso: mes < mes_atual e saldo > 0

#### Inventário (StockService)
- empréstimo → validar stock, criar `emprestimos`, criar `movimentos_stock`, atualizar stock
- devolução → atualizar empréstimo, movimento entrada, atualizar stock
- impedir stock negativo

---

## 4) Autorização (Policies + Permissions Spatie)

### 4.1 Permissões padrão (gerar seed)
Para cada módulo: `ver`, `editar`, `eliminar`:
- `membros.ver|editar|eliminar`
- `desportivo.ver|editar|eliminar`
- `eventos.ver|editar|eliminar`
- `treinos.ver|editar|eliminar`
- `financeiro.ver|editar|eliminar`
- `inventario.ver|editar|eliminar`
- `comunicacao.ver|editar|eliminar`
- `configuracao.ver|editar|eliminar`
- `dashboard.ver`

### 4.2 Roles padrão (seed)
- `admin`
- `secretaria`
- `treinador`
- `financeiro`
- `inventario`
- `marketing`

### 4.3 Regra de tenancy
- Todas as actions validam `club_id` do contexto.
- Um user só acede a clubes onde tem `club_users.ativo = true`.

---

## 5) CRUDs (Controllers + Routes + Requests) — build “de raiz”

### 5.1 Rotas
- `/clubs` (admin) — gerir clubes e associação de utilizadores
- `/config/...` — tudo de configuração por clube
- `/membros` — lista + ficha com tabs
- `/desportivo/...`
- `/treinos/...`
- `/eventos/...`
- `/financeiro/...`
- `/inventario/...`
- `/comunicacao/...`
- `/dashboard`

### 5.2 Controllers mínimos
- Tenancy: `ClubSwitchController` (selecionar clube ativo)
- Config: controllers resource por entidade
- Membros: `MembroController` (ficha) + controllers auxiliares (AJAX para tabelas)
- Financeiro: `FaturaController` (+ endpoints gerar/criar/itens/pagamentos)
- Treinos: `TreinoController`, `PresencaController`
- Eventos: `EventoController`, `ResultadoController`, `EventoParticipanteController`
- Inventário: `MaterialController`, `EmprestimoController`, `ManutencaoController`
- Comunicação: `CampanhaController`, `EnvioController`
- Documentos: `FicheiroController`, `EntidadeFicheiroController`

### 5.3 Requests
- Criar Store/Update requests por entidade principal.
- Validar sempre `club_id` por contexto (não vindo do client).

---

## 6) UI e dashboards (dados gráficos alinhados)

### 6.1 Filtros globais (sempre)
- clube (obrigatório)
- época (se aplicável)
- escalão
- grupo
- mês
- estado financeiro
- tipo de utilizador

### 6.2 KPIs e queries fonte-de-verdade
**Assiduidade**
- `presencas` join `treinos` join `grupos`
- taxa = presenças presentes / total marcadas por período

**Financeiro**
- faturado = sum(faturas.valor_total) por mês
- recebido = sum(pagamentos.valor) por mês
- atraso = faturas com saldo>0 e mes < atual

**Inventário**
- stock baixo = materiais.stock <= threshold (config futuro)
- emprestados = emprestimos.estado=ativo

**Eventos**
- próximos = eventos.data_inicio entre hoje e +30d
- participação = count confirmados/pendentes

---

## 7) Seeds obrigatórios (para Copilot criar automaticamente)
1. `clubs` (criar 1 clube default “BSCN”)
2. `modulos` *(se quiseres tabela, opcional; com Spatie podes não precisar)*
3. `permissions` e `roles` + atribuições default
4. `escaloes`, `tipos_utilizador`, `provas`, `centros_custo`
5. `notificacoes_tipos` + `notificacoes_config` default por clube

---

## 8) Checklist “reescrever tudo de raiz” (ordem de execução)
1. migrations: core/tenancy/auth
2. migrations: config por clube
3. migrations: pessoas/membros
4. migrations: desportivo
5. migrations: treinos/eventos
6. migrations: financeiro
7. migrations: inventário
8. migrations: comunicação
9. migrations: ficheiros/auditoria
10. models + relations + scopes por club
11. seeders
12. policies + middleware
13. controllers + requests + services
14. UI (listagens, ficha membro com tabs, modais faturas)
15. dashboard
16. testes feature mínimos

---

## 9) Nota final (o que mudou vs versões anteriores)
- ✅ Adicionado **multi-clube** com `club_id` e `club_users`
- ✅ Removida matriz `permissoes_tipo_utilizador` (evita conflito) → Spatie como fonte de verdade
- ✅ Estratégia sem soft delete em entidades críticas (evita bloqueios e mantém histórico)
- ✅ Normalização de targets: pessoa = `user_id`
- ✅ Financeiro com estado derivado (consistente)
- ✅ Índices e uniques definidos para escalar

