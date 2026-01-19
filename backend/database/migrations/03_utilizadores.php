Schema::create('utilizadores', function (Blueprint $table) {
    $table->id();

    $table->foreignId('pessoa_id')
        ->constrained('pessoas')
        ->cascadeOnDelete();

    $table->string('email_utilizador')->unique();
    $table->string('password');

    $table->boolean('ativo_login')->default(true);
    $table->timestamp('last_login_at')->nullable();

    $table->timestamps();
});
