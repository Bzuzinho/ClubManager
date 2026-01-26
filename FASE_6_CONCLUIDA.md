# FASE 6 - DevOps e Deploy - CONCLUÍDA ✅

**Data de Conclusão:** 22 de janeiro de 2026

## Objetivos da FASE 6

Implementar infraestrutura completa de **DevOps**, **deploy automatizado**, **monitoring** e **backup** para ambientes de produção e staging.

---

## 1. Configurações de Ambiente

### 1.1 Arquivos .env por Ambiente

Criados templates específicos para cada ambiente:

**✅ `.env.development`** - Desenvolvimento local
- SQLite database
- File cache
- Sync queue
- Mail logger
- Debug habilitado
- Sentry desabilitado

**✅ `.env.staging`** - Pré-produção
- MySQL/PostgreSQL
- Redis cache + queue + session
- S3 storage
- SMTP real (Mailtrap)
- Debug habilitado
- Sentry com sample 50%
- Backup diário (14 dias retenção)

**✅ `.env.production`** - Produção
- MySQL/PostgreSQL com connection pooling
- Redis cluster (cache/queue/session)
- S3 storage
- AWS SES/SendGrid
- Debug desabilitado
- Sentry 100% tracking
- Backup diário (30 dias retenção)
- Security headers habilitados
- Rate limiting habilitado

### 1.2 Variáveis Críticas de Produção

```env
# Security
SECURE_COOKIES=true
SESSION_SECURE_COOKIE=true
HSTS_ENABLED=true
HSTS_MAX_AGE=31536000

# Performance
DB_POOL_MIN=5
DB_POOL_MAX=20
REDIS_PREFIX=clubmanager_

# Monitoring
SENTRY_LARAVEL_DSN=https://...
MONITORING_ENABLED=true

# Backup
BACKUP_ENABLED=true
BACKUP_RETENTION_DAYS=30
BACKUP_TIME=03:00
```

---

## 2. Configurações de Infraestrutura

### 2.1 Redis (Cache + Queue + Session)

**Arquivo:** `config/database.php` (já existente)

**Conexões configuradas:**
- `default` - Redis DB 0 (geral)
- `cache` - Redis DB 1 (cache)
- `queue` - Redis DB 1 (queue)

**Features:**
- Persistent connections opcional
- Connection pooling
- Retry strategy (decorrelated jitter)
- Prefix por aplicação

### 2.2 Storage (S3 + Local)

**Arquivo:** `config/filesystems.php` (atualizado)

**Disks configurados:**
- `local` - Storage privado local
- `public` - Storage público (storage/app/public)
- `s3` - AWS S3 (produção)
- `documents` - Documentos do clube (S3 ou local)
- `backups` - Backups (S3 ou local)
- `exports` - Exports temporários (local)
- `temp` - Arquivos temporários (local)

**Vantagens:**
- Troca automática entre local/S3 por ambiente
- Disks específicos por tipo de arquivo
- Gestão de visibilidade (public/private)

### 2.3 Queue System

**Arquivo:** `config/queue.php` (já existente)

**Connections:**
- `sync` - Desenvolvimento (processamento imediato)
- `database` - Fallback
- `redis` - Produção (default)
- `high` - Prioridade alta (emails urgentes)
- `low` - Prioridade baixa (reports, exports)
- `emails` - Queue dedicada para emails

**Configuração Redis:**
- Retry after: 90s (default), 60s (high), 300s (low)
- Block for: null (non-blocking)
- Failed jobs: database storage

### 2.4 Logging Estruturado

**Arquivo:** `config/logging.php` (criado)

**Channels customizados:**
- `auth` - Logs de autenticação (30 dias)
- `api` - Logs de API requests (14 dias)
- `financial` - Logs financeiros críticos (90 dias)
- `audit` - Auditoria compliance (365 dias)
- `performance` - Performance issues (7 dias)
- `security` - Eventos de segurança (180 dias)

**Service:** `LoggingService` (criado)
```php
LoggingService::logAuth('login_success', $userId);
LoggingService::logApi('GET', '/api/membros', 200, 0.15);
LoggingService::logFinancial('fatura_criada', ['id' => 123]);
LoggingService::logAudit('Membro', 'update', $membroId, $changes);
LoggingService::logPerformance('slow_query', 1500, 'ms');
LoggingService::logSecurity('failed_login_attempt', 'warning');
LoggingService::logCritical($exception);
```

---

## 3. CI/CD Pipelines

### 3.1 Workflow de Deploy Produção

**Arquivo:** `.github/workflows/deploy-production.yml`

**Trigger:**
- Push para `main` branch
- Manual dispatch (escolher production/staging)

**Jobs:**

**1. Tests:**
- Setup PHP 8.3
- Install backend dependencies
- Run PHPUnit tests
- Setup Node.js 20.x
- Install frontend dependencies
- Run Vitest + Playwright

**2. Deploy:**
- Requires tests passing
- Environment: production/staging
- Install dependencies (no-dev)
- Build frontend com variáveis corretas
- SSH deploy:
  - Pull latest code
  - Install composer dependencies
  - Put application down (maintenance)
  - Run migrations
  - Cache config/routes/views
  - Restart queue workers
  - Build frontend
  - Bring application up
  - Reload nginx

**3. Post-Deploy:**
- Health check (curl /health)
- Notify Sentry of release
- Notify Slack on failure

**Secrets necessários:**
```
SSH_HOST
SSH_USERNAME
SSH_PRIVATE_KEY
SSH_PORT
DEPLOY_PATH
APP_URL
VITE_API_URL
VITE_SENTRY_DSN
SENTRY_ORG
SENTRY_TOKEN
SLACK_WEBHOOK
```

### 3.2 Workflow de Backup

**Arquivo:** `.github/workflows/backup.yml`

**Trigger:**
- Cron diário: 3:00 AM UTC
- Manual dispatch

**Job:**
- SSH para servidor
- Executa `php artisan backup:run --only-db`
- Upload automático para S3
- Clean old backups
- Verifica backup com `backup:list`
- Notifica Slack on failure

**Retenção:**
- 7 dias: todos os backups
- 16 dias: backups diários
- 8 semanas: backups semanais
- 4 meses: backups mensais
- 2 anos: backups anuais

### 3.3 Workflows Existentes (mantidos)

- **backend-ci.yml** - Testes PHP (matrix 8.2/8.3)
- **frontend-ci.yml** - Testes React (matrix Node 20/22)
- **deploy.yml** - Deploy básico (substituído por deploy-production.yml)

---

## 4. Scripts de Deploy

### 4.1 deploy.sh

**Localização:** Raiz do projeto

**Uso:**
```bash
./deploy.sh production  # Deploy para produção
./deploy.sh staging     # Deploy para staging
```

**Passos:**
1. Valida pré-requisitos (PHP, Composer, NPM, Git)
2. Pull latest code
3. Install backend dependencies
4. Put application down
5. Run migrations
6. Cache config/routes/views
7. Restart queue workers
8. Build frontend
9. Bring application up
10. Reload web server
11. Health check

**Features:**
- Output colorido
- Validação de ambiente
- Health check automático
- Safe failure handling

### 4.2 backup.sh

**Localização:** Raiz do projeto

**Uso:**
```bash
./backup.sh  # Backup completo
```

**Passos:**
1. Cria diretório de backup
2. Backup database (mysqldump ou Laravel backup)
3. Backup files (uploads, documentos)
4. Clean old backups (>30 dias)
5. Lista backups recentes

**Configuração via ENV:**
```bash
BACKUP_DIR=/var/backups/clubmanager
APP_PATH=/var/www/clubmanager
RETENTION_DAYS=30
```

### 4.3 rollback.sh

**Localização:** Raiz do projeto

**Uso:**
```bash
./rollback.sh HEAD~1  # Rollback para commit anterior
./rollback.sh abc123  # Rollback para commit específico
```

**Passos:**
1. Confirmação manual (yes/no)
2. Put application down
3. Git reset --hard para versão anterior
4. Reinstall dependencies
5. Skip migration rollback (manual)
6. Clear caches
7. Rebuild frontend
8. Bring application up

**⚠️ Avisos:**
- Migrations não são revertidas automaticamente
- Requer verificação manual de compatibilidade
- Fazer backup antes de rollback

### 4.4 Permissões

Todos os scripts têm permissões de execução (`chmod +x`):
- `deploy.sh`
- `backup.sh`
- `rollback.sh`
- `setup_fase5.sh`
- `bootstrap.sh`

---

## 5. Backup Automatizado

### 5.1 Configuração

**Arquivo:** `config/backup.php`

**Fontes de backup:**
- Database (MySQL/PostgreSQL)
- Files:
  - `storage/app/documents`
  - `storage/app/public`
- Excluídos:
  - `storage/app/temp`
  - `storage/framework/cache`
  - `storage/framework/sessions`

**Destinos:**
- Disk configurável (local ou S3)
- Filename prefix customizável
- Compressão automática

**Notificações:**
- Email em sucesso/falha
- Slack (opcional)
- Discord (opcional)

**Health checks:**
- Maximum age: 1 dia
- Maximum storage: 5000 MB

**Cleanup strategy:**
- Keep all: 7 dias
- Daily: 16 dias
- Weekly: 8 semanas
- Monthly: 4 meses
- Yearly: 2 anos

### 5.2 Command Artisan

**Arquivo:** `app/Console/Commands/BackupDatabase.php`

**Uso:**
```bash
php artisan backup:database
php artisan backup:database --compress
php artisan backup:database --disk=s3
```

**Features:**
- Mysqldump direto
- Compressão gzip opcional
- Upload para S3 automático
- Output formatado com tabela
- Formato de tamanho human-readable

**Output:**
```
Starting database backup...
Backing up database: clubmanager_production
Compressing backup...
Uploading to s3 disk...
Backup completed successfully!
┌──────────┬─────────────────────────┐
│ Property │ Value                   │
├──────────┼─────────────────────────┤
│ Database │ clubmanager_production  │
│ Filename │ database_2026-01-22...  │
│ Size     │ 45.23 MB               │
│ Disk     │ s3                      │
│ Timestamp│ 2026-01-22_030000      │
└──────────┴─────────────────────────┘
```

### 5.3 Agendamento

**Arquivo:** `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Backup diário às 3h da manhã
    $schedule->command('backup:database --compress --disk=s3')
             ->dailyAt('03:00')
             ->onFailure(function () {
                 // Notificar equipe
             });
    
    // Cleanup de logs antigos
    $schedule->command('backup:clean')
             ->daily();
}
```

---

## 6. Monitoring e Observabilidade

### 6.1 Health Check Endpoint

**Route:** `GET /health`

**Response:**
```json
{
  "status": "ok",
  "timestamp": "2026-01-22T15:30:00Z",
  "services": {
    "database": "ok",
    "redis": "ok",
    "storage": "ok"
  }
}
```

**Uso:**
- Load balancer health checks
- Monitoring tools (Uptime Robot, Pingdom)
- CI/CD post-deploy validation

### 6.2 Sentry Integration

**Backend:** `config/sentry.php` (Laravel)
```php
'dsn' => env('SENTRY_LARAVEL_DSN'),
'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV')),
'release' => env('SENTRY_RELEASE'),
'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 1.0),
```

**Frontend:** `src/lib/sentry.ts` (FASE 5)
- Error tracking
- Performance monitoring
- Session replay
- Breadcrumbs

**Deploy notifications:**
- Sentry Release API chamada no workflow
- Tracking de deploys por SHA
- Source maps uploaded automaticamente

### 6.3 Performance Monitoring

**LoggingService:**
```php
LoggingService::logPerformance('api_request', 1500, 'ms', [
    'endpoint' => '/api/membros',
    'method' => 'GET',
]);
```

**Métricas rastreadas:**
- Slow queries (>1s)
- API response times
- Queue processing times
- Cache hit rates

### 6.4 Security Monitoring

**LoggingService:**
```php
LoggingService::logSecurity('failed_login_attempt', 'warning', [
    'username' => $email,
    'attempts' => 3,
]);
```

**Eventos rastreados:**
- Failed login attempts
- Authorization failures
- Suspicious activity
- Rate limit exceeded

---

## 7. Documentação de Deploy

### 7.1 Guia de Deploy Manual

**Pré-requisitos:**
- Servidor Linux (Ubuntu 22.04+)
- PHP 8.3+
- Composer 2+
- Node.js 20+
- MySQL 8+ ou PostgreSQL 14+
- Redis 7+
- Nginx ou Apache
- Certbot (SSL)

**1. Preparar Servidor:**
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-redis \
  php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip \
  mysql-server redis-server nginx certbot python3-certbot-nginx \
  git composer nodejs npm

# Configurar PHP
sudo vim /etc/php/8.3/fpm/php.ini
# upload_max_filesize = 20M
# post_max_size = 20M
# memory_limit = 256M

# Configurar MySQL
sudo mysql_secure_installation
```

**2. Clonar Projeto:**
```bash
cd /var/www
sudo git clone https://github.com/user/clubmanager.git
sudo chown -R www-data:www-data clubmanager
cd clubmanager
```

**3. Configurar Backend:**
```bash
cd backend
cp .env.production .env
# Editar .env com valores reais
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**4. Configurar Frontend:**
```bash
cd ../frontend
npm ci
npm run build
```

**5. Configurar Nginx:**
```nginx
server {
    listen 80;
    server_name app.clubmanager.pt;
    root /var/www/clubmanager/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Frontend (servir build estático)
server {
    listen 80;
    server_name app.clubmanager.pt;
    root /var/www/clubmanager/frontend/dist;

    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

**6. SSL/HTTPS:**
```bash
sudo certbot --nginx -d app.clubmanager.pt
```

**7. Queue Workers:**
```bash
# Criar supervisor config
sudo nano /etc/supervisor/conf.d/clubmanager-worker.conf
```

```ini
[program:clubmanager-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/clubmanager/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/clubmanager/backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start clubmanager-worker:*
```

**8. Cron (Scheduler):**
```bash
sudo crontab -e -u www-data
```

```cron
* * * * * cd /var/www/clubmanager/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 7.2 Guia de Deploy Automático

**1. Configurar GitHub Secrets:**

No repositório GitHub:
- Settings → Secrets and variables → Actions → New repository secret

**Secrets necessários:**
```
SSH_HOST=123.456.789.0
SSH_USERNAME=deploy
SSH_PRIVATE_KEY=-----BEGIN RSA PRIVATE KEY-----...
SSH_PORT=22
DEPLOY_PATH=/var/www/clubmanager
APP_URL=https://app.clubmanager.pt
VITE_API_URL=https://app.clubmanager.pt/api
VITE_SENTRY_DSN=https://...@sentry.io/...
SENTRY_ORG=clubmanager
SENTRY_TOKEN=sntrys_...
SLACK_WEBHOOK=https://hooks.slack.com/services/...
```

**2. Push para Main:**
```bash
git push origin main
```

**3. Acompanhar Deploy:**
- GitHub → Actions tab
- Ver logs do workflow
- Verificar health check

**4. Verificar Produção:**
```bash
curl https://app.clubmanager.pt/health
```

### 7.3 Troubleshooting

**Problema: Migrations falhando**
```bash
php artisan migrate:status
php artisan migrate --force
```

**Problema: Storage permissions**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Problema: Queue workers não processam**
```bash
sudo supervisorctl status
sudo supervisorctl restart clubmanager-worker:*
php artisan queue:work --once  # teste manual
```

**Problema: Redis connection failed**
```bash
redis-cli ping  # deve retornar PONG
sudo systemctl status redis
```

**Problema: 502 Bad Gateway**
```bash
sudo systemctl status php8.3-fpm
sudo systemctl status nginx
sudo tail -f /var/log/nginx/error.log
```

---

## 8. Checklist de Deploy

### 8.1 Pré-Deploy

- [ ] Código reviewed e aprovado
- [ ] Testes passando (backend + frontend)
- [ ] Migrations testadas em staging
- [ ] Backup recente disponível
- [ ] Rollback plan documentado
- [ ] Notificar stakeholders

### 8.2 Durante Deploy

- [ ] Application em maintenance mode
- [ ] Migrations executadas
- [ ] Assets compilados
- [ ] Caches limpos e recriados
- [ ] Queue workers reiniciados
- [ ] Health check passed

### 8.3 Pós-Deploy

- [ ] Verificar aplicação funcionando
- [ ] Verificar logs (sem erros críticos)
- [ ] Testar features principais
- [ ] Monitorar Sentry (sem novos erros)
- [ ] Verificar performance
- [ ] Notificar stakeholders (sucesso)

---

## 9. Métricas e KPIs

### 9.1 Availability

**Target:** 99.9% uptime (8.76h downtime/ano)

**Monitoring:**
- Uptime Robot (checks a cada 5 min)
- Health endpoint
- Alertas Slack on downtime

### 9.2 Performance

**Targets:**
- API response: p95 < 500ms
- Page load: p95 < 2s
- Database queries: p95 < 100ms

**Monitoring:**
- Sentry performance monitoring
- Laravel Telescope (staging)
- Slow query logs

### 9.3 Errors

**Targets:**
- Error rate: < 1%
- Critical errors: 0

**Monitoring:**
- Sentry error tracking
- Daily error reports
- Alert on critical errors

### 9.4 Backups

**Targets:**
- Daily backups: 100% success rate
- Backup age: < 24h
- Restore test: mensal

**Monitoring:**
- Backup notifications
- GitHub Actions workflow
- Manual verification mensal

---

## 10. Conclusão

A FASE 6 estabeleceu uma infraestrutura completa de **DevOps** e **Deploy**:

### ✅ Achievements

1. **Configurações de Ambiente**
   - 3 ambientes completos (dev, staging, prod)
   - Variáveis otimizadas por ambiente
   - Security hardening em produção

2. **Infraestrutura**
   - Redis configurado (cache + queue + session)
   - S3 storage preparado
   - Logging estruturado (6 channels)
   - Backup automatizado

3. **CI/CD**
   - Pipeline de deploy completo
   - Backup diário automatizado
   - Health checks pós-deploy
   - Notificações Slack/Sentry

4. **Scripts**
   - Deploy script (colorido, seguro)
   - Backup script (database + files)
   - Rollback script (safe recovery)

5. **Monitoring**
   - LoggingService (6 tipos de logs)
   - Sentry integration
   - Performance tracking
   - Security monitoring

6. **Documentação**
   - Guia de deploy manual completo
   - Guia de deploy automático
   - Troubleshooting guide
   - Checklist de deploy

### 📊 Estatísticas

- **3** arquivos .env configurados
- **4** scripts shell criados
- **2** workflows GitHub Actions novos
- **6** channels de logging customizados
- **4** disks de storage configurados
- **1** command Artisan de backup
- **7** configurações de infraestrutura

### 🎯 Próximos Passos

**Opcional (Melhorias Futuras):**
1. Kubernetes deployment (containerização)
2. Load balancing (múltiplos servers)
3. CDN para assets estáticos
4. Database read replicas
5. Auto-scaling infrastructure
6. Disaster recovery plan completo

---

**Status Final:** ✅ **FASE 6 COMPLETA**

O ClubManager agora está **production-ready** com:
- Deploy automatizado
- Backup diário
- Monitoring completo
- Infraestrutura escalável
- Documentação completa

---

**Próxima fase sugerida:** Otimizações e melhorias baseadas em métricas reais de produção.
