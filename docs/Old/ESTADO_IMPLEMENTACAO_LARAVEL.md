# Estado de Implementação – Versão Laravel + React

Este documento apresenta o ponto de situação do projecto após a migração para uma arquitectura **Laravel + React**. Destina‑se a registar o que já foi realizado e a orientar as próximas fases de desenvolvimento.

## Funcionalidades implementadas

### Backend

1. **Estrutura Laravel inicial** – O backend foi criado com Laravel 12, configurado para utilizar PHP 8.2 e inclui dependências essenciais (`laravel/framework`, `laravel/sanctum`, `laravel/tinker`)【575875427089611†L7-L13】.
2. **Gestão de pacotes e scripts** – O `composer.json` define scripts para instalação, migrações, execução em desenvolvimento (usando `concurrently` para lançar servidor, filas e Vite) e testes【575875427089611†L34-L49】.
3. **Migrações** – Foram adicionadas migrações para criar a tabela `users`, as tabelas de jobs e de tokens, bem como uma migração que adiciona um campo `role` à tabela de utilizadores (permitindo diferenciar administradores, atletas, etc.).
4. **Seeders** – O seeder `AdminUserSeeder` cria um utilizador administrador por defeito.
5. **Autenticação via Sanctum** – Implementado um controlador de autenticação (`AuthController`) que devolve um token Sanctum aquando do login. As rotas protegidas são sujeitas ao middleware `auth:sanctum`.

### Frontend

1. **Estrutura React + Vite** – A pasta `frontend` foi inicializada com um template de React + TypeScript + Vite. As dependências incluem `react`, `react-dom`, `react-router-dom`, `axios` e `lucide-react`【726157665345239†L11-L17】.
2. **Cliente API** – Foi criado `src/lib/api.ts` que configura `axios` com baseURL da variável de ambiente e injeta o token de autenticação a partir do `localStorage`【364525479893686†L0-L10】.
3. **Roteamento** – Definido um router com as rotas `/login`, `/` (dashboard), `/membros`, `/desportivo`, `/eventos` e `/financeiro`. O componente `RequireAuth` protege as rotas internas【520954156553719†L13-L31】.
4. **Layout** – Implementados `AppLayout`, `DashboardLayout`, `Sidebar` e `TopBar` para fornecer uma estrutura de navegação consistente.
5. **Vistas básicas** – Criadas páginas de login (`Login.tsx`) e dashboard (`Dashboard.tsx`) e quatro módulos base (`Members`, `Sports`, `Events`, `Financial`) que actualmente apresentam cartões e texto placeholder indicando funcionalidades a implementar【387989765185654†L1-L23】【566649306671744†L4-L11】.

## Itens a desenvolver

Apesar da fundação estar estabelecida, a maior parte das funcionalidades ainda não foi implementada. Os próximos passos incluem:

1. **Modelos e migrations adicionais** – Definir tabelas e modelos para entidades-chave: `Event`, `Presence`, `Convocation`, `Result`, `Invoice` (Fatura), `Movement`, `MembershipType`, `CostCenter`, etc. Criar migrações e relacionamentos (Eloquent) entre estas entidades.
2. **Controladores e rotas API** – Implementar controladores REST para cada entidade, usando políticas e middleware para autorizar operações conforme o papel do utilizador.
3. **Autorização e perfis** – Expandir o modelo `User` para incluir perfis (`admin`, `encarregado`, `atleta`, `staff`) e definir políticas de acesso via Gates ou Policies do Laravel.
4. **Integração com o frontend** – Transformar os componentes React de `Members`, `Sports`, `Events` e `Financial` em CRUDs completos que consumam os endpoints da API. Criar formulários de criação/edição e listas com pesquisa e filtros.
5. **Gestão de estado** – Integrar bibliotecas como React Query ou Zustand para gerir o estado global e a cache de dados obtidos da API.
6. **Relatórios e dashboards** – Desenvolver componentes para apresentar KPIs, gráficos e relatórios cruzados (ex.: custos vs resultados desportivos) no dashboard.
7. **Regras de negócio** – Aplicar as validações e lógicas especificadas na documentação original (número de sócio único, validação de escalões, sincronização de presenças, geração automática de movimentos financeiros) ao novo backend.
8. **Design system** – Manter o guia de estilo definido em `GUIAS_GRAFICAS.md`, adaptando os componentes React para usar Tailwind e os tokens visuais já definidos.

## Considerações finais

Nesta fase inicial da migração, o backend Laravel fornece apenas a infra‑estrutura de autenticação e a tabela de utilizadores. O frontend dispõe de roteamento e layouts, mas os módulos apresentam apenas conteúdo estático. A próxima fase deve concentrar‑se na modelação de dados e na criação de APIs para permitir que o frontend realize operações reais. É essencial manter a documentação viva actualizada e seguir as guidelines visuais para garantir coesão entre as equipas de frontend e backend.