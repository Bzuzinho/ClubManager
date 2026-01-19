Schema::create('membros', function (Blueprint $table) {
    $table->id();

    $table->foreignId('pessoa_id')
        ->constrained('pessoas')
        ->cascadeOnDelete();

    $table->string('numero_socio')->unique();
    $table->enum('estado', ['ativo', 'inativo', 'suspenso'])->default('ativo');

    $table->date('data_entrada')->nullable();
    $table->date('data_saida')->nullable();

    $table->timestamps();
});
