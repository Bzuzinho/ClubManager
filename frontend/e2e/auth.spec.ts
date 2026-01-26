import { test, expect, Page } from '@playwright/test';

test.describe('Authentication Flow', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
  });

  test('should display login form', async ({ page }) => {
    await expect(page.locator('input[type="email"]')).toBeVisible();
    await expect(page.locator('input[type="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  test('should show validation errors for empty fields', async ({ page }) => {
    await page.locator('button[type="submit"]').click();
    
    await expect(page.locator('text=/email.*required/i')).toBeVisible();
    await expect(page.locator('text=/password.*required/i')).toBeVisible();
  });

  test('should show error for invalid credentials', async ({ page }) => {
    await page.locator('input[type="email"]').fill('invalid@example.com');
    await page.locator('input[type="password"]').fill('wrongpassword');
    await page.locator('button[type="submit"]').click();

    await expect(page.locator('text=/invalid.*credentials/i')).toBeVisible();
  });

  test('should login successfully with valid credentials', async ({ page }) => {
    // Mock API response
    await page.route('**/api/login', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          token: 'mock-token-123',
          user: {
            id: 1,
            name: 'Admin User',
            email: 'admin@example.com',
          },
        }),
      });
    });

    await page.locator('input[type="email"]').fill('admin@example.com');
    await page.locator('input[type="password"]').fill('password123');
    await page.locator('button[type="submit"]').click();

    // Verificar redirecionamento para dashboard
    await expect(page).toHaveURL(/.*dashboard/);
  });

  test('should logout successfully', async ({ page }) => {
    // Setup authenticated state
    await page.evaluate(() => {
      localStorage.setItem('auth_token', 'mock-token-123');
      localStorage.setItem('user', JSON.stringify({
        id: 1,
        name: 'Admin User',
      }));
    });

    await page.goto('/dashboard');
    
    // Click logout button
    await page.locator('[data-testid="logout-button"]').click();
    
    // Verificar redirecionamento para login
    await expect(page).toHaveURL(/.*login/);
    
    // Verificar token removido
    const token = await page.evaluate(() => localStorage.getItem('auth_token'));
    expect(token).toBeNull();
  });
});
