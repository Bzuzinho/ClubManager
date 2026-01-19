Schema::create('tipos_membro', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('descricao');
});
