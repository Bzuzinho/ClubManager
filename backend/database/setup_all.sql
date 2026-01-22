-- Script para criar todas as tabelas do ClubManager
-- Executar tudo de uma vez sem transações

-- Jobs e sistema
CREATE TABLE IF NOT EXISTS jobs (
    id bigserial PRIMARY KEY,
    queue varchar(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer NULL,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);
CREATE INDEX IF NOT EXISTS jobs_queue_index ON jobs(queue);

CREATE TABLE IF NOT EXISTS job_batches (
    id varchar(255) PRIMARY KEY,
    name varchar(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text NULL,
    cancelled_at integer NULL,
    created_at integer NOT NULL,
    finished_at integer NULL
);

CREATE TABLE IF NOT EXISTS failed_jobs (
    id bigserial PRIMARY KEY,
    uuid varchar(255) UNIQUE NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Personal Access Tokens
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id bigserial PRIMARY KEY,
    tokenable_type varchar(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name varchar(255) NOT NULL,
    token varchar(64) UNIQUE NOT NULL,
    abilities text NULL,
    last_used_at timestamp(0) NULL,
    expires_at timestamp(0) NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS personal_access_tokens_tokenable_type_tokenable_id_index 
    ON personal_access_tokens(tokenable_type, tokenable_id);

-- Dados Pessoais (antigas tabelas)
CREATE TABLE IF NOT EXISTS dados_pessoais (
    id bigserial PRIMARY KEY,
    user_id bigint NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    numero_socio varchar(255) UNIQUE NOT NULL,
    estado varchar(255) CHECK (estado IN ('ativo', 'inativo', 'suspenso')) DEFAULT 'ativo' NOT NULL,
    tipo_utilizador varchar(255) NOT NULL,
    menor boolean DEFAULT false NOT NULL,
    encarregado_educacao_id bigint NULL REFERENCES users(id) ON DELETE SET NULL,
    educando_id bigint NULL REFERENCES users(id) ON DELETE SET NULL,
    telemovel varchar(255) NULL,
    data_nascimento date NULL,
    nif varchar(20) NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(user_id)
);
CREATE INDEX IF NOT EXISTS dados_pessoais_estado_index ON dados_pessoais(estado);
CREATE INDEX IF NOT EXISTS dados_pessoais_menor_index ON dados_pessoais(menor);
CREATE INDEX IF NOT EXISTS dados_pessoais_tipo_utilizador_index ON dados_pessoais(tipo_utilizador);

CREATE TABLE IF NOT EXISTS dados_configuracao (
    id bigserial PRIMARY KEY,
    user_id bigint UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    consentimento_rgpd boolean DEFAULT false NOT NULL,
    consentimento_rgpd_em timestamp(0) NULL,
    declaracao_transporte boolean DEFAULT false NOT NULL,
    declaracao_transporte_em timestamp(0) NULL,
    afiliado boolean DEFAULT false NOT NULL,
    numero_afiliacao varchar(255) NULL,
    afiliacao_validade date NULL,
    acessos_enviados_em timestamp(0) NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);

-- Novas tabelas do sistema
CREATE TABLE IF NOT EXISTS pessoas (
    id bigserial PRIMARY KEY,
    user_id bigint UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    nome_completo varchar(255) NOT NULL,
    nif varchar(9) UNIQUE NULL,
    email varchar(255) UNIQUE NOT NULL,
    telemovel varchar(20) NULL,
    telefone_fixo varchar(20) NULL,
    data_nascimento date NULL,
    nacionalidade varchar(255) DEFAULT 'Portuguesa' NULL,
    naturalidade varchar(255) NULL,
    numero_identificacao varchar(255) NULL,
    validade_identificacao date NULL,
    morada varchar(255) NULL,
    codigo_postal varchar(8) NULL,
    localidade varchar(255) NULL,
    distrito varchar(255) NULL,
    foto_perfil varchar(255) NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS pessoas_nome_completo_index ON pessoas(nome_completo);
CREATE INDEX IF NOT EXISTS pessoas_email_index ON pessoas(email);
CREATE INDEX IF NOT EXISTS pessoas_nif_index ON pessoas(nif);

CREATE TABLE IF NOT EXISTS tipos_membro (
    id bigserial PRIMARY KEY,
    nome varchar(255) UNIQUE NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    descricao text NULL,
    mensalidade decimal(8,2) DEFAULT 0 NOT NULL,
    limite_modalidades integer DEFAULT 1 NOT NULL,
    requer_encarregado boolean DEFAULT false NOT NULL,
    pode_competir boolean DEFAULT false NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    ordem integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS tipos_membro_codigo_index ON tipos_membro(codigo);
CREATE INDEX IF NOT EXISTS tipos_membro_ativo_index ON tipos_membro(ativo);

CREATE TABLE IF NOT EXISTS membros (
    id bigserial PRIMARY KEY,
    pessoa_id bigint UNIQUE NOT NULL REFERENCES pessoas(id) ON DELETE CASCADE,
    numero_socio varchar(255) UNIQUE NOT NULL,
    estado varchar(255) CHECK (estado IN ('ativo', 'inativo', 'suspenso', 'pendente')) DEFAULT 'pendente' NOT NULL,
    data_inscricao date NOT NULL,
    data_inicio date NULL,
    data_fim date NULL,
    motivo_inativacao varchar(255) NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS membros_numero_socio_index ON membros(numero_socio);
CREATE INDEX IF NOT EXISTS membros_estado_index ON membros(estado);
CREATE INDEX IF NOT EXISTS membros_data_inscricao_index ON membros(data_inscricao);

CREATE TABLE IF NOT EXISTS membros_tipos (
    id bigserial PRIMARY KEY,
    membro_id bigint NOT NULL REFERENCES membros(id) ON DELETE CASCADE,
    tipo_membro_id bigint NOT NULL REFERENCES tipos_membro(id) ON DELETE CASCADE,
    data_inicio date NOT NULL,
    data_fim date NULL,
    ativo boolean DEFAULT true NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS membros_tipos_membro_id_tipo_membro_id_ativo_index 
    ON membros_tipos(membro_id, tipo_membro_id, ativo);

CREATE TABLE IF NOT EXISTS atletas (
    id bigserial PRIMARY KEY,
    membro_id bigint UNIQUE NOT NULL REFERENCES membros(id) ON DELETE CASCADE,
    ativo boolean DEFAULT true NOT NULL,
    numero_camisola varchar(255) NULL,
    tamanho_equipamento varchar(255) NULL,
    altura decimal(5,2) NULL,
    peso decimal(5,2) NULL,
    pe_dominante varchar(255) CHECK (pe_dominante IN ('direito', 'esquerdo', 'ambidestro')) NULL,
    posicao_preferida varchar(255) NULL,
    observacoes_medicas text NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS atletas_ativo_index ON atletas(ativo);
CREATE INDEX IF NOT EXISTS atletas_numero_camisola_index ON atletas(numero_camisola);

CREATE TABLE IF NOT EXISTS encarregados_educacao (
    id bigserial PRIMARY KEY,
    pessoa_id bigint UNIQUE NOT NULL REFERENCES pessoas(id) ON DELETE CASCADE,
    telemovel_alternativo varchar(20) NULL,
    email_alternativo varchar(255) NULL,
    profissao varchar(255) NULL,
    local_trabalho varchar(255) NULL,
    telefone_trabalho varchar(20) NULL,
    contacto_emergencia boolean DEFAULT true NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);

CREATE TABLE IF NOT EXISTS atletas_encarregados (
    id bigserial PRIMARY KEY,
    atleta_id bigint NOT NULL REFERENCES atletas(id) ON DELETE CASCADE,
    encarregado_id bigint NOT NULL REFERENCES encarregados_educacao(id) ON DELETE CASCADE,
    grau_parentesco varchar(255) NOT NULL,
    principal boolean DEFAULT false NOT NULL,
    autorizado_levantar boolean DEFAULT true NOT NULL,
    receber_notificacoes boolean DEFAULT true NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(atleta_id, encarregado_id)
);
CREATE INDEX IF NOT EXISTS atletas_encarregados_principal_index ON atletas_encarregados(principal);

CREATE TABLE IF NOT EXISTS relacoes_pessoas (
    id bigserial PRIMARY KEY,
    pessoa_origem_id bigint NOT NULL REFERENCES pessoas(id) ON DELETE CASCADE,
    pessoa_destino_id bigint NOT NULL REFERENCES pessoas(id) ON DELETE CASCADE,
    tipo_relacao varchar(255) NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(pessoa_origem_id, pessoa_destino_id, tipo_relacao)
);
CREATE INDEX IF NOT EXISTS relacoes_pessoas_tipo_relacao_index ON relacoes_pessoas(tipo_relacao);

CREATE TABLE IF NOT EXISTS tipos_documento (
    id bigserial PRIMARY KEY,
    nome varchar(255) UNIQUE NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    descricao text NULL,
    obrigatorio boolean DEFAULT false NOT NULL,
    tem_validade boolean DEFAULT false NOT NULL,
    validade_meses integer NULL,
    aplicavel_a varchar(255) NULL,
    ativo boolean DEFAULT true NOT NULL,
    ordem integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS tipos_documento_codigo_index ON tipos_documento(codigo);
CREATE INDEX IF NOT EXISTS tipos_documento_obrigatorio_index ON tipos_documento(obrigatorio);

CREATE TABLE IF NOT EXISTS documentos (
    id bigserial PRIMARY KEY,
    documentavel_type varchar(255) NOT NULL,
    documentavel_id bigint NOT NULL,
    tipo_documento_id bigint NOT NULL REFERENCES tipos_documento(id),
    nome_original varchar(255) NOT NULL,
    nome_ficheiro varchar(255) NOT NULL,
    caminho varchar(255) NOT NULL,
    mime_type varchar(255) NULL,
    tamanho integer NULL,
    data_emissao date NULL,
    data_validade date NULL,
    data_upload date NOT NULL,
    estado varchar(255) CHECK (estado IN ('valido', 'expirado', 'pendente_validacao', 'rejeitado')) DEFAULT 'pendente_validacao' NOT NULL,
    observacoes text NULL,
    uploaded_by bigint NOT NULL REFERENCES users(id),
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS documentos_documentavel_type_documentavel_id_index 
    ON documentos(documentavel_type, documentavel_id);
CREATE INDEX IF NOT EXISTS documentos_data_validade_index ON documentos(data_validade);
CREATE INDEX IF NOT EXISTS documentos_estado_index ON documentos(estado);

CREATE TABLE IF NOT EXISTS consentimentos (
    id bigserial PRIMARY KEY,
    pessoa_id bigint NOT NULL REFERENCES pessoas(id) ON DELETE CASCADE,
    tipo varchar(255) NOT NULL,
    consentido boolean DEFAULT false NOT NULL,
    data_consentimento date NULL,
    consentido_por bigint NULL REFERENCES users(id),
    observacoes text NULL,
    versao_termo varchar(255) NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(pessoa_id, tipo)
);
CREATE INDEX IF NOT EXISTS consentimentos_consentido_index ON consentimentos(consentido);
CREATE INDEX IF NOT EXISTS consentimentos_data_consentimento_index ON consentimentos(data_consentimento);

CREATE TABLE IF NOT EXISTS historico_estados (
    id bigserial PRIMARY KEY,
    entidade_type varchar(255) NOT NULL,
    entidade_id bigint NOT NULL,
    estado_anterior varchar(255) NULL,
    estado_novo varchar(255) NOT NULL,
    motivo varchar(255) NULL,
    observacoes text NULL,
    alterado_por bigint NOT NULL REFERENCES users(id),
    data_alteracao timestamp(0) NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS historico_estados_entidade_type_entidade_id_index 
    ON historico_estados(entidade_type, entidade_id);
CREATE INDEX IF NOT EXISTS historico_estados_data_alteracao_index ON historico_estados(data_alteracao);

-- Módulo Desportivo
CREATE TABLE IF NOT EXISTS modalidades (
    id bigserial PRIMARY KEY,
    nome varchar(255) UNIQUE NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    descricao text NULL,
    icone varchar(255) NULL,
    cor varchar(255) NULL,
    ativa boolean DEFAULT true NOT NULL,
    ordem integer DEFAULT 0 NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS modalidades_codigo_index ON modalidades(codigo);
CREATE INDEX IF NOT EXISTS modalidades_ativa_index ON modalidades(ativa);

CREATE TABLE IF NOT EXISTS escaloes (
    id bigserial PRIMARY KEY,
    nome varchar(255) NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    idade_minima integer NOT NULL,
    idade_maxima integer NOT NULL,
    ano_nascimento_inicio integer NULL,
    ano_nascimento_fim integer NULL,
    genero varchar(255) CHECK (genero IN ('masculino', 'feminino', 'misto')) DEFAULT 'misto' NOT NULL,
    descricao text NULL,
    ativo boolean DEFAULT true NOT NULL,
    ordem integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS escaloes_codigo_index ON escaloes(codigo);
CREATE INDEX IF NOT EXISTS escaloes_idade_minima_idade_maxima_index ON escaloes(idade_minima, idade_maxima);
CREATE INDEX IF NOT EXISTS escaloes_ativo_index ON escaloes(ativo);

CREATE TABLE IF NOT EXISTS equipas (
    id bigserial PRIMARY KEY,
    modalidade_id bigint NOT NULL REFERENCES modalidades(id) ON DELETE CASCADE,
    escalao_id bigint NULL REFERENCES escaloes(id) ON DELETE SET NULL,
    nome varchar(255) NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    genero varchar(255) CHECK (genero IN ('masculino', 'feminino', 'misto')) DEFAULT 'misto' NOT NULL,
    temporada varchar(255) NOT NULL,
    treinador_principal_id bigint NULL REFERENCES membros(id) ON DELETE SET NULL,
    local_treino varchar(255) NULL,
    horario_treino text NULL,
    estado varchar(255) CHECK (estado IN ('ativa', 'inativa', 'suspensa')) DEFAULT 'ativa' NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS equipas_modalidade_id_escalao_id_index ON equipas(modalidade_id, escalao_id);
CREATE INDEX IF NOT EXISTS equipas_temporada_index ON equipas(temporada);
CREATE INDEX IF NOT EXISTS equipas_estado_index ON equipas(estado);

CREATE TABLE IF NOT EXISTS atletas_equipas (
    id bigserial PRIMARY KEY,
    atleta_id bigint NOT NULL REFERENCES atletas(id) ON DELETE CASCADE,
    equipa_id bigint NOT NULL REFERENCES equipas(id) ON DELETE CASCADE,
    data_inicio date NOT NULL,
    data_fim date NULL,
    numero_camisola varchar(255) NULL,
    posicao varchar(255) NULL,
    titular boolean DEFAULT false NOT NULL,
    capitao boolean DEFAULT false NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS atletas_equipas_atleta_id_equipa_id_ativo_index 
    ON atletas_equipas(atleta_id, equipa_id, ativo);
CREATE INDEX IF NOT EXISTS atletas_equipas_data_inicio_index ON atletas_equipas(data_inicio);

CREATE TABLE IF NOT EXISTS treinos (
    id bigserial PRIMARY KEY,
    equipa_id bigint NOT NULL REFERENCES equipas(id) ON DELETE CASCADE,
    data date NOT NULL,
    hora_inicio time NOT NULL,
    hora_fim time NULL,
    local varchar(255) NOT NULL,
    tipo varchar(255) CHECK (tipo IN ('treino', 'jogo_treino', 'fisico', 'tatico', 'tecnico')) DEFAULT 'treino' NOT NULL,
    objetivos text NULL,
    descricao text NULL,
    observacoes text NULL,
    responsavel_id bigint NULL REFERENCES membros(id) ON DELETE SET NULL,
    estado varchar(255) CHECK (estado IN ('agendado', 'realizado', 'cancelado', 'adiado')) DEFAULT 'agendado' NOT NULL,
    motivo_cancelamento text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS treinos_equipa_id_data_index ON treinos(equipa_id, data);
CREATE INDEX IF NOT EXISTS treinos_estado_index ON treinos(estado);

CREATE TABLE IF NOT EXISTS presencas_treino (
    id bigserial PRIMARY KEY,
    treino_id bigint NOT NULL REFERENCES treinos(id) ON DELETE CASCADE,
    atleta_id bigint NOT NULL REFERENCES atletas(id) ON DELETE CASCADE,
    estado varchar(255) CHECK (estado IN ('presente', 'ausente', 'justificado', 'atrasado', 'saiu_mais_cedo')) DEFAULT 'ausente' NOT NULL,
    hora_chegada time NULL,
    hora_saida time NULL,
    justificacao text NULL,
    observacoes text NULL,
    registado_por bigint NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(treino_id, atleta_id)
);
CREATE INDEX IF NOT EXISTS presencas_treino_estado_index ON presencas_treino(estado);

CREATE TABLE IF NOT EXISTS competicoes (
    id bigserial PRIMARY KEY,
    modalidade_id bigint NOT NULL REFERENCES modalidades(id) ON DELETE CASCADE,
    equipa_casa_id bigint NULL REFERENCES equipas(id) ON DELETE SET NULL,
    adversario varchar(255) NULL,
    tipo varchar(255) CHECK (tipo IN ('jogo', 'torneio', 'amigavel', 'campeonato', 'taca')) DEFAULT 'jogo' NOT NULL,
    data date NOT NULL,
    hora time NULL,
    local varchar(255) NOT NULL,
    casa boolean DEFAULT true NOT NULL,
    competicao varchar(255) NULL,
    jornada varchar(255) NULL,
    estado varchar(255) CHECK (estado IN ('agendado', 'em_curso', 'finalizado', 'cancelado', 'adiado')) DEFAULT 'agendado' NOT NULL,
    golos_favor integer NULL,
    golos_contra integer NULL,
    resultado varchar(255) CHECK (resultado IN ('vitoria', 'empate', 'derrota')) NULL,
    relatorio text NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS competicoes_equipa_casa_id_data_index ON competicoes(equipa_casa_id, data);
CREATE INDEX IF NOT EXISTS competicoes_estado_index ON competicoes(estado);
CREATE INDEX IF NOT EXISTS competicoes_tipo_index ON competicoes(tipo);

CREATE TABLE IF NOT EXISTS convocatorias (
    id bigserial PRIMARY KEY,
    competicao_id bigint NOT NULL REFERENCES competicoes(id) ON DELETE CASCADE,
    atleta_id bigint NOT NULL REFERENCES atletas(id) ON DELETE CASCADE,
    estado varchar(255) CHECK (estado IN ('convocado', 'confirmado', 'ausente', 'justificado', 'lesionado')) DEFAULT 'convocado' NOT NULL,
    titular boolean DEFAULT false NOT NULL,
    hora_concentracao time NULL,
    local_concentracao varchar(255) NULL,
    observacoes text NULL,
    convocado_por bigint NULL REFERENCES users(id) ON DELETE SET NULL,
    data_convocatoria timestamp(0) NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(competicao_id, atleta_id)
);
CREATE INDEX IF NOT EXISTS convocatorias_estado_index ON convocatorias(estado);

CREATE TABLE IF NOT EXISTS dados_desportivos_atleta (
    id bigserial PRIMARY KEY,
    atleta_id bigint NOT NULL REFERENCES atletas(id) ON DELETE CASCADE,
    equipa_id bigint NULL REFERENCES equipas(id) ON DELETE SET NULL,
    temporada varchar(255) NOT NULL,
    jogos_realizados integer DEFAULT 0 NOT NULL,
    jogos_titular integer DEFAULT 0 NOT NULL,
    minutos_jogados integer DEFAULT 0 NOT NULL,
    golos integer DEFAULT 0 NOT NULL,
    assistencias integer DEFAULT 0 NOT NULL,
    cartoes_amarelos integer DEFAULT 0 NOT NULL,
    cartoes_vermelhos integer DEFAULT 0 NOT NULL,
    treinos_presentes integer DEFAULT 0 NOT NULL,
    treinos_totais integer DEFAULT 0 NOT NULL,
    percentagem_presenca decimal(5,2) NULL,
    media_golos decimal(5,2) NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(atleta_id, equipa_id, temporada)
);
CREATE INDEX IF NOT EXISTS dados_desportivos_atleta_temporada_index ON dados_desportivos_atleta(temporada);

-- Módulo Eventos
CREATE TABLE IF NOT EXISTS tipos_evento (
    id bigserial PRIMARY KEY,
    nome varchar(255) UNIQUE NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    descricao text NULL,
    cor varchar(255) NULL,
    icone varchar(255) NULL,
    requer_inscricao boolean DEFAULT false NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    ordem integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS tipos_evento_codigo_index ON tipos_evento(codigo);

CREATE TABLE IF NOT EXISTS eventos (
    id bigserial PRIMARY KEY,
    tipo_evento_id bigint NULL REFERENCES tipos_evento(id) ON DELETE SET NULL,
    titulo varchar(255) NOT NULL,
    descricao text NULL,
    data_inicio date NOT NULL,
    data_fim date NULL,
    hora_inicio time NULL,
    hora_fim time NULL,
    local varchar(255) NULL,
    morada_completa varchar(255) NULL,
    preco decimal(8,2) DEFAULT 0 NULL,
    vagas_totais integer NULL,
    vagas_disponiveis integer NULL,
    data_limite_inscricao date NULL,
    publico boolean DEFAULT true NOT NULL,
    requer_aprovacao boolean DEFAULT false NOT NULL,
    estado varchar(255) CHECK (estado IN ('rascunho', 'publicado', 'em_curso', 'finalizado', 'cancelado')) DEFAULT 'rascunho' NOT NULL,
    imagem varchar(255) NULL,
    observacoes text NULL,
    criado_por bigint NOT NULL REFERENCES users(id),
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS eventos_data_inicio_index ON eventos(data_inicio);
CREATE INDEX IF NOT EXISTS eventos_estado_index ON eventos(estado);
CREATE INDEX IF NOT EXISTS eventos_publico_index ON eventos(publico);

CREATE TABLE IF NOT EXISTS inscricoes_evento (
    id bigserial PRIMARY KEY,
    evento_id bigint NOT NULL REFERENCES eventos(id) ON DELETE CASCADE,
    membro_id bigint NOT NULL REFERENCES membros(id) ON DELETE CASCADE,
    estado varchar(255) CHECK (estado IN ('pendente', 'confirmada', 'cancelada', 'em_lista_espera')) DEFAULT 'pendente' NOT NULL,
    data_inscricao date NOT NULL,
    data_confirmacao date NULL,
    pago boolean DEFAULT false NOT NULL,
    valor_pago decimal(8,2) NULL,
    numero_acompanhantes integer DEFAULT 0 NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    UNIQUE(evento_id, membro_id)
);
CREATE INDEX IF NOT EXISTS inscricoes_evento_estado_index ON inscricoes_evento(estado);
CREATE INDEX IF NOT EXISTS inscricoes_evento_data_inscricao_index ON inscricoes_evento(data_inscricao);

-- Módulo Financeiro
CREATE TABLE IF NOT EXISTS centros_custo (
    id bigserial PRIMARY KEY,
    nome varchar(255) UNIQUE NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    descricao text NULL,
    responsavel_id bigint NULL REFERENCES membros(id) ON DELETE SET NULL,
    orcamento_anual decimal(10,2) NULL,
    ativo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS centros_custo_codigo_index ON centros_custo(codigo);

CREATE TABLE IF NOT EXISTS categorias_movimento (
    id bigserial PRIMARY KEY,
    nome varchar(255) UNIQUE NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    tipo varchar(255) CHECK (tipo IN ('receita', 'despesa')) NOT NULL,
    descricao text NULL,
    cor varchar(255) NULL,
    ativo boolean DEFAULT true NOT NULL,
    ordem integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS categorias_movimento_tipo_codigo_index ON categorias_movimento(tipo, codigo);

CREATE TABLE IF NOT EXISTS metodos_pagamento (
    id bigserial PRIMARY KEY,
    nome varchar(255) UNIQUE NOT NULL,
    codigo varchar(255) UNIQUE NOT NULL,
    descricao text NULL,
    requer_comprovativo boolean DEFAULT false NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    ordem integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS metodos_pagamento_codigo_index ON metodos_pagamento(codigo);

CREATE TABLE IF NOT EXISTS faturas (
    id bigserial PRIMARY KEY,
    membro_id bigint NOT NULL REFERENCES membros(id) ON DELETE CASCADE,
    numero_fatura varchar(255) UNIQUE NOT NULL,
    data_emissao date NOT NULL,
    data_vencimento date NOT NULL,
    valor_total decimal(10,2) NOT NULL,
    valor_pago decimal(10,2) DEFAULT 0 NOT NULL,
    valor_pendente decimal(10,2) NOT NULL,
    estado varchar(255) CHECK (estado IN ('pendente', 'paga', 'parcialmente_paga', 'vencida', 'cancelada')) DEFAULT 'pendente' NOT NULL,
    tipo varchar(255) CHECK (tipo IN ('mensalidade', 'inscricao', 'evento', 'multa', 'outro')) DEFAULT 'mensalidade' NOT NULL,
    referencia_mb varchar(255) NULL,
    observacoes text NULL,
    emitida_por bigint NOT NULL REFERENCES users(id),
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS faturas_numero_fatura_index ON faturas(numero_fatura);
CREATE INDEX IF NOT EXISTS faturas_membro_id_estado_index ON faturas(membro_id, estado);
CREATE INDEX IF NOT EXISTS faturas_data_vencimento_index ON faturas(data_vencimento);
CREATE INDEX IF NOT EXISTS faturas_estado_index ON faturas(estado);

CREATE TABLE IF NOT EXISTS itens_fatura (
    id bigserial PRIMARY KEY,
    fatura_id bigint NOT NULL REFERENCES faturas(id) ON DELETE CASCADE,
    descricao varchar(255) NOT NULL,
    quantidade integer DEFAULT 1 NOT NULL,
    preco_unitario decimal(10,2) NOT NULL,
    subtotal decimal(10,2) NOT NULL,
    desconto decimal(5,2) DEFAULT 0 NOT NULL,
    total decimal(10,2) NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS itens_fatura_fatura_id_index ON itens_fatura(fatura_id);

CREATE TABLE IF NOT EXISTS pagamentos (
    id bigserial PRIMARY KEY,
    fatura_id bigint NOT NULL REFERENCES faturas(id) ON DELETE CASCADE,
    metodo_pagamento_id bigint NOT NULL REFERENCES metodos_pagamento(id),
    numero_pagamento varchar(255) UNIQUE NOT NULL,
    data_pagamento date NOT NULL,
    valor decimal(10,2) NOT NULL,
    referencia varchar(255) NULL,
    comprovativo varchar(255) NULL,
    estado varchar(255) CHECK (estado IN ('pendente', 'confirmado', 'rejeitado')) DEFAULT 'pendente' NOT NULL,
    observacoes text NULL,
    registado_por bigint NOT NULL REFERENCES users(id),
    confirmado_por bigint NULL REFERENCES users(id) ON DELETE SET NULL,
    data_confirmacao timestamp(0) NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS pagamentos_numero_pagamento_index ON pagamentos(numero_pagamento);
CREATE INDEX IF NOT EXISTS pagamentos_fatura_id_estado_index ON pagamentos(fatura_id, estado);
CREATE INDEX IF NOT EXISTS pagamentos_data_pagamento_index ON pagamentos(data_pagamento);

CREATE TABLE IF NOT EXISTS movimentos_financeiros (
    id bigserial PRIMARY KEY,
    centro_custo_id bigint NULL REFERENCES centros_custo(id) ON DELETE SET NULL,
    categoria_movimento_id bigint NOT NULL REFERENCES categorias_movimento(id),
    tipo varchar(255) CHECK (tipo IN ('receita', 'despesa')) NOT NULL,
    numero_movimento varchar(255) UNIQUE NOT NULL,
    data_movimento date NOT NULL,
    valor decimal(10,2) NOT NULL,
    descricao varchar(255) NOT NULL,
    observacoes text NULL,
    pagamento_id bigint NULL REFERENCES pagamentos(id) ON DELETE SET NULL,
    documento_comprovativo varchar(255) NULL,
    registado_por bigint NOT NULL REFERENCES users(id),
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL,
    deleted_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS movimentos_financeiros_numero_movimento_index ON movimentos_financeiros(numero_movimento);
CREATE INDEX IF NOT EXISTS movimentos_financeiros_tipo_data_movimento_index ON movimentos_financeiros(tipo, data_movimento);
CREATE INDEX IF NOT EXISTS movimentos_financeiros_data_movimento_index ON movimentos_financeiros(data_movimento);

CREATE TABLE IF NOT EXISTS contas_bancarias (
    id bigserial PRIMARY KEY,
    nome varchar(255) NOT NULL,
    banco varchar(255) NOT NULL,
    iban varchar(25) UNIQUE NOT NULL,
    swift varchar(255) NULL,
    saldo_inicial decimal(10,2) DEFAULT 0 NOT NULL,
    saldo_atual decimal(10,2) DEFAULT 0 NOT NULL,
    ativa boolean DEFAULT true NOT NULL,
    principal boolean DEFAULT false NOT NULL,
    observacoes text NULL,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);
CREATE INDEX IF NOT EXISTS contas_bancarias_iban_index ON contas_bancarias(iban);
CREATE INDEX IF NOT EXISTS contas_bancarias_ativa_index ON contas_bancarias(ativa);
