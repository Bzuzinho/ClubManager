# Deployment Guide - ClubManager

## Pré-requisitos

### Servidor
- Ubuntu 22.04 LTS ou superior
- Nginx 1.24+
- PHP 8.3+ (com extensões: mbstring, xml, ctype, json, mysql, pdo_mysql, redis, zip)
- MySQL 8.0+ ou MariaDB 10.11+
- Node.js 22.x LTS
- Redis 7.0+ (para cache e queues)
- Composer 2.x
- Git

### Domínio e SSL
- Domínio configurado apontando para o servidor
- Certificado SSL (recomendado: Let's Encrypt via Certbot)

---

## 1. Preparação do Servidor

### 1.1 Instalar Dependências

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# PHP 8.3 e extensões
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-xml \
    php8.3-mbstring php8.3-curl php8.3-zip php8.3-redis php8.3-gd

# MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Nginx
sudo apt install -y nginx

# Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server

# Node.js 22.x
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 1.2 Configurar MySQL

```bash
sudo mysql -u root -p

CREATE DATABASE clubmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'clubmanager'@'localhost' IDENTIFIED BY 'senha_segura_aqui';
GRANT ALL PRIVILEGES ON clubmanager.* TO 'clubmanager'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 1.3 Criar Utilizador de Deploy

```bash
sudo adduser deployer
sudo usermod -aG www-data deployer
sudo mkdir -p /var/www/clubmanager
sudo chown -R deployer:www-data /var/www/clubmanager
```

---

## 2. Configuração da Aplicação

### 2.1 Clonar Repositório

```bash
su - deployer
cd /var/www/clubmanager
git clone git@github.com:your-org/clubmanager.git .
```

### 2.2 Backend Setup

```bash
cd /var/www/clubmanager/backend

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Configurar ambiente
cp .env.example .env
nano .env
```

**Configuração .env para produção:**
```env
APP_NAME="ClubManager"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://clubmanager.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clubmanager
DB_USERNAME=clubmanager
DB_PASSWORD=senha_segura_aqui

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

```bash
# Gerar chave
php artisan key:generate

# Executar migrações
php artisan migrate --force

# Seeders (apenas primeira vez)
php artisan db:seed --class=RolesAndPermissionsSeeder

# Cache de produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Permissões
sudo chown -R deployer:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 2.3 Frontend Setup

```bash
cd /var/www/clubmanager/frontend

# Instalar dependências
npm ci

# Build para produção
npm run build

# Resultado em ./dist
```

---

## 3. Configuração do Nginx

### 3.1 Criar Configuração do Site

```bash
sudo nano /etc/nginx/sites-available/clubmanager
```

```nginx
# Frontend (React)
server {
    listen 80;
    listen [::]:80;
    server_name clubmanager.example.com;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name clubmanager.example.com;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/clubmanager.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/clubmanager.example.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    root /var/www/clubmanager/frontend/dist;
    index index.html;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Frontend routing (React Router)
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
}

# API Backend (Laravel)
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.clubmanager.example.com;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/api.clubmanager.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.clubmanager.example.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    root /var/www/clubmanager/backend/public;
    index index.php;

    # Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

### 3.2 Ativar Site

```bash
sudo ln -s /etc/nginx/sites-available/clubmanager /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 4. SSL com Let's Encrypt

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obter certificados
sudo certbot --nginx -d clubmanager.example.com -d api.clubmanager.example.com

# Renovação automática (já configurado)
sudo systemctl status certbot.timer
```

---

## 5. Queue Workers (Laravel)

### 5.1 Criar Serviço Systemd

```bash
sudo nano /etc/systemd/system/clubmanager-worker.service
```

```ini
[Unit]
Description=ClubManager Queue Worker
After=network.target redis-server.service mysql.service

[Service]
Type=simple
User=deployer
Group=www-data
Restart=always
RestartSec=5s
ExecStart=/usr/bin/php /var/www/clubmanager/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable clubmanager-worker
sudo systemctl start clubmanager-worker
sudo systemctl status clubmanager-worker
```

---

## 6. Scheduler (Laravel Cron)

```bash
sudo crontab -u deployer -e
```

Adicionar:
```cron
* * * * * cd /var/www/clubmanager/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Monitorização e Logs

### 7.1 Logs do Laravel

```bash
tail -f /var/www/clubmanager/backend/storage/logs/laravel.log
```

### 7.2 Logs do Nginx

```bash
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### 7.3 Logs do PHP-FPM

```bash
tail -f /var/log/php8.3-fpm.log
```

---

## 8. Backup Automático

### 8.1 Script de Backup

```bash
sudo nano /usr/local/bin/clubmanager-backup.sh
```

```bash
#!/bin/bash
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/clubmanager"
DB_NAME="clubmanager"
DB_USER="clubmanager"
DB_PASS="senha_segura_aqui"

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$TIMESTAMP.sql.gz

# Files backup
tar -czf $BACKUP_DIR/files_$TIMESTAMP.tar.gz \
    /var/www/clubmanager/backend/storage \
    /var/www/clubmanager/backend/.env

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $TIMESTAMP"
```

```bash
sudo chmod +x /usr/local/bin/clubmanager-backup.sh

# Agendar backup diário às 2h
sudo crontab -e
0 2 * * * /usr/local/bin/clubmanager-backup.sh >> /var/log/clubmanager-backup.log 2>&1
```

---

## 9. Deploy Automático com GitHub Actions

### 9.1 Configurar SSH Key no Servidor

```bash
# No servidor
su - deployer
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Gerar chave (se não existir)
ssh-keygen -t ed25519 -C "deploy@clubmanager"

# Adicionar ao authorized_keys
cat ~/.ssh/id_ed25519.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### 9.2 Adicionar Secrets no GitHub

No repositório GitHub: **Settings → Secrets and variables → Actions**

- `DEPLOY_HOST`: IP ou domínio do servidor
- `DEPLOY_USER`: `deployer`
- `DEPLOY_KEY`: Conteúdo de `~/.ssh/id_ed25519` (chave privada)
- `VITE_API_URL`: `https://api.clubmanager.example.com`

### 9.3 Verificar Workflows

Os workflows estão em `.github/workflows/`:
- `backend-ci.yml`: Testes automáticos do backend
- `frontend-ci.yml`: Testes e build do frontend
- `deploy.yml`: Deploy automático em push para `main`

---

## 10. Segurança Adicional

### 10.1 Firewall (UFW)

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

### 10.2 Fail2Ban (proteção contra brute-force)

```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## 11. Manutenção

### 11.1 Atualizar Aplicação

```bash
cd /var/www/clubmanager
git pull origin main

# Backend
cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan queue:restart
sudo systemctl reload php8.3-fpm

# Frontend
cd ../frontend
npm ci
npm run build
```

### 11.2 Verificar Saúde da Aplicação

```bash
# Laravel
cd /var/www/clubmanager/backend
php artisan about

# Queue workers
sudo systemctl status clubmanager-worker

# Logs recentes
php artisan log:show --lines=50
```

---

## 12. Troubleshooting

### Permissões

```bash
sudo chown -R deployer:www-data /var/www/clubmanager
sudo chmod -R 775 /var/www/clubmanager/backend/storage
sudo chmod -R 775 /var/www/clubmanager/backend/bootstrap/cache
```

### Cache

```bash
cd /var/www/clubmanager/backend
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Queue stuck

```bash
sudo systemctl restart clubmanager-worker
php artisan queue:flush
```

---

## Checklist de Deploy ✅

- [ ] Servidor configurado (PHP, MySQL, Nginx, Redis, Node.js)
- [ ] Base de dados criada e configurada
- [ ] Repositório clonado
- [ ] `.env` configurado corretamente
- [ ] Dependências instaladas (composer, npm)
- [ ] Migrações executadas
- [ ] Frontend built
- [ ] Nginx configurado e ativo
- [ ] SSL certificado instalado
- [ ] Queue worker ativo
- [ ] Cron jobs configurados
- [ ] Backup automático configurado
- [ ] Firewall configurado
- [ ] Monitorização configurada
- [ ] GitHub Actions secrets configurados
