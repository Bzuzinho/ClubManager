# Arquitectura e Lógica – Versão Laravel + React

Este documento descreve a nova arquitectura do projecto **ClubManager** baseada numa separação clara entre **backend** e **frontend**. O backend foi reescrito em Laravel para permitir uma API robusta e escalável, enquanto o frontend mantém React + Vite para a interface de utilizador. As secções abaixo sintetizam os componentes tecnológicos, estrutura de pastas, bases de dados, rotas e pontos críticos da implementação actual.

## Estrutura do repositório

```
ClubManager-main/
├── backend/       # Aplicação Laravel (API e base de dados)
└── frontend/      # Aplicação React + Vite (SPA)
```

### Backend (Laravel 12)

O backend é uma aplicação **Laravel 12** que fornece endpoints REST via `routes/api.php` e utiliza **Laravel Sanctum** para autenticação por token (JWT/Cookie). Os principais elementos:

| Componente | Descrição |
| --- | --- |
| **composer.json** | Define o projecto Laravel, requer PHP ^8.2, `laravel/framework` ^12.0 e inclui pacotes como `laravel/sanctum` para gestão de tokens【575875427089611†L7-L13】. |
| **Migrations** | A pasta `database/migrations` contém migrações que criam a tabela `users` e outras tabelas de suporte (jobs, personal_access_tokens), bem como uma migração extra que adiciona um campo `role` aos utilizadores. |
| **Seeders** | `AdminUserSeeder` cria um utilizador administrador por defeito para iniciar o sistema. |
| **Models** | O modelo `User` (Eloquent) representa os membros/administradores no sistema; inclui atributos para nome, email, password e papel. |
| **Controllers** | `Api/AuthController.php` contém o endpoint de login (ex.: `POST /api/login`) que devolve um token de acesso após validação. Outros controladores ainda não foram implementados. |
| **Sanctum** | Configurado através da migration `create_personal_access_tokens_table.php` e do pacote `laravel/sanctum`; o middleware protege as rotas API. |
| **Artisan Scripts** | `composer.json` define scripts para `setup`, `dev` e `test`. O script `dev` usa `concurrently` para correr o servidor Laravel, o listener de filas e o Vite em paralelo【575875427089611†L34-L49】. |

**Base de dados:** o Laravel utiliza Eloquent ORM e migrations para criar uma base relacional (por padrão SQLite ou MySQL). Neste estágio inicial, apenas a tabela `users` está definida. Os campos esperados incluem `id`, `name`, `email`, `password` e `role`. O `Seeder` preenche a tabela com um administrador.

### Frontend (React + Vite)

O frontend foi reestruturado para consumir a API Laravel. Utiliza **React 19**, **TypeScript** e **Vite**. Principais componentes:

| Componente | Descrição |
| --- | --- |
| **package.json** | Lista as dependências de produção: `axios`, `lucide-react`, `react`, `react-router-dom` e `react-dom`【726157665345239†L11-L17】. Inclui scripts para `dev`, `build`, `lint` e `preview`【726157665345239†L5-L10】. |
| **api.ts** | Configura o cliente Axios com `baseURL` retirado das variáveis de ambiente e adiciona o token de autenticação (Bearer) armazenado no `localStorage` aos cabeçalhos de cada requisição【364525479893686†L0-L10】. |
| **router/index.tsx** | Define as rotas da aplicação. O componente `RequireAuth` envolve as rotas protegidas; o layout principal é `AppLayout`. As rotas incluem: `/login`, `/` (dashboard), `/membros`, `/desportivo`, `/eventos` e `/financeiro`【520954156553719†L13-L31】. |
| **Layouts** | `AppLayout` define a estrutura base com barra lateral (`Sidebar`) e topo (`TopBar`); `DashboardLayout` é utilizado para vistas internas. |
| **Módulos** | Existem componentes React para `Members`, `Sports`, `Events` e `Financial` dentro da pasta `modules`. Actualmente são **stubs**: mostram cartões base e texto a indicar que CRUDs reais serão implementados posteriormente【387989765185654†L1-L23】【566649306671744†L4-L11】. |
| **Views** | `views/Login.tsx` permite login através da API; `views/Dashboard.tsx` apresenta um ecrã inicial ainda em construção. |

### Interacção entre frontend e backend

1. **Autenticação** – O utilizador submete o formulário de login; o `AuthController` no backend valida as credenciais e devolve um token Sanctum. Este token é armazenado no `localStorage` do navegador e anexado às requisições subsequentes via Axios【364525479893686†L6-L10】.
2. **Protecção de rotas** – O componente `RequireAuth` verifica a existência do token antes de permitir acesso às rotas internas. Caso não exista, o utilizador é redireccionado para `/login`【520954156553719†L20-L24】.
3. **Navegação** – Após autenticação, o utilizador tem acesso às secções principais (**Membros**, **Desportivo**, **Eventos** e **Financeiro**). Os componentes actualmente apresentam informação placeholder; a integração com a API será desenvolvida em fases futuras【387989765185654†L7-L12】.

## Diferenças face à versão Spark

Na versão anterior, todo o estado e persistência eram geridos no lado do cliente através do Spark KV. Nesta nova arquitectura:

- A persistência de dados passa a ser feita num **servidor Laravel** com base de dados relacional. Isto permite escalabilidade e segurança acrescida.
- A camada de autenticação utiliza tokens Sanctum em vez de perfis geridos localmente.
- Os módulos de frontend (membros, eventos, desportivo e financeiro) continuam a ser desenvolvidos em React, mas consumirão endpoints REST para CRUDs, em vez de manipular diretamente o armazenamento local.

## Considerações finais

Esta migração para Laravel é uma fundação para funcionalidades futuras. Embora muitos módulos ainda estejam em formato de **stub**, a separação de responsabilidades permitirá:

1. **Desenvolver APIs robustas** para gestão de membros, eventos, treinos, faturas e relatórios.
2. **Garantir segurança** através de autenticação e autorização no backend.
3. **Escalar o sistema** adicionando filas (Queue), jobs e migrações adicionais conforme necessário.
4. **Manter o frontend modular**, beneficiando de React Router e Vite para desenvolvimento rápido e experiência de utilizador fluida.

As próximas fases deverão incluir a definição de modelos adicionais (Fatura, Movimento, Evento, Presença, Convocação, etc.), criação de controladores REST correspondentes, configuração de políticas/permissions e integração completa com os componentes React.