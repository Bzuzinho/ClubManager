#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TPL="$ROOT_DIR/templates"

echo "==> ClubManager bootstrap"
echo "==> Pasta: $ROOT_DIR"

need_cmd() {
  command -v "$1" >/dev/null 2>&1 || { echo "ERRO: falta o comando '$1'."; exit 1; }
}

need_cmd php
need_cmd composer
need_cmd npm
need_cmd python3

echo "==> (1/5) Criar backend Laravel (se não existir)..."
if [ ! -d "$ROOT_DIR/backend" ]; then
  composer create-project laravel/laravel backend
fi

echo "==> (2/5) Configurar backend (Sanctum + API + .env)..."
pushd "$ROOT_DIR/backend" >/dev/null

cp .env.example .env

python3 - <<'PY'
import pathlib, re
env_path = pathlib.Path(".env")
txt = env_path.read_text(encoding="utf-8")
def set_kv(k,v,quote=False):
    global txt
    pat = re.compile(rf"^{re.escape(k)}=.*$", re.M)
    line = f'{k}="{v}"' if quote else f"{k}={v}"
    if pat.search(txt):
        txt = pat.sub(line, txt)
    else:
        txt += "\n" + line + "\n"
set_kv("APP_NAME","ClubManager")
set_kv("APP_URL","http://localhost:8000")
set_kv("FRONTEND_URL","http://localhost:5173", quote=True)
set_kv("DB_CONNECTION","pgsql")
set_kv("DATABASE_URL","postgresql://neondb_owner:npg_t1IUDsnqzCB5@ep-bitter-glade-agamupv1-pooler.c-2.eu-central-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require", quote=True)
env_path.write_text(txt, encoding="utf-8")
PY

php artisan key:generate --force

composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force

python3 - <<'PY'
import pathlib, re
p = pathlib.Path("config/cors.php")
txt = p.read_text(encoding="utf-8")
txt = re.sub(r"'paths'\s*=>\s*\[[^\]]*\]", "'paths' => ['api/*', 'sanctum/csrf-cookie']", txt, flags=re.S)
txt = re.sub(r"'allowed_origins'\s*=>\s*\[[^\]]*\]", "'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')]", txt, flags=re.S)
txt = re.sub(r"'allowed_headers'\s*=>\s*\[[^\]]*\]", "'allowed_headers' => ['*']", txt, flags=re.S)
txt = re.sub(r"'allowed_methods'\s*=>\s*\[[^\]]*\]", "'allowed_methods' => ['*']", txt, flags=re.S)
p.write_text(txt, encoding="utf-8")
PY

cp "$TPL/backend/9999_01_01_000001_add_role_to_users_table.php" database/migrations/9999_01_01_000001_add_role_to_users_table.php
cp "$TPL/backend/AdminUserSeeder.php" database/seeders/AdminUserSeeder.php
mkdir -p app/Http/Controllers/Api
cp "$TPL/backend/AuthController.php" app/Http/Controllers/Api/AuthController.php
cp "$TPL/backend/api.php" routes/api.php

python3 - <<'PY'
import pathlib, re
p = pathlib.Path("database/seeders/DatabaseSeeder.php")
txt = p.read_text(encoding="utf-8")
if "AdminUserSeeder" not in txt:
    # inserir call dentro do run()
    txt = re.sub(r"public function run\(\): void\s*\{\s*\}", 
                 "public function run(): void\n    {\n        $this->call(AdminUserSeeder::class);\n    }",
                 txt, flags=re.S)
p.write_text(txt, encoding="utf-8")
PY

popd >/dev/null

echo "==> (3/5) Criar frontend React (se não existir)..."
if [ ! -d "$ROOT_DIR/frontend" ]; then
  npm create vite@latest frontend -- --template react-ts
fi

echo "==> (4/5) Configurar frontend (router + axios + layout)..."
pushd "$ROOT_DIR/frontend" >/dev/null
npm install
npm install axios react-router-dom

cp "$TPL/frontend/vite.config.ts" vite.config.ts
mkdir -p src/{router,layouts,views,modules/members,modules/financial,modules/sports,modules/events}

cp "$TPL/frontend/src/main.tsx" src/main.tsx
cp "$TPL/frontend/src/app.css" src/app.css
cp "$TPL/frontend/src/api.ts" src/api.ts
cp "$TPL/frontend/src/router/index.tsx" src/router/index.tsx
cp "$TPL/frontend/src/layouts/DashboardLayout.tsx" src/layouts/DashboardLayout.tsx
cp "$TPL/frontend/src/views/Login.tsx" src/views/Login.tsx
cp "$TPL/frontend/src/views/Dashboard.tsx" src/views/Dashboard.tsx
cp "$TPL/frontend/src/modules/members/Members.tsx" src/modules/members/Members.tsx
cp "$TPL/frontend/src/modules/financial/Financial.tsx" src/modules/financial/Financial.tsx
cp "$TPL/frontend/src/modules/sports/Sports.tsx" src/modules/sports/Sports.tsx
cp "$TPL/frontend/src/modules/events/Events.tsx" src/modules/events/Events.tsx
cp "$TPL/frontend/src/App.tsx" src/App.tsx

popd >/dev/null

echo "==> (5/5) Concluído."
echo ""
echo "Próximos passos:"
echo "  Backend : cd backend && php artisan migrate && php artisan db:seed && php artisan serve --host=0.0.0.0 --port=8000"
echo "  Frontend: cd frontend && npm run dev -- --host=0.0.0.0 --port 5173"
