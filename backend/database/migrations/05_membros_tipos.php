Schema::create('membro_tipos', function (Blueprint $table) {
    $table->id();

    $table->foreignId('membro_id')
        ->constrained('membros')
        ->cascadeOnDelete();

    $table->foreignId('tipo_membro_id')
        ->constrained('tipos_membro');

    $table->date('data_inicio');
    $table->date('data_fim')->nullable();

    $table->timestamps();
});
