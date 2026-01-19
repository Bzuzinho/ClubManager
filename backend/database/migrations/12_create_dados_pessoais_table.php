Schema::create('dados_pessoais', function (Blueprint $table) {
  $table->id();
  $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();

  $table->string('tipo_utilizador'); // o que define "membro" no teu modelo A
  $table->boolean('menor')->default(false);

  // relações entre users (EE <-> educando)
  $table->foreignId('encarregado_educacao_id')->nullable()->constrained('users')->nullOnDelete();
  $table->foreignId('educando_id')->nullable()->constrained('users')->nullOnDelete();

  // campos pessoais (vais expandir depois)
  $table->string('telemovel')->nullable();
  $table->date('data_nascimento')->nullable();
  $table->string('nif')->nullable();

  $table->timestamps();
});
