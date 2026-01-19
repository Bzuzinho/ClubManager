Schema::create('dados_desportivos', function (Blueprint $table) {
    $table->id();

    $table->foreignId('atleta_id')
        ->constrained('atletas')
        ->cascadeOnDelete();

    $table->string('num_federacao')->nullable();
    $table->string('numero_pmb')->nullable();

    $table->date('data_inscricao')->nullable();
    $table->string('arquivo_inscricao')->nullable();

    $table->date('data_atestado_medico')->nullable();
    $table->string('arquivo_atestado_medico')->nullable();

    $table->text('informacoes_medicas')->nullable();

    $table->timestamps();
});
