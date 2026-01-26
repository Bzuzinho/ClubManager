#!/usr/bin/env bash
set -euo pipefail

echo "🧹 Limpando recursos do Codespace..."

# Backend cleanup
if [ -d "backend" ]; then
  cd backend
  echo "→ Limpando cache Laravel..."
  php artisan cache:clear 2>/dev/null || true
  php artisan config:clear 2>/dev/null || true
  php artisan view:clear 2>/dev/null || true
  composer clear-cache 2>/dev/null || true
  
  # Limpar logs antigos
  if [ -d "storage/logs" ]; then
    find storage/logs -name "*.log" -mtime +7 -delete 2>/dev/null || true
  fi
  cd ..
fi

# Frontend cleanup
if [ -d "frontend" ]; then
  cd frontend
  echo "→ Limpando cache npm..."
  npm cache clean --force 2>/dev/null || true
  
  # Remover cache do Vite
  rm -rf node_modules/.vite 2>/dev/null || true
  cd ..
fi

# Limpar processos órfãos
echo "→ Verificando processos..."
pkill -f "bootstrap.sh" 2>/dev/null || true

echo "✅ Limpeza concluída!"
