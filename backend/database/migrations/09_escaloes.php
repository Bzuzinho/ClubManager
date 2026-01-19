Schema::create('escaloes', function (Blueprint $table) {
    $table->id();
    $table->string('nome');
    $table->integer('idade_min');
    $table->integer('idade_max');
});
