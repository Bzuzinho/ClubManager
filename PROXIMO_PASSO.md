# 🚀 PRÓXIMO PASSO - Colocar o Software Funcional

**Data:** 23 de Janeiro de 2026  
**Objetivo:** Sistema funcional básico em menos de 30 minutos

---

## 📋 Diagnóstico Atual

### ✅ O que já está pronto:
- Backend Laravel configurado
- 58 Migrations aplicadas (todas `Ran`)
- API com 82+ endpoints funcionais
- Frontend React estruturado e com módulo Members implementado
- Base de dados SQLite criada

### ❌ O que está a faltar:
- **Base de dados VAZIA** (0 users, 0 clubs, 0 membros)
- Seeders não executados
- Nenhum utilizador para fazer login

---

## 🎯 AÇÃO IMEDIATA (5 minutos)

### Passo 1: Executar os Seeders

```bash
cd /workspaces/ClubManager/backend
php artisan db:seed
```

**O que isto faz:**
- Cria clube "BSCN" (Barrosense Sport Clube Natação)
- Cria utilizador admin (admin@admin.pt / password)
- Cria roles e permissões (Spatie)
- Cria tipos de utilizador (Atleta, Encarregado, etc.)
- Cria escalões, provas, mensalidades
- Cria dados de exemplo (membros, atletas, faturas)

### Passo 2: Verificar se funcionou

```bash
php artisan tinker --execute="
echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
echo 'Clubs: ' . \App\Models\Club::count() . PHP_EOL;
echo 'Membros: ' . \App\Models\Membro::count() . PHP_EOL;
"
```

**Resultado esperado:**
```
Users: 10+
Clubs: 1
Membros: 10+
```

### Passo 3: Iniciar os Servidores

**Opção A - Tudo de uma vez (recomendado):**
```bash
cd /workspaces/ClubManager/backend
composer dev
```
Isto inicia automaticamente:
- Backend API (porta 8000)
- Queue worker
- Logs (Laravel Pail)
- Frontend Vite (porta 5173)

**Opção B - Separado (2 terminais):**

Terminal 1 (Backend):
```bash
cd /workspaces/ClubManager/backend
php artisan serve --host=0.0.0.0 --port=8000
```

Terminal 2 (Frontend):
```bash
cd /workspaces/ClubManager/frontend
npm run dev -- --host=0.0.0.0 --port=5173
```

### Passo 4: Aceder ao Sistema

1. **Frontend:** http://localhost:5173
2. **Login:**
   - Email: `admin@admin.pt`
   - Password: `password`
3. **Testar:**
   - Dashboard deve mostrar dados
   - Menu "Membros" deve listar membros
   - Criar/editar/apagar membros deve funcionar

---

## 🔍 Verificações Adicionais

### Se os seeders não existirem ou estiverem vazios:

```bash
cd /workspaces/ClubManager/backend
php artisan make:seeder DatabaseSeeder
```

Depois editar `database/seeders/DatabaseSeeder.php` com dados mínimos:

```php
public function run(): void
{
    // 1. Criar clube
    $club = \App\Models\Club::create([
        'nome' => 'BSCN',
        'nome_completo' => 'Barrosense Sport Clube Natação',
        'ativo' => true,
    ]);

    // 2. Criar admin
    $user = \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@admin.pt',
        'password' => bcrypt('password'),
    ]);

    // 3. Associar ao clube
    \App\Models\ClubUser::create([
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => 'admin',
    ]);

    // 4. Criar roles (Spatie)
    \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
    \Spatie\Permission\Models\Role::create(['name' => 'admin']);
    
    $user->assignRole('super-admin');

    echo "✅ Dados base criados!\n";
}
```

---

## 🎯 Resultado Esperado

Após executar estes passos, deves ter:

1. ✅ Sistema acessível em http://localhost:5173
2. ✅ Login funcional com admin@admin.pt
3. ✅ Dashboard com dados (mesmo que mock)
4. ✅ Módulo Membros 100% funcional:
   - Listar membros
   - Criar novo membro
   - Editar membro
   - Ver detalhes
   - Apagar membro
   - Filtros e pesquisa

---

## 📊 Estado dos Módulos (Após este passo)

| Módulo | Status |
|--------|--------|
| **Auth** | ✅ Funcional |
| **Dashboard** | ✅ Funcional (dados mock) |
| **Membros** | ✅ 100% Funcional |
| **Financeiro** | 🟡 API OK, Frontend básico |
| **Desportivo** | 🟡 API OK, Frontend pendente |
| **Eventos** | 🟡 API OK, Frontend pendente |

---

## 🚨 Se algo falhar

### Erro: "SQLSTATE[HY000]: General error: 1 no such table"
```bash
cd /workspaces/ClubManager/backend
php artisan migrate:fresh --seed
```

### Erro: "Class 'DatabaseSeeder' not found"
```bash
composer dump-autoload
php artisan db:seed
```

### Erro 401 no frontend ao fazer requests
- Verificar se o token está a ser guardado no localStorage
- Abrir DevTools → Application → Local Storage
- Deve existir chave `token` ou similar

### Frontend não conecta ao backend
Verificar `frontend/src/lib/api.ts`:
```typescript
const api = axios.create({
  baseURL: 'http://localhost:8000', // Deve apontar para o backend
  headers: {
    'Content-Type': 'application/json',
  },
});
```

---

## 📝 Checklist Rápido

- [ ] `cd backend && php artisan db:seed`
- [ ] Verificar dados criados (Users > 0, Clubs > 0)
- [ ] `composer dev` OU iniciar backend+frontend separados
- [ ] Abrir http://localhost:5173
- [ ] Login com admin@admin.pt / password
- [ ] Navegar para Membros
- [ ] Criar um membro novo
- [ ] Editar um membro existente
- [ ] Sistema funcional! 🎉

---

## 🎯 Próximo Passo (Depois de funcional)

**Objetivo:** Completar frontend dos módulos principais

**Prioridade Alta:**
1. **Dashboard com dados reais** - Conectar aos endpoints da API
2. **Módulo Financeiro** - Interface de faturas e conta corrente
3. **Módulo Desportivo** - Gestão de atletas e treinos
4. **Módulo Eventos** - Calendário e gestão de eventos

**Documentação de referência:**
- `docs/ESTADO_ATUAL_DO_SISTEMA.md` - Estado completo
- `docs/FASE_5_CONCLUIDA.md` - Testes e monitoring
- `docs/FASE_6_CONCLUIDA.md` - DevOps e deploy
- `README.md` - Setup e comandos

---

**Tempo estimado para sistema funcional:** 5-10 minutos  
**Próxima milestone:** Frontend completo dos módulos principais (FASE 7)
