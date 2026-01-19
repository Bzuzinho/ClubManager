Schema::create('relacoes_pessoas', function (Blueprint $table) {
    $table->id();

    $table->foreignId('pessoa_origem_id')
        ->constrained('pessoas')
        ->cascadeOnDelete();

    $table->foreignId('pessoa_destino_id')
        ->constrained('pessoas')
        ->cascadeOnDelete();

    $table->string('tipo_relacao'); // encarregado_educacao
    $table->date('data_inicio')->nullable();
    $table->date('data_fim')->nullable();

    $table->timestamps();

    $table->unique([
        'pessoa_origem_id',
        'pessoa_destino_id',
        'tipo_relacao'
    ]);
});
