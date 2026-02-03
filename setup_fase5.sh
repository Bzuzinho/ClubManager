#!/bin/bash

# Script para instalar dependências da FASE 5
# Frontend Tests & Monitoring

echo "======================================"
echo "FASE 5 - Instalação de Dependências"
echo "======================================"
echo ""

cd "$(dirname "$0")/frontend" || exit 1

echo "📦 Instalando dependências de teste..."
echo ""

# Instalar todas as dependências
npm install

echo ""
echo "✅ Dependências instaladas com sucesso!"
echo ""
echo "Dependências de teste:"
echo "  - vitest@^2.0.0"
echo "  - @testing-library/react@^16.0.0"
echo "  - @testing-library/jest-dom@^6.1.0"
echo "  - @testing-library/user-event@^14.5.0"
echo "  - jsdom@^24.0.0"
echo "  - @vitest/ui@^2.0.0"
echo "  - @playwright/test@^1.40.0"
echo ""
echo "Dependências de monitoring:"
echo "  - @sentry/react@^7.90.0"
echo "  - @sentry/tracing@^7.90.0"
echo ""
echo "======================================"
echo "Comandos disponíveis:"
echo "======================================"
echo ""
echo "Testes Unitários:"
echo "  npm run test              # Modo watch"
echo "  npm run test:ui           # Interface UI"
echo "  npm run test:coverage     # Com coverage"
echo "  npm run test:ci           # CI mode"
echo ""
echo "Testes E2E:"
echo "  npm run test:e2e          # Headless"
echo "  npm run test:e2e:ui       # Com UI"
echo "  npm run test:e2e:headed   # Browser visível"
echo "  npm run test:e2e:debug    # Debug mode"
echo ""
echo "Instalar Playwright browsers:"
echo "  npx playwright install"
echo ""
echo "======================================"
echo "✅ FASE 5 Setup Completo!"
echo "======================================"
