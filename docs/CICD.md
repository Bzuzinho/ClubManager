# CI/CD Configuration Guide

## GitHub Secrets Required

Configure estes secrets em: **Settings → Secrets and variables → Actions**

### Deploy Secrets
```
DEPLOY_HOST         - IP ou domínio do servidor (ex: 192.168.1.100)
DEPLOY_USER         - Utilizador SSH no servidor (ex: deployer)
DEPLOY_KEY          - Chave SSH privada para acesso ao servidor
VITE_API_URL        - URL da API para frontend (ex: https://api.clubmanager.example.com)
```

### Optional Secrets
```
CODECOV_TOKEN       - Token do Codecov para reports de cobertura
SLACK_WEBHOOK_URL   - Webhook do Slack para notificações de deploy
```

---

## Workflows Disponíveis

### 1. Backend CI (`backend-ci.yml`)

**Trigger:** Push ou PR em `main`/`develop` com alterações em `backend/**`

**Execução:**
- ✅ Matrix PHP 8.2 e 8.3
- ✅ MySQL 8.0 service
- ✅ Composer install
- ✅ Run migrations
- ✅ PHPUnit tests com coverage mínimo 80%
- ✅ PHPStan análise estática (level 5)
- ✅ Laravel Pint code style check
- ✅ Upload coverage para Codecov

**Comandos locais equivalentes:**
```bash
cd backend
composer ci  # Executa format:test + analyse + test:coverage
```

### 2. Frontend CI (`frontend-ci.yml`)

**Trigger:** Push ou PR em `main`/`develop` com alterações em `frontend/**`

**Execução:**
- ✅ Matrix Node.js 20.x e 22.x
- ✅ npm ci (clean install)
- ✅ ESLint check
- ✅ TypeScript type check
- ✅ Vitest tests (quando implementados)
- ✅ Build production
- ✅ Upload artifacts

**Comandos locais equivalentes:**
```bash
cd frontend
npm run lint
npm run type-check
npm run test:ci
npm run build
```

### 3. Deploy (`deploy.yml`)

**Trigger:** Push em `main` ou manual via `workflow_dispatch`

**Execução:**
1. **Backend:**
   - Composer install (production)
   - Deploy via SSH
   - Run migrations
   - Cache config/routes/views
   - Restart queue workers
   - Reload PHP-FPM

2. **Frontend:**
   - npm install
   - Build com env vars de produção
   - Upload via SCP
   - Reload Nginx

**Deploy manual:**
```bash
# Via GitHub UI: Actions → Deploy to Production → Run workflow
```

---

## Status Badges

Adicionar ao README.md:

```markdown
![Backend CI](https://github.com/your-org/clubmanager/workflows/CI%20-%20Backend%20Tests/badge.svg)
![Frontend CI](https://github.com/your-org/clubmanager/workflows/CI%20-%20Frontend%20Tests/badge.svg)
![Deploy](https://github.com/your-org/clubmanager/workflows/CD%20-%20Deploy%20to%20Production/badge.svg)
[![codecov](https://codecov.io/gh/your-org/clubmanager/branch/main/graph/badge.svg)](https://codecov.io/gh/your-org/clubmanager)
```

---

## Branch Protection Rules

Configurar em: **Settings → Branches → Branch protection rules**

### Para `main`:
- ✅ Require a pull request before merging
- ✅ Require status checks to pass before merging:
  - `test (8.3)` - Backend tests
  - `test (22.x)` - Frontend tests
- ✅ Require branches to be up to date before merging
- ✅ Do not allow bypassing the above settings

### Para `develop`:
- ✅ Require status checks to pass before merging
- ✅ Require branches to be up to date before merging

---

## Local Development Setup

### Backend

```bash
cd backend

# Instalar larastan
composer require --dev larastan/larastan

# Executar análise
composer analyse

# Formatar código
composer format

# Verificar formatação
composer format:test

# Testes com coverage
composer test:coverage

# Executar tudo (CI local)
composer ci
```

### Frontend

```bash
cd frontend

# Instalar dependências de testes
npm install -D vitest @vitest/ui @testing-library/react @testing-library/jest-dom jsdom

# Executar linter
npm run lint
npm run lint:fix

# Type checking
npm run type-check

# Testes
npm run test        # Watch mode
npm run test:ci     # CI mode com coverage

# Formatação
npm run format
npm run format:check
```

---

## Pre-commit Hooks (Recomendado)

### Instalar Husky

```bash
# Na raiz do projeto
npm install -D husky lint-staged

# Inicializar husky
npx husky init

# Criar hook pre-commit
echo "npx lint-staged" > .husky/pre-commit
chmod +x .husky/pre-commit
```

### Configurar lint-staged

Criar `.lintstagedrc.json` na raiz:

```json
{
  "backend/**/*.php": [
    "cd backend && vendor/bin/pint"
  ],
  "frontend/src/**/*.{ts,tsx,js,jsx}": [
    "cd frontend && npm run lint:fix",
    "cd frontend && npm run format"
  ]
}
```

---

## Continuous Deployment Strategy

### Environments

1. **Development** (`develop` branch)
   - Auto-deploy para servidor de staging
   - Testes podem falhar sem bloquear merge
   - Dados de teste

2. **Staging** (`staging` branch)
   - Auto-deploy para servidor de staging
   - Requer todos os testes a passar
   - Dados semelhantes a produção

3. **Production** (`main` branch)
   - Deploy manual ou aprovação necessária
   - Requer todos os testes + aprovação
   - Dados reais

### Deploy Flow

```
Feature Branch → develop (auto-deploy staging)
                    ↓ (após testes)
              staging (auto-deploy staging)
                    ↓ (após validação)
                  main (deploy production)
```

---

## Monitoring & Alerts

### GitHub Actions Notifications

Adicionar ao fim de cada workflow:

```yaml
- name: Notify Slack on Failure
  if: failure()
  uses: 8398a7/action-slack@v3
  with:
    status: ${{ job.status }}
    text: 'CI Failed on ${{ github.repository }}'
    webhook_url: ${{ secrets.SLACK_WEBHOOK_URL }}
```

### Health Checks

Criar endpoint de health check:

```php
// routes/api.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getDatabaseName(),
        'redis' => Cache::store('redis')->get('health_check') ?? 'ok',
    ]);
});
```

Monitorizar via cron ou serviço externo (UptimeRobot, Pingdom).

---

## Troubleshooting CI/CD

### Testes Falhando Localmente mas Passando no CI

```bash
# Limpar cache
cd backend
php artisan config:clear
php artisan cache:clear
composer dump-autoload

# Recriar database
php artisan migrate:fresh
```

### Deploy SSH Timeout

- Verificar firewall do servidor
- Verificar chave SSH está correta nos secrets
- Testar SSH manual: `ssh deployer@host`

### Build Frontend Falha

- Verificar Node.js version match (`.nvmrc`)
- Verificar env vars estão definidas
- Limpar node_modules: `rm -rf node_modules package-lock.json && npm install`

---

## Performance Optimization

### Backend Cache

```bash
# Após deploy, executar:
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

### Frontend Build

Otimizações no `vite.config.ts`:

```typescript
build: {
  rollupOptions: {
    output: {
      manualChunks: {
        'react-vendor': ['react', 'react-dom', 'react-router-dom'],
      },
    },
  },
  chunkSizeWarningLimit: 1000,
}
```

---

## Security Scan

Adicionar workflow de segurança:

```yaml
name: Security Scan

on:
  schedule:
    - cron: '0 0 * * 0'  # Weekly
  workflow_dispatch:

jobs:
  security:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Run Trivy vulnerability scanner
        uses: aquasecurity/trivy-action@master
        with:
          scan-type: 'fs'
          scan-ref: '.'
          
      - name: PHP Security Checker
        run: |
          cd backend
          composer audit
```

---

## Cost Optimization

### GitHub Actions Minutes

- Usar cache de dependências (`actions/cache`)
- Executar workflows apenas em paths relevantes
- Usar matrix strategy eficientemente
- Cancelar workflows redundantes em novos pushes

### Self-hosted Runners (Optional)

Para projetos maiores, considerar runners próprios:

```yaml
jobs:
  test:
    runs-on: self-hosted
```

---

## Next Steps

- [ ] Configurar todos os GitHub Secrets
- [ ] Testar workflows manualmente
- [ ] Configurar branch protection rules
- [ ] Adicionar badges ao README
- [ ] Configurar pre-commit hooks
- [ ] Implementar health checks
- [ ] Configurar monitoring (opcional)
