Schema::create('atleta_escalao', function (Blueprint $table) {
    $table->id();

    $table->foreignId('atleta_id')
        ->constrained('atletas')
        ->cascadeOnDelete();

    $table->foreignId('escalao_id')
        ->constrained('escaloes');

    $table->date('data_inicio');
    $table->date('data_fim')->nullable();
});
