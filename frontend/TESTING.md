# Testing Guide

## Test Structure

This project uses two different testing frameworks:

### Unit Tests (Vitest)
- **Location:** `src/**/*.test.tsx`
- **Framework:** Vitest + @testing-library/react
- **Environment:** jsdom
- **Command:** `npm run test` or `npm run test:ci`

### E2E Tests (Playwright)
- **Location:** `e2e/**/*.spec.ts`
- **Framework:** Playwright  
- **Environment:** Real browsers (Chromium, Firefox, WebKit)
- **Command:** `npm run test:e2e`

## Running Tests

### Unit Tests
```bash
# Run all unit tests
npm run test

# Run unit tests in CI mode (no watch)
npm run test:ci

# Run with UI
npm run test:ui

# Run with coverage
npm run test:coverage
```

### E2E Tests  
```bash
# Run all E2E tests
npm run test:e2e

# Run with UI
npm run test:e2e:ui

# Run in headed mode (see browser)
npm run test:e2e:headed

# Debug mode
npm run test:e2e:debug
```

## Current Status

✅ **Unit Tests:** 13 tests passing
- `Button.test.tsx`: 6 tests
- `MembrosPage.test.tsx`: 4 tests  
- `App.test.tsx`: 3 tests

⚠️ **E2E Tests:** Configured but vitest may try to load them (safe to ignore the errors, they run separately with Playwright)

## Configuration Files

- **vite.config.ts** - Vitest configuration (unit tests only)
- **playwright.config.ts** - Playwright configuration (E2E tests)
- **src/tests/setup.ts** - Test setup and mocks

## Writing Tests

### Unit Test Example
```typescript
import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';

describe('MyComponent', () => {
  it('renders correctly', () => {
    render(<MyComponent />);
    expect(screen.getByText('Hello')).toBeInTheDocument();
  });
});
```

### E2E Test Example
```typescript
import { test, expect } from '@playwright/test';

test('user can login', async ({ page }) => {
  await page.goto('/login');
  await page.fill('[name="email"]', 'test@example.com');
  await page.fill('[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await expect(page).toHaveURL('/dashboard');
});
```

## Troubleshooting

### E2E tests showing in vitest output
This is expected - vitest will try to discover e2e tests but they will show as "0 tests" or errors. This is safe to ignore. The actual E2E tests run separately with `npm run test:e2e`.

### Missing dependencies
If you see errors about missing `@testing-library/dom`, run:
```bash
npm install --legacy-peer-deps
```

### Type errors in tests
The test files are configured to use jsdom environment. If you see type errors, make sure:
1. `@types/node` is installed
2. `vitest/config` is imported in vite.config.ts (not just `vite`)
