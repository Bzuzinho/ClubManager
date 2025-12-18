#!/usr/bin/env bash
# Script para actualizar automaticamente a documentação do projeto.
# Este script deve ser colocado na raiz do repositório (por exemplo em `scripts/generate_docs.sh`).
# Ele copia ficheiros de documentação provenientes de uma pasta de módulos externa
# e pode ser estendido para gerar documentação a partir do código-fonte.

set -euo pipefail

# Detectar a raiz do repositório
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Directório onde a documentação será mantida
DOCS_DIR="$ROOT_DIR/docs"

# Criar pasta docs caso não exista
mkdir -p "$DOCS_DIR"

# Copiar ficheiros README existentes no projeto para docs/
echo "A recolher documentação do projeto..."
if [ -f "$ROOT_DIR/README.md" ]; then
    cp "$ROOT_DIR/README.md" "$DOCS_DIR/"
    echo "Copiado: README.md"
fi

if [ -f "$ROOT_DIR/backend/README.md" ]; then
    cp "$ROOT_DIR/backend/README.md" "$DOCS_DIR/backend-README.md"
    echo "Copiado: backend-README.md"
fi

if [ -f "$ROOT_DIR/frontend/README.md" ]; then
    cp "$ROOT_DIR/frontend/README.md" "$DOCS_DIR/frontend-README.md"
    echo "Copiado: frontend-README.md"
fi

# Procurar por outros ficheiros de documentação no projeto
find "$ROOT_DIR" -maxdepth 3 -type f \( -iname "*.md" -o -iname "*.pdf" -o -iname "*.docx" \) \
  ! -path "*/node_modules/*" ! -path "*/vendor/*" ! -path "*/docs/*" -print0 \
  | while IFS= read -r -d '' file; do
    filename="$(basename "$file")"
    if [ ! -f "$DOCS_DIR/$filename" ]; then
        cp "$file" "$DOCS_DIR/"
        echo "Copiado: $filename"
    fi
  done

# Gerar índice de documentação
echo "A gerar índice de documentação..."
cat > "$DOCS_DIR/INDEX.md" << 'EOF'
# Índice da Documentação - ClubManager

## Documentação Geral
- [README Principal](README.md)
- [Backend README](backend-README.md)
- [Frontend README](frontend-README.md)

## Estrutura do Projeto

### Backend (Laravel)
- Framework: Laravel
- Linguagem: PHP
- Base de Dados: Configurada em `config/database.php`
- API: Rotas em `routes/api.php`

### Frontend (React + TypeScript)
- Framework: React + TypeScript
- Build: Vite
- Configuração: `vite.config.ts`

## Documentação do Código

### Modelos (Backend)
EOF

# Listar modelos do backend
if [ -d "$ROOT_DIR/backend/app/Models" ]; then
    find "$ROOT_DIR/backend/app/Models" -name "*.php" -type f | while read -r model; do
        modelname=$(basename "$model" .php)
        echo "- $modelname" >> "$DOCS_DIR/INDEX.md"
    done
fi

cat >> "$DOCS_DIR/INDEX.md" << 'EOF'

### Controladores (Backend)
EOF

# Listar controladores do backend
if [ -d "$ROOT_DIR/backend/app/Http/Controllers" ]; then
    find "$ROOT_DIR/backend/app/Http/Controllers" -name "*.php" -type f | while read -r controller; do
        controllername=$(basename "$controller" .php)
        echo "- $controllername" >> "$DOCS_DIR/INDEX.md"
    done
fi

cat >> "$DOCS_DIR/INDEX.md" << 'EOF'

### Componentes (Frontend)
EOF

# Listar componentes principais do frontend
if [ -d "$ROOT_DIR/frontend/src" ]; then
    find "$ROOT_DIR/frontend/src" -name "*.tsx" -type f -not -path "*/node_modules/*" | while read -r component; do
        componentname=$(basename "$component")
        componentpath=$(echo "$component" | sed "s|$ROOT_DIR/frontend/src/||")
        echo "- $componentpath" >> "$DOCS_DIR/INDEX.md"
    done
fi

cat >> "$DOCS_DIR/INDEX.md" << 'EOF'

## Última Actualização
EOF

echo "Data: $(date '+%Y-%m-%d %H:%M:%S')" >> "$DOCS_DIR/INDEX.md"

echo "✅ Índice gerado em: $DOCS_DIR/INDEX.md"

# Mensagem final
echo "📚 Documentação actualizada em: $DOCS_DIR"
