#!/bin/bash

# Script para corrigir todas as migrations para PostgreSQL
cd /workspaces/ClubManager/backend/database/migrations

echo "Corrigindo migrations para PostgreSQL..."

# 1. Adicionar withinTransaction = false em todas as migrations
for file in *.php; do
    if ! grep -q "withinTransaction" "$file"; then
        sed -i 's/return new class extends Migration$/return new class extends Migration\n{\n    public $withinTransaction = false;\n/' "$file"
        sed -i '/return new class extends Migration/{N;s/return new class extends Migration\n{/return new class extends Migration\n{\n    public $withinTransaction = false;/}' "$file"
    fi
done

echo "Migrations corrigidas!"
echo "Execute: php artisan migrate:fresh --force"
