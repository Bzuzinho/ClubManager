Schema::create('atletas', function (Blueprint $table) {
    $table->id();

    $table->foreignId('membro_id')
        ->constrained('membros')
        ->cascadeOnDelete();

    $table->boolean('ativo')->default(true);
    $table->date('data_inicio')->nullable();
    $table->date('data_fim')->nullable();

    $table->timestamps();
});
