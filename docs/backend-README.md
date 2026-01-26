# ClubManager Backend - Laravel API

> **⚠️ IMPORTANTE:** Todo desenvolvimento novo deve usar a **API v2** (`/api/v2/*`).  
> Código legacy mantido apenas para compatibilidade. Ver [VERSIONING.md](../docs/VERSIONING.md).

---

## Stack Tecnológica

- **Laravel** 12.0
- **PHP** 8.2+
- **Sanctum** 4.2 (autenticação API)
- **Spatie Permission** 6.24 (roles & permissions)
- **SQLite** (dev) / **MySQL** (produção)

---

## Setup Rápido

```bash
# Instalar dependências
composer install

# Copiar .env
cp .env.example .env

# Gerar chave
php artisan key:generate

# Migrations + Seeders
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

---

## Arquitetura

### Nova Arquitetura (V2) ✅

**Usar esta estrutura para todo desenvolvimento novo:**

```
app/
├── Http/
│   ├── Controllers/Api/     # Controllers v2
│   ├── Middleware/          # EnsureClubContext
│   └── Resources/           # API Resources (criar aqui)
├── Models/
│   ├── Scopes/              # ClubScope (tenancy)
│   └── Traits/              # HasClubScope
├── Services/                # Lógica de negócio
│   ├── Tenancy/
│   ├── Membros/
│   └── Financeiro/
└── Policies/                # Autorização (criar aqui)
```

**Endpoints v2:** `/api/v2/*`  
**Middleware obrigatório:** `['auth:sanctum', 'ensure.club.context']`

### Código Legacy ⚠️

**NÃO desenvolver aqui:**

```
app/Http/Controllers/*Controller.php  # Controllers legacy
routes/api.php (endpoints sem /v2/)   # Rotas legacy
```

---

## Regras de Desenvolvimento

### 1. Multi-Clube (Tenancy)

**Todos os models operacionais usam `HasClubScope`:**

```php
use App\Models\Traits\HasClubScope;

class Membro extends Model
{
    use HasClubScope;  // Filtra automaticamente por club_id
}
```

**Para queries sem scope (admin):**
```php
Membro::allClubs()->get();  // Todos os clubes
Membro::forClub($clubId)->get();  // Clube específico
```

### 2. API Resources Obrigatórias

**❌ NUNCA fazer:**
```php
return response()->json($membro);  // Expõe estrutura interna
```

**✅ SEMPRE fazer:**
```php
return new MembroResource($membro);  // Formato normalizado
```

### 3. Autorização com Policies

```php
// No controller
$this->authorize('viewAny', Membro::class);
```

### 4. SoftDeletes APENAS em Configs

**❌ NÃO usar em:** Membro, Fatura, Pagamento, Pessoa, Atleta, Resultado, Presenca  
**✅ USAR em:** Templates, Campanhas (administráveis)

**Controlar com estados:**
```php
$membro->estado = 'inativo';  // Não usar delete()
$fatura->status_cache = 'cancelado';  // Não usar delete()
```

---

## Scripts Composer

```bash
composer setup    # Setup completo
composer dev      # Servidor + queue + logs + vite
composer test     # Testes PHPUnit
```

---

## Estrutura de Testes

```bash
tests/
├── Feature/          # Testes de controllers
├── Unit/             # Testes de services/models
└── TestCase.php
```

**Executar testes:**
```bash
php artisan test
php artisan test --filter=MembroServiceTest
```

---

## Documentação

- **[ESTADO_ATUAL_DO_SISTEMA.md](../docs/ESTADO_ATUAL_DO_SISTEMA.md)** - Estado completo
- **[VERSIONING.md](../docs/VERSIONING.md)** - Separação v2 vs legacy
- **[ClubManager_SPEC_DEFINITIVA_Copilot_Rewrite.md](../docs/ClubManager_SPEC_DEFINITIVA_Copilot_Rewrite.md)** - Especificação técnica

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
