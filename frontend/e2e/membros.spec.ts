import { test, expect, Page } from '@playwright/test';

// Helper para autenticação
async function authenticate(page: Page) {
  await page.evaluate(() => {
    localStorage.setItem('auth_token', 'mock-token-123');
    localStorage.setItem('user', JSON.stringify({
      id: 1,
      name: 'Admin User',
      email: 'admin@example.com',
      roles: ['admin'],
    }));
  });
}

test.describe('Membros Management', () => {
  test.beforeEach(async ({ page }) => {
    await authenticate(page);
    
    // Mock API response
    await page.route('**/api/v2/membros*', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: 1,
              numero_socio: 'M001',
              estado: 'ativo',
              user: {
                id: 1,
                name: 'João Silva',
                email: 'joao@example.com',
              },
            },
            {
              id: 2,
              numero_socio: 'M002',
              estado: 'inativo',
              user: {
                id: 2,
                name: 'Maria Santos',
                email: 'maria@example.com',
              },
            },
          ],
          meta: {
            current_page: 1,
            total: 2,
          },
        }),
      });
    });

    await page.goto('/membros');
  });

  test('should display membros list', async ({ page }) => {
    await expect(page.locator('h1')).toContainText(/membros/i);
    await expect(page.locator('text=M001')).toBeVisible();
    await expect(page.locator('text=João Silva')).toBeVisible();
    await expect(page.locator('text=M002')).toBeVisible();
  });

  test('should filter membros by status', async ({ page }) => {
    await page.locator('select[name="estado"]').selectOption('ativo');
    
    // Mock filtered response
    await page.route('**/api/v2/membros?estado=ativo', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: 1,
              numero_socio: 'M001',
              estado: 'ativo',
              user: { name: 'João Silva' },
            },
          ],
        }),
      });
    });

    await expect(page.locator('text=M001')).toBeVisible();
    await expect(page.locator('text=M002')).not.toBeVisible();
  });

  test('should search membros by name', async ({ page }) => {
    await page.locator('input[placeholder*="pesquisar"]').fill('João');
    await page.locator('button[type="submit"]').click();

    await expect(page.locator('text=João Silva')).toBeVisible();
  });

  test('should open create membro modal', async ({ page }) => {
    await page.locator('button:has-text("Novo Membro")').click();
    
    await expect(page.locator('role=dialog')).toBeVisible();
    await expect(page.locator('input[name="nome"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });

  test('should create new membro', async ({ page }) => {
    await page.locator('button:has-text("Novo Membro")').click();
    
    // Mock create API
    await page.route('**/api/v2/membros', async route => {
      if (route.request().method() === 'POST') {
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({
            data: {
              id: 3,
              numero_socio: 'M003',
              user: {
                name: 'Novo Membro',
                email: 'novo@example.com',
              },
            },
          }),
        });
      }
    });

    await page.locator('input[name="nome"]').fill('Novo Membro');
    await page.locator('input[name="email"]').fill('novo@example.com');
    await page.locator('button[type="submit"]:has-text("Criar")').click();

    await expect(page.locator('text=/criado.*sucesso/i')).toBeVisible();
  });

  test('should view membro details', async ({ page }) => {
    await page.locator('text=M001').click();
    
    await expect(page).toHaveURL(/.*membros\/1/);
    await expect(page.locator('h1')).toContainText('João Silva');
  });

  test('should edit membro', async ({ page }) => {
    await page.locator('[data-testid="edit-membro-1"]').click();
    
    await page.locator('input[name="nome"]').fill('João Silva Updated');
    await page.locator('button[type="submit"]:has-text("Guardar")').click();

    await expect(page.locator('text=/atualizado.*sucesso/i')).toBeVisible();
  });

  test('should delete membro with confirmation', async ({ page }) => {
    page.on('dialog', dialog => dialog.accept());
    
    await page.locator('[data-testid="delete-membro-1"]').click();

    await expect(page.locator('text=/eliminado.*sucesso/i')).toBeVisible();
  });

  test('should paginate through membros', async ({ page }) => {
    // Mock page 2
    await page.route('**/api/v2/membros?page=2', async route => {
      await route.fulfill({
        status: 200,
        body: JSON.stringify({
          data: [{ id: 3, numero_socio: 'M003' }],
          meta: { current_page: 2, total: 3 },
        }),
      });
    });

    await page.locator('button:has-text("Próximo")').click();
    
    await expect(page.locator('text=M003')).toBeVisible();
  });
});
