# ClubManager (Base funcional Laravel + React)

Este projeto é um sistema completo de gestão de clubes com **backend Laravel 12** e **frontend React 19**, incluindo autenticação, multi-tenancy, testes automatizados e monitoring completo.

## 🚀 Features Implementadas

### Backend (Laravel 12)
- ✅ API RESTful completa com Resources
- ✅ Autenticação Sanctum
- ✅ Multi-tenancy (ClubScope)
- ✅ Autorização (Policies)
- ✅ 91 testes automatizados
- ✅ CI/CD com GitHub Actions
- ✅ PHPStan Level 5
- ✅ Laravel Pint

### Frontend (React 19 + TypeScript)
- ✅ Interface moderna com Vite
- ✅ React Router v7
- ✅ 29 testes (13 unitários + 16 E2E)
- ✅ Error tracking (Sentry)
- ✅ Performance monitoring
- ✅ Logging estruturado
- ✅ Monitoring dashboard

## 📋 Pré-requisitos
- PHP 8.3+
- Composer 2+
- Node 20+ (ou 18+)
- npm
- python3 (para pequenos ajustes no bootstrap)

## 🏗️ Setup Inicial (1ª vez)

### 1. Bootstrap do Projeto
Na raiz do projeto:

```bash
bash bootstrap.sh
```

### 2. Setup da FASE 5 (Testes & Monitoring)
```bash
bash setup_fase5.sh
npx playwright install  # Instalar browsers para E2E
```

### 3. Backend
```bash
cd backend
php artisan migrate
php artisan db:seed
php artisan serve --host=0.0.0.0 --port=8000
```

### 4. Frontend
Noutro terminal:
```bash
cd frontend
npm run dev -- --host 0.0.0.0 --port 5173
```

Abrir:
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api

## 🔐 Credenciais de teste
- Email: admin@admin.pt
- Password: password

## 🗃️ Base de Dados (Neon PostgreSQL)
O bootstrap escreve no `backend/.env`:

```env
DATABASE_URL="postgresql://neondb_owner:npg_t1IUDsnqzCB5@ep-bitter-glade-agamupv1-pooler.c-2.eu-central-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require"
DB_CONNECTION=pgsql
FRONTEND_URL="http://localhost:5173"
```

## 🧪 Testes

### Backend
```bash
cd backend
npm run test           # PHPUnit
npm run analyse        # PHPStan
npm run format:test    # Pint check
```

### Frontend
```bash
cd frontend
# Testes unitários
npm run test           # Watch mode
npm run test:ui        # UI interface
npm run test:ci        # CI mode + coverage

# Testes E2E
npm run test:e2e       # Headless
npm run test:e2e:ui    # Com UI
npm run test:e2e:debug # Debug mode
```

## 📊 Monitoring

### Error Tracking (Sentry)
Configurar DSN no `.env`:
```env
VITE_SENTRY_DSN=https://your-dsn@sentry.io/project
```

### Development Dashboard
O dashboard de monitoring aparece automaticamente em modo development (canto inferior direito).

### Logs
Todos os logs são estruturados e aparecem no console:
- 🔵 Debug (apenas dev)
- 🟢 Info
- 🟡 Warn
- 🔴 Error

## 📚 Documentação

### Geral
- [FASE_5_RESUMO.md](FASE_5_RESUMO.md) - Resumo executivo da FASE 5
- [FASE_5_CONCLUIDA.md](FASE_5_CONCLUIDA.md) - Documentação técnica completa

### Guias Específicos
- [docs/GUIA_TESTES_MONITORING.md](docs/GUIA_TESTES_MONITORING.md) - Como usar testes e monitoring
- [docs/CICD.md](docs/CICD.md) - Configuração CI/CD
- [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) - Deploy em produção

### Fases Anteriores
- [FASE_1_CONCLUIDA.md](docs/FASE_1_CONCLUIDA.md) - Correções críticas
- [FASE_3_CONCLUIDA.md](docs/FASE_3_CONCLUIDA.md) - Testes backend
- [FASE_4_CONCLUIDA.md](docs/FASE_4_CONCLUIDA.md) - CI/CD

## 🏗️ Estrutura do Projeto

```
ClubManager/
├── backend/                # Laravel 12 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── tests/
│       ├── Feature/        # 91 testes
│       └── Unit/
│
├── frontend/               # React 19 + TypeScript
│   ├── src/
│   │   ├── components/
│   │   ├── lib/
│   │   │   ├── api.ts
│   │   │   ├── sentry.ts
│   │   │   ├── logger.ts
│   │   │   └── performance.ts
│   │   ├── tests/          # 13 testes unitários
│   │   └── examples/
│   ├── e2e/                # 16 testes E2E
│   └── playwright.config.ts
│
├── docs/                   # Documentação
└── .github/workflows/      # CI/CD
```

## 🚀 Deploy

### Production Checklist
- [ ] Configurar variáveis de ambiente
- [ ] Configurar Sentry DSN
- [ ] Executar migrations
- [ ] Build do frontend: `npm run build`
- [ ] Configurar servidor web (Nginx/Apache)
- [ ] Configurar queue workers
- [ ] Configurar SSL

Ver [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) para guia completo.

## 📊 Status das Fases

- ✅ **FASE 1:** Correções críticas (versioning, soft deletes, normalization)
- ✅ **FASE 2:** API Resources, Policies, Authorization
- ✅ **FASE 3:** Testes backend (91 testes)
- ✅ **FASE 4:** CI/CD, PHPStan, Pint, Deployment
- ✅ **FASE 5:** Testes frontend, Monitoring, Performance tracking
- ⏳ **FASE 6:** Performance Optimization (próxima)

## 🤝 Contribuindo

1. Criar branch: `git checkout -b feature/nova-feature`
2. Fazer alterações e testes
3. Executar testes: `npm run test:ci`
4. Commit: `git commit -m "feat: nova feature"`
5. Push: `git push origin feature/nova-feature`
6. Abrir Pull Request

## 📝 Comandos Úteis

### Backend
```bash
php artisan route:list        # Listar rotas
php artisan tinker            # REPL
php artisan queue:work        # Queue worker
php artisan test              # Executar testes
```

### Frontend
```bash
npm run dev                   # Dev server
npm run build                 # Build produção
npm run preview               # Preview build
npm run lint                  # ESLint
npm run type-check            # TypeScript check
```

## 🐛 Troubleshooting

### Backend
**Erro de conexão DB:**
```bash
php artisan config:clear
php artisan cache:clear
```

**Migrations falhando:**
```bash
php artisan migrate:fresh --seed
```

### Frontend
**Testes falhando:**
```bash
npm run test:ci
```

**Build failing:**
```bash
rm -rf node_modules
npm install
npm run build
```

Ver mais em [docs/GUIA_TESTES_MONITORING.md](docs/GUIA_TESTES_MONITORING.md)

## 📞 Suporte

- **Documentação:** Ver pasta `docs/`
- **Issues:** GitHub Issues
- **Exemplos:** `frontend/src/examples/`

---

**Made with ❤️ using Laravel 12 + React 19**

