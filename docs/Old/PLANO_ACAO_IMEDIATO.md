# Plano de Ação Imediato - ClubManager
**Data:** 21 de Janeiro de 2026  
**Objetivo:** Alinhar a implementação com a arquitetura planejada

---

## 🎯 VISÃO GERAL

**Problema:** O sistema tem apenas 15% de implementação com falta crítica de estrutura de base de dados.

**Solução:** Implementação em 4 fases prioritárias, começando pelas migrations essenciais.

**Timeline:** 8 semanas para MVP funcional

---

## 📊 FASE 1: FOUNDATION FIX (Semana 1-2)
**Objetivo:** Corrigir a base e criar estrutura mínima funcional

### ✅ Sprint 1.1: Decisões Arquiteturais (Dia 1)

#### Decisão 1: Nomenclatura
**Opções:**
- **A)** Manter português (pessoa, membro, atleta) - Alinhado com models existentes
- **B)** Migrar para inglês (person, member, athlete) - Padrão Laravel

**Recomendação:** Manter **português** para consistência com código existente.

#### Decisão 2: Estrutura de Membros
**Opções:**
- **A)** User → DadosPessoais → Membro → Atleta (atual)
- **B)** User → Member direto (simplificado)

**Recomendação:** Manter **estrutura atual** (Opção A) pois já tem models definidos.

### ✅ Sprint 1.2: Migrations Críticas (Dia 2-5)

#### Prioridade MÁXIMA - Criar estas migrations:

```php
// 1. Pessoas (base de tudo)
2026_01_19_000200_create_pessoas_table.php

// 2. Membros (core do sistema)
2026_01_19_000300_create_membros_table.php
2026_01_19_000310_create_tipos_membro_table.php
2026_01_19_000320_create_membros_tipos_table.php

// 3. Atletas e Encarregados
2026_01_19_000400_create_atletas_table.php
2026_01_19_000410_create_encarregados_educacao_table.php
2026_01_19_000420_create_atletas_encarregados_table.php

// 4. Relações
2026_01_19_000500_create_relacoes_pessoas_table.php

// 5. Consentimentos
2026_01_19_000600_create_consentimentos_table.php

// 6. Documentos
2026_01_19_000700_create_documentos_table.php
2026_01_19_000710_create_tipos_documento_table.php
```

#### Estrutura das Migrations

**pessoas:**
```php
Schema::create('pessoas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
    $table->string('nome_completo');
    $table->string('nif', 9)->unique()->nullable();
    $table->string('email')->unique();
    $table->string('telemovel', 20)->nullable();
    $table->date('data_nascimento')->nullable();
    $table->string('morada')->nullable();
    $table->string('codigo_postal', 8)->nullable();
    $table->string('localidade')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

**membros:**
```php
Schema::create('membros', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pessoa_id')->unique()->constrained('pessoas')->cascadeOnDelete();
    $table->string('numero_socio')->unique();
    $table->enum('estado', ['ativo', 'inativo', 'suspenso'])->default('ativo');
    $table->date('data_inscricao')->default(now());
    $table->date('data_fim')->nullable();
    $table->text('observacoes')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('numero_socio');
    $table->index('estado');
});
```

**tipos_membro:**
```php
Schema::create('tipos_membro', function (Blueprint $table) {
    $table->id();
    $table->string('nome'); // Ex: Atleta, Sócio, Staff
    $table->text('descricao')->nullable();
    $table->decimal('mensalidade', 8, 2)->default(0);
    $table->integer('limite_modalidades')->default(1);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
});
```

**membros_tipos (pivot):**
```php
Schema::create('membros_tipos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
    $table->foreignId('tipo_membro_id')->constrained('tipos_membro')->cascadeOnDelete();
    $table->date('data_inicio')->default(now());
    $table->date('data_fim')->nullable();
    $table->timestamps();
    
    $table->unique(['membro_id', 'tipo_membro_id', 'data_inicio']);
});
```

**atletas:**
```php
Schema::create('atletas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('membro_id')->unique()->constrained('membros')->cascadeOnDelete();
    $table->boolean('ativo')->default(true);
    $table->string('numero_camisola')->nullable();
    $table->string('posicao_preferida')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

**encarregados_educacao:**
```php
Schema::create('encarregados_educacao', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pessoa_id')->unique()->constrained('pessoas')->cascadeOnDelete();
    $table->string('grau_parentesco'); // Pai, Mãe, Tutor, etc
    $table->string('telemovel_alternativo', 20)->nullable();
    $table->string('email_alternativo')->nullable();
    $table->timestamps();
});
```

**atletas_encarregados (pivot):**
```php
Schema::create('atletas_encarregados', function (Blueprint $table) {
    $table->id();
    $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
    $table->foreignId('encarregado_id')->constrained('encarregados_educacao')->cascadeOnDelete();
    $table->boolean('principal')->default(false);
    $table->timestamps();
    
    $table->unique(['atleta_id', 'encarregado_id']);
});
```

**documentos (polimórfica):**
```php
Schema::create('documentos', function (Blueprint $table) {
    $table->id();
    $table->morphs('documentavel'); // documentavel_type, documentavel_id
    $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
    $table->string('nome_ficheiro');
    $table->string('caminho');
    $table->string('mime_type')->nullable();
    $table->integer('tamanho')->nullable(); // bytes
    $table->date('data_validade')->nullable();
    $table->date('data_upload')->default(now());
    $table->foreignId('uploaded_by')->constrained('users');
    $table->timestamps();
    $table->softDeletes();
});
```

### ✅ Sprint 1.3: Ajustar Models Existentes (Dia 6-7)

Para cada model, garantir:

```php
// Exemplo: Membro.php
class Membro extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'membros';
    
    protected $fillable = [
        'pessoa_id',
        'numero_socio',
        'estado',
        'data_inscricao',
        'data_fim',
        'observacoes'
    ];
    
    protected $casts = [
        'data_inscricao' => 'date',
        'data_fim' => 'date',
    ];
    
    // Relações
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
    
    public function tipos()
    {
        return $this->belongsToMany(TipoMembro::class, 'membros_tipos')
                    ->withPivot('data_inicio', 'data_fim')
                    ->withTimestamps();
    }
    
    public function atleta()
    {
        return $this->hasOne(Atleta::class);
    }
    
    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentavel');
    }
    
    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('estado', 'ativo');
    }
    
    public function scopeInativos($query)
    {
        return $query->where('estado', 'inativo');
    }
}
```

### ✅ Sprint 1.4: Seeders Base (Dia 8-10)

```php
// TipoMembroSeeder.php
class TipoMembroSeeder extends Seeder
{
    public function run()
    {
        TipoMembro::create([
            'nome' => 'Atleta',
            'descricao' => 'Atleta praticante',
            'mensalidade' => 30.00,
            'limite_modalidades' => 1,
            'ativo' => true,
        ]);
        
        TipoMembro::create([
            'nome' => 'Sócio',
            'descricao' => 'Sócio não praticante',
            'mensalidade' => 10.00,
            'limite_modalidades' => 0,
            'ativo' => true,
        ]);
        
        TipoMembro::create([
            'nome' => 'Staff',
            'descricao' => 'Treinador ou dirigente',
            'mensalidade' => 0.00,
            'limite_modalidades' => 0,
            'ativo' => true,
        ]);
    }
}

// TipoDocumentoSeeder.php
class TipoDocumentoSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            'CC', 'Cartão Cidadão',
            'ATESTADO', 'Atestado Médico',
            'AUTORIZACAO', 'Autorização Parental',
            'FOTO', 'Fotografia',
            'COMPROVATIVO', 'Comprovativo Morada',
            'AFILIACAO', 'Certificado Afiliação',
            'SEGURO', 'Seguro Desportivo',
            'OUTRO', 'Outro Documento'
        ];
        
        foreach (array_chunk($tipos, 2) as $tipo) {
            TipoDocumento::create([
                'codigo' => $tipo[0],
                'nome' => $tipo[1],
                'ativo' => true
            ]);
        }
    }
}
```

### ✅ Sprint 1.5: Testar Migrations (Dia 10)

```bash
# Limpar e recriar BD
php artisan migrate:fresh --seed

# Verificar estrutura
php artisan tinker
>>> Schema::hasTable('membros')
>>> Schema::hasTable('atletas')
>>> Schema::hasTable('documentos')

# Testar relações
>>> $pessoa = Pessoa::first()
>>> $pessoa->membro
>>> $membro = Membro::first()
>>> $membro->tipos
```

---

## 📊 FASE 2: MÓDULO DESPORTIVO (Semana 3-4)

### ✅ Sprint 2.1: Migrations Desportivas (Dia 11-15)

```php
// Modalidades
2026_01_20_000100_create_modalidades_table.php

// Escalões
2026_01_20_000200_create_escaloes_table.php

// Equipas
2026_01_20_000300_create_equipas_table.php
2026_01_20_000310_create_atletas_equipas_table.php

// Treinos
2026_01_20_000400_create_treinos_table.php
2026_01_20_000410_create_tipos_presenca_table.php
2026_01_20_000420_create_presencas_treino_table.php

// Competições
2026_01_20_000500_create_tipos_competicao_table.php
2026_01_20_000510_create_competicoes_table.php
2026_01_20_000520_create_convocatorias_table.php
2026_01_20_000530_create_resultados_competicao_table.php

// Dados Desportivos
2026_01_20_000600_create_dados_desportivos_table.php
2026_01_20_000610_create_estatisticas_atleta_table.php
```

**Estruturas-chave:**

```php
// modalidades
Schema::create('modalidades', function (Blueprint $table) {
    $table->id();
    $table->string('nome')->unique();
    $table->text('descricao')->nullable();
    $table->boolean('ativa')->default(true);
    $table->string('icone')->nullable(); // para UI
    $table->string('cor', 7)->nullable(); // hex color
    $table->timestamps();
});

// equipas
Schema::create('equipas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('modalidade_id')->constrained('modalidades')->cascadeOnDelete();
    $table->foreignId('escalao_id')->nullable()->constrained('escaloes')->nullOnDelete();
    $table->string('nome');
    $table->string('genero')->nullable(); // M, F, Misto
    $table->foreignId('treinador_id')->nullable()->constrained('users')->nullOnDelete();
    $table->boolean('ativa')->default(true);
    $table->timestamps();
    
    $table->unique(['modalidade_id', 'nome']);
});

// treinos
Schema::create('treinos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('equipa_id')->constrained('equipas')->cascadeOnDelete();
    $table->dateTime('data_hora');
    $table->integer('duracao_minutos')->default(90);
    $table->string('local')->nullable();
    $table->text('observacoes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    
    $table->index(['equipa_id', 'data_hora']);
});

// presencas_treino
Schema::create('presencas_treino', function (Blueprint $table) {
    $table->id();
    $table->foreignId('treino_id')->constrained('treinos')->cascadeOnDelete();
    $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
    $table->foreignId('tipo_presenca_id')->constrained('tipos_presenca');
    $table->text('observacoes')->nullable();
    $table->foreignId('marked_by')->nullable()->constrained('users');
    $table->timestamps();
    
    $table->unique(['treino_id', 'atleta_id']);
});
```

### ✅ Sprint 2.2: Models e Controllers (Dia 16-20)

Criar:
- Models: Modalidade, Equipa, Escalao, Treino, PresencaTreino, etc.
- Controllers: ModalidadeController, EquipaController, TreinoController, PresencaController
- Form Requests: StoreModalidadeRequest, UpdateEquipaRequest, etc.

---

## 📊 FASE 3: MÓDULO FINANCEIRO (Semana 5-6)

### ✅ Sprint 3.1: Migrations Financeiras (Dia 21-25)

```php
// Faturas
2026_01_21_000100_create_estados_fatura_table.php
2026_01_21_000110_create_faturas_table.php
2026_01_21_000120_create_itens_fatura_table.php

// Pagamentos
2026_01_21_000200_create_metodos_pagamento_table.php
2026_01_21_000210_create_pagamentos_table.php

// Movimentos
2026_01_21_000300_create_tipos_movimento_table.php
2026_01_21_000310_create_categorias_movimento_table.php
2026_01_21_000320_create_centros_custo_table.php
2026_01_21_000330_create_movimentos_financeiros_table.php

// Contas
2026_01_21_000400_create_contas_bancarias_table.php
```

**Estruturas-chave:**

```php
// faturas
Schema::create('faturas', function (Blueprint $table) {
    $table->id();
    $table->string('numero_fatura')->unique();
    $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
    $table->foreignId('estado_fatura_id')->constrained('estados_fatura');
    $table->date('data_emissao');
    $table->date('data_vencimento');
    $table->decimal('valor_total', 10, 2);
    $table->decimal('valor_pago', 10, 2)->default(0);
    $table->decimal('valor_pendente', 10, 2);
    $table->text('observacoes')->nullable();
    $table->foreignId('emitida_por')->constrained('users');
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['membro_id', 'estado_fatura_id']);
    $table->index('data_vencimento');
});

// pagamentos
Schema::create('pagamentos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fatura_id')->constrained('faturas')->cascadeOnDelete();
    $table->foreignId('metodo_pagamento_id')->constrained('metodos_pagamento');
    $table->decimal('valor', 10, 2);
    $table->date('data_pagamento');
    $table->string('referencia')->nullable();
    $table->text('observacoes')->nullable();
    $table->foreignId('registado_por')->constrained('users');
    $table->timestamps();
});

// movimentos_financeiros
Schema::create('movimentos_financeiros', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tipo_movimento_id')->constrained('tipos_movimento');
    $table->foreignId('categoria_movimento_id')->constrained('categorias_movimento');
    $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->nullOnDelete();
    $table->decimal('valor', 10, 2);
    $table->date('data_movimento');
    $table->text('descricao');
    $table->string('documento_referencia')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    
    $table->index(['data_movimento', 'tipo_movimento_id']);
});
```

### ✅ Sprint 3.2: Automações (Dia 26-30)

```php
// Jobs Laravel
- GenerateMonthlyInvoicesJob
- MarkOverdueInvoicesJob
- SendInvoiceReminderJob

// Commands
php artisan financeiro:gerar-quotas --mes=2026-01
php artisan financeiro:marcar-vencidas
```

---

## 📊 FASE 4: MÓDULO EVENTOS + FRONTEND (Semana 7-8)

### ✅ Sprint 4.1: Eventos (Dia 31-35)

```php
// Migrations
2026_01_22_000100_create_tipos_evento_table.php
2026_01_22_000110_create_eventos_table.php
2026_01_22_000120_create_estados_inscricao_table.php
2026_01_22_000130_create_inscricoes_evento_table.php
```

### ✅ Sprint 4.2: Frontend CRUD Completo (Dia 36-40)

Implementar:
1. **Módulo Membros:** Lista, criar, editar, upload documentos
2. **Módulo Desportivo:** Calendário treinos, marcar presenças
3. **Módulo Financeiro:** Emitir faturas, registar pagamentos
4. **Módulo Eventos:** Criar eventos, inscrições

---

## 📋 CHECKLIST DE PROGRESSO

### Semana 1-2
- [ ] Decisões arquiteturais tomadas
- [ ] 11 migrations de membros criadas
- [ ] Models ajustados com relações
- [ ] Seeders implementados
- [ ] Testes de relações passando

### Semana 3-4
- [ ] 11 migrations desportivas criadas
- [ ] Models desportivos implementados
- [ ] Controllers básicos criados
- [ ] API routes definidas

### Semana 5-6
- [ ] 8 migrations financeiras criadas
- [ ] Jobs de automação implementados
- [ ] Sistema de faturas funcional
- [ ] Registos de pagamento funcionais

### Semana 7-8
- [ ] 4 migrations de eventos criadas
- [ ] Frontend - módulo membros funcional
- [ ] Frontend - calendário treinos funcional
- [ ] Frontend - emissão faturas funcional

---

## 🎯 KPIs DE SUCESSO

### Ao final das 8 semanas:

**Backend:**
- ✅ 50+ tabelas criadas e relacionadas
- ✅ 15+ controllers CRUD funcionais
- ✅ Autenticação e autorização implementada
- ✅ 3+ jobs automatizados rodando

**Frontend:**
- ✅ 4 módulos com CRUD completo
- ✅ Upload de ficheiros funcional
- ✅ Calendários e dashboards funcionais
- ✅ Relatórios básicos implementados

**Sistema:**
- ✅ Fluxo completo: Criar membro → Inscrever em modalidade → Marcar presença → Emitir fatura → Registar pagamento
- ✅ Testes automatizados cobrindo 50%+ do código
- ✅ Documentação atualizada

---

## 🚀 COMEÇAR AGORA

### Próxima Ação Imediata:

```bash
# 1. Criar branch
git checkout -b feature/migrations-membros

# 2. Criar primeira migration
php artisan make:migration create_pessoas_table

# 3. Seguir estrutura definida acima

# 4. Testar
php artisan migrate
php artisan tinker
```

**Começar por:** Migration `create_pessoas_table.php`

---

*Este plano deve ser seguido rigorosamente para garantir progresso consistente.*
