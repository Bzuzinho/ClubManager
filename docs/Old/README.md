# ClubManager (Base funcional Laravel + React)

Este ZIP é um **bootstrap**: ao correr um único script, ele **gera**:
- `backend/` (Laravel 12 + Sanctum + API /login e /me)
- `frontend/` (React + TS + Vite + Router + Layout base)
- Configuração DB via **Neon PostgreSQL** (DATABASE_URL)

## Pré-requisitos
- PHP 8.3+
- Composer 2+
- Node 20+ (ou 18+)
- npm
- python3 (para pequenos ajustes no bootstrap)

## Arrancar (1ª vez)
Na raiz do projeto:

```bash
bash bootstrap.sh
```

Depois:

### Backend
```bash
cd backend
php artisan migrate
php artisan db:seed
php artisan serve --host=0.0.0.0 --port=8000
```

### Frontend
Noutro terminal:
```bash
cd frontend
npm run dev -- --host 0.0.0.0 --port 5173
```

Abrir:
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api

## Credenciais de teste
- Email: admin@admin.pt
- Password: password

## BD (Neon)
O bootstrap escreve no `backend/.env`:

```env
DATABASE_URL="postgresql://neondb_owner:npg_t1IUDsnqzCB5@ep-bitter-glade-agamupv1-pooler.c-2.eu-central-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require"
DB_CONNECTION=pgsql
FRONTEND_URL="http://localhost:5173"
```
