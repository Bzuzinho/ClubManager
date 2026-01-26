# Guia de Testes e Monitoring - Frontend

Este guia explica como usar a infraestrutura de testes e monitoring implementada na FASE 5.

## 📋 Índice

1. [Testes Unitários e de Integração](#testes-unitários-e-de-integração)
2. [Testes End-to-End (E2E)](#testes-end-to-end-e2e)
3. [Error Tracking com Sentry](#error-tracking-com-sentry)
4. [Logging Estruturado](#logging-estruturado)
5. [Performance Monitoring](#performance-monitoring)
6. [Monitoring Dashboard](#monitoring-dashboard)

---

## Testes Unitários e de Integração

### Executar Testes

```bash
# Modo watch (desenvolvimento)
npm run test

# Executar uma vez (CI)
npm run test:ci

# Com interface UI
npm run test:ui

# Com coverage report
npm run test:coverage
```

### Criar Novo Teste

**1. Criar arquivo de teste:**
```typescript
// src/tests/components/MyComponent.test.tsx
import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { MyComponent } from '../../components/MyComponent';

describe('MyComponent', () => {
  it('should render correctly', () => {
    render(<MyComponent />);
    expect(screen.getByText('Hello')).toBeInTheDocument();
  });
});
```

**2. Usar test utilities:**
```typescript
import { renderWithRouter, mockMembro, setupAuthenticatedAPI } from '../testUtils';

describe('MembrosPage', () => {
  it('should fetch and display membros', async () => {
    const mockData = [mockMembro(), mockMembro()];
    setupAuthenticatedAPI('/membros', { data: mockData });
    
    renderWithRouter(<MembrosPage />);
    
    // Assertions...
  });
});
```

### Test Utilities Disponíveis

```typescript
// Renderizar com Router
renderWithRouter(<Component />, { route: '/membros' });

// Mock data
const membro = mockMembro({ nome: 'João', status: 'ativo' });
const fatura = mockFatura({ valor: 50.00, status: 'pendente' });
const user = mockUser({ email: 'test@example.com' });

// Mock API response
mockPaginatedResponse('/membros', [mockMembro()]);
setupAuthenticatedAPI('/membros', { data: [] });
mockAPIError('/membros', 404, 'Not found');

// Mock localStorage
mockLocalStorage({ token: 'abc123' });

// Delay helper
await delay(1000); // Espera 1 segundo
```

---

## Testes End-to-End (E2E)

### Executar Testes E2E

```bash
# Headless (padrão)
npm run test:e2e

# Com UI interativa
npm run test:e2e:ui

# Com browser visível
npm run test:e2e:headed

# Debug mode
npm run test:e2e:debug
```

### Criar Novo Teste E2E

**1. Criar spec file:**
```typescript
// e2e/myfeature.spec.ts
import { test, expect } from '@playwright/test';

test.describe('My Feature', () => {
  test.beforeEach(async ({ page }) => {
    // Login ou setup inicial
    await page.goto('/login');
    // ...
  });
  
  test('should do something', async ({ page }) => {
    await page.goto('/my-feature');
    await expect(page.locator('h1')).toHaveText('My Feature');
  });
});
```

**2. Usar fixtures e helpers:**
```typescript
test('should login successfully', async ({ page }) => {
  await page.goto('/login');
  await page.fill('[name="email"]', 'admin@example.com');
  await page.fill('[name="password"]', 'password123');
  await page.click('button[type="submit"]');
  
  await expect(page).toHaveURL('/dashboard');
});
```

### Debugging E2E Tests

```bash
# Modo debug com Playwright Inspector
npm run test:e2e:debug

# Gerar trace file
npx playwright test --trace on

# Ver trace file
npx playwright show-trace trace.zip
```

---

## Error Tracking com Sentry

### Inicializar Sentry

**No main.tsx ou App.tsx:**
```typescript
import { initSentry, ErrorBoundary } from './lib/sentry';

// Inicializar no início da aplicação
initSentry(import.meta.env.PROD ? 'production' : 'development');

// Envolver app com ErrorBoundary
function App() {
  return (
    <ErrorBoundary>
      <YourApp />
    </ErrorBoundary>
  );
}
```

### Capturar Erros Manualmente

```typescript
import { captureException, captureMessage, addBreadcrumb } from './lib/sentry';

try {
  // Código que pode falhar
  await riskyOperation();
} catch (error) {
  // Capturar exceção com contexto
  captureException(error, {
    operation: 'riskyOperation',
    userId: user.id,
  });
}

// Mensagem informativa
captureMessage('User performed important action', 'info');

// Breadcrumb para tracking
addBreadcrumb('User clicked button X', 'user-action', 'info');
```

### Associar Utilizador

```typescript
import { setUser } from './lib/sentry';

// Após login
setUser({
  id: user.id,
  email: user.email,
  username: user.name,
});

// Após logout
setUser(null);
```

### Configurar DSN

**Criar arquivo `.env`:**
```env
VITE_SENTRY_DSN=https://your-sentry-dsn@sentry.io/project
```

---

## Logging Estruturado

### Usar Logger

```typescript
import { logger } from './lib/logger';

// Diferentes níveis
logger.debug('Detailed debug info', { data: complexObject });
logger.info('Something happened', { event: 'user-login' });
logger.warn('Potential issue detected', { metric: value });
logger.error('Error occurred', error, { context: 'payment' });

// Performance logging
logger.performance('Page rendered', {
  renderTime: '250ms',
  components: 15
});

// API request logging (automático no api.ts)
logger.apiRequest('GET', '/api/membros', 200, 150);
```

### Configuração por Ambiente

```typescript
// Development: Todos os logs no console
// Production: Apenas warn e error

// Forçar nível de log
if (import.meta.env.DEV) {
  logger.debug('Development only log');
}
```

### Integração com Serviços Externos

```typescript
// Enviar logs para serviço externo (ex: Datadog, LogRocket)
logger.toExternalService('error', 'Critical error', data, error);
```

---

## Performance Monitoring

### Inicializar Monitoring

**No main.tsx:**
```typescript
import { initPerformanceMonitoring } from './lib/performance';

// Inicializar no load
initPerformanceMonitoring();
```

### Usar Performance Marks

```typescript
import { performanceMark } from './lib/performance';

// Início da operação
performanceMark.start('data-fetch');

// ... operação ...
await fetchData();

// Fim da operação (retorna duration em ms)
const duration = performanceMark.end('data-fetch');

// Limpar marks
performanceMark.clear('data-fetch');
```

### Tracking Custom Metrics

```typescript
import { trackCustomMetric, trackMemoryUsage } from './lib/performance';

// Métrica customizada
trackCustomMetric('images-loaded', 25, 'count');
trackCustomMetric('bundle-size', 350, 'KB');

// Memory usage
trackMemoryUsage(); // Log no console
```

### Web Vitals

As Web Vitals são rastreadas automaticamente após `initPerformanceMonitoring()`:

- **LCP** (Largest Contentful Paint)
- **FID** (First Input Delay)
- **CLS** (Cumulative Layout Shift)

Logs aparecem automaticamente no console e são enviados para Sentry.

### Observers

```typescript
import { observeResources, observeLongTasks } from './lib/performance';

// Observar recursos lentos (> 1s)
const resourceObserver = observeResources();

// Detectar tarefas longas (> 50ms)
const taskObserver = observeLongTasks();

// Cleanup
resourceObserver?.disconnect();
taskObserver?.disconnect();
```

---

## Monitoring Dashboard

### Ativar Dashboard

O dashboard aparece automaticamente em **modo development**.

```typescript
import { MonitoringDashboard } from './components/MonitoringDashboard';

function App() {
  return (
    <>
      <YourApp />
      <MonitoringDashboard /> {/* Apenas em dev */}
    </>
  );
}
```

### Funcionalidades

- **API Status:** Healthy/Degraded/Down
- **Contadores:** Errors e Warnings
- **Memory Usage:** Used, Total, Limit
- **Ações:**
  - Track Memory: Atualiza métricas
  - Clear Counters: Reseta contadores

### Esconder Dashboard

Clicar no **✕** no canto superior direito.

---

## Best Practices

### Testes

✅ **DO:**
- Testar comportamento, não implementação
- Usar `data-testid` para elementos dinâmicos
- Mock API calls com dados realistas
- Testar casos de erro e loading states
- Agrupar testes com `describe`

❌ **DON'T:**
- Testar detalhes de implementação (state interno)
- Duplicar testes (test same thing multiple times)
- Usar `setTimeout` (usar `waitFor` do Testing Library)
- Mockar tudo (testar integração quando possível)

### Error Tracking

✅ **DO:**
- Capturar erros críticos com contexto
- Adicionar breadcrumbs em operações importantes
- Associar user após login
- Filtrar erros conhecidos/esperados

❌ **DON'T:**
- Capturar erros triviais (404 esperados)
- Enviar dados sensíveis (passwords, tokens)
- Logar tudo (gera ruído)

### Performance

✅ **DO:**
- Marcar operações críticas (data fetching, rendering)
- Monitorar Web Vitals
- Track custom metrics relevantes
- Observer recursos lentos

❌ **DON'T:**
- Marcar operações triviais
- Criar marks sem fazer cleanup
- Logar performance em production (usar Sentry)

---

## Troubleshooting

### Testes falhando

**Problema:** `ReferenceError: localStorage is not defined`
```typescript
// Adicionar no test file
import '../testUtils';
mockLocalStorage({});
```

**Problema:** `window.matchMedia is not a function`
```typescript
// Já está no setup.ts, garantir que está sendo importado
```

### E2E Tests timeout

**Solução:**
```typescript
// Aumentar timeout no test
test('long operation', async ({ page }) => {
  test.setTimeout(60000); // 60 segundos
  // ...
});
```

### Sentry não capturando erros

**Verificar:**
1. DSN configurado em `.env`
2. `initSentry()` chamado no início
3. Ambiente correto (production/staging)
4. Erro não está filtrado (beforeSend)

### Performance marks não funcionam

**Verificar:**
```typescript
// Browser suporta Performance API?
if ('performance' in window) {
  performanceMark.start('operation');
}
```

---

## CI/CD Integration

Os testes são executados automaticamente no GitHub Actions (`.github/workflows/frontend-ci.yml`):

```yaml
- name: Run unit tests
  run: npm run test:ci

- name: Run E2E tests
  run: npm run test:e2e
```

**Coverage reports** são gerados e podem ser enviados para:
- Codecov
- Coveralls
- SonarQube

---

## Recursos Adicionais

- [Vitest Documentation](https://vitest.dev/)
- [Testing Library](https://testing-library.com/react)
- [Playwright Documentation](https://playwright.dev/)
- [Sentry React Documentation](https://docs.sentry.io/platforms/javascript/guides/react/)
- [Web Vitals](https://web.dev/vitals/)

---

**Dúvidas?** Consulte [FASE_5_CONCLUIDA.md](../FASE_5_CONCLUIDA.md) para mais detalhes técnicos.
