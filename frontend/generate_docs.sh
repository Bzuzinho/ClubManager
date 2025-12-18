#!/usr/bin/env bash
# Script para actualizar automaticamente a documentação do projeto.
# Este script deve ser colocado na raiz do repositório (por exemplo em `scripts/generate_docs.sh`).
# Ele copia ficheiros de documentação provenientes de uma pasta de módulos externa
# e pode ser estendido para gerar documentação a partir do código-fonte.

set -euo pipefail

# Detectar a raiz do repositório (assumindo que este script está na pasta `scripts/`)
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Directório onde a documentação será mantida
DOCS_DIR="$ROOT_DIR/docs"

# Directório de origem com documentação extra (modulos descompactados)
# Ajuste este caminho conforme necessário.
MODULES_SRC="$ROOT_DIR/Modulos/Gestao-main"

# Criar pasta docs caso não exista
mkdir -p "$DOCS_DIR"

# Copiar todos os ficheiros Markdown, PDF e DOCX da pasta de módulos para `docs/`
# Esta operação sobrescreve versões antigas, garantindo que a documentação está actualizada.
find "$MODULES_SRC" -type f \( -iname "*.md" -o -iname "*.pdf" -o -iname "*.docx" \) -print0 \
  | while IFS= read -r -d '' file; do
    cp "$file" "$DOCS_DIR/"
    echo "Copiado: $(basename "$file")"
  done

# Exemplo para gerar documentação do frontend (TypeScript) usando typedoc
# Necessita de typedoc instalado globalmente (`npm install -g typedoc`).
# typedoc --out "$DOCS_DIR/api/frontend" "$ROOT_DIR/src"

# Exemplo para gerar documentação do backend (PHP/Laravel) usando phpDocumentor
# Necessita de phpDocumentor instalado (`composer require --dev phpdocumentor/phpdocumentor`).
# ./vendor/bin/phpdoc -d "$ROOT_DIR/backend" -t "$DOCS_DIR/api/backend"

# Mensagem final
echo "Documentação actualizada em: $DOCS_DIR"