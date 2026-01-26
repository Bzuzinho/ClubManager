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

test.describe('Financial Module', () => {
  test.beforeEach(async ({ page }) => {
    await authenticate(page);

    // Mock faturas list API
    await page.route('**/api/v2/faturas*', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: 1,
              numero: 'FAT-2025-001',
              membro_id: 1,
              membro: {
                id: 1,
                numero_socio: 'M001',
                user: {
                  name: 'João Silva',
                  email: 'joao@example.com',
                },
              },
              mes: '2025-01',
              data_emissao: '2025-01-01',
              data_vencimento: '2025-01-31',
              status_cache: 'pendente',
              valor_total: 50.0,
              valor_pago: 0.0,
              valor_pendente: 50.0,
              itens: [
                {
                  id: 1,
                  fatura_id: 1,
                  descricao: 'Mensalidade Janeiro 2025',
                  tipo: 'mensalidade',
                  valor: 50.0,
                },
              ],
              pagamentos: [],
            },
            {
              id: 2,
              numero: 'FAT-2025-002',
              membro_id: 2,
              membro: {
                id: 2,
                numero_socio: 'M002',
                user: {
                  name: 'Maria Santos',
                  email: 'maria@example.com',
                },
              },
              mes: '2025-01',
              data_emissao: '2025-01-01',
              data_vencimento: '2025-01-31',
              status_cache: 'paga',
              valor_total: 50.0,
              valor_pago: 50.0,
              valor_pendente: 0.0,
              itens: [],
              pagamentos: [
                {
                  id: 1,
                  fatura_id: 2,
                  data: '2025-01-05',
                  valor: 50.0,
                  metodo: 'mb',
                },
              ],
            },
          ],
          meta: {
            current_page: 1,
            total: 2,
            per_page: 15,
            last_page: 1,
          },
        }),
      });
    });

    // Mock membros API
    await page.route('**/api/v2/membros*', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: 1,
              numero_socio: 'M001',
              user: { name: 'João Silva', email: 'joao@example.com' },
            },
            {
              id: 2,
              numero_socio: 'M002',
              user: { name: 'Maria Santos', email: 'maria@example.com' },
            },
          ],
        }),
      });
    });

    await page.goto('/financeiro');
  });

  test('should display faturas list', async ({ page }) => {
    await expect(page.locator('h1')).toContainText(/financeiro/i);
    await expect(page.locator('text=FAT-2025-001')).toBeVisible();
    await expect(page.locator('text=FAT-2025-002')).toBeVisible();
    await expect(page.locator('text=João Silva')).toBeVisible();
  });

  test('should filter faturas by status', async ({ page }) => {
    await page.locator('select[value=""]').first().selectOption('pendente');

    // Mock filtered response
    await page.route('**/api/v2/faturas?*estado=pendente*', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            {
              id: 1,
              numero: 'FAT-2025-001',
              status_cache: 'pendente',
              membro: {
                numero_socio: 'M001',
                user: { name: 'João Silva' },
              },
            },
          ],
          meta: { current_page: 1, total: 1, per_page: 15, last_page: 1 },
        }),
      });
    });

    await expect(page.locator('text=FAT-2025-001')).toBeVisible();
  });

  test('should open create fatura modal', async ({ page }) => {
    await page.locator('button:has-text("Criar Fatura")').click();

    await expect(page.locator('h2:has-text("Criar Fatura Avulsa")')).toBeVisible();
    await expect(page.locator('select[required]').first()).toBeVisible(); // Membro select
  });

  test('should create new fatura', async ({ page }) => {
    await page.locator('button:has-text("Criar Fatura")').click();

    // Mock create API
    await page.route('**/api/v2/faturas', async (route) => {
      if (route.request().method() === 'POST') {
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({
            data: {
              id: 3,
              numero: 'FAT-2025-003',
              membro_id: 1,
            },
          }),
        });
      }
    });

    // Fill form
    await page.locator('select[required]').first().selectOption('1'); // Membro
    await page.locator('input[type="month"]').fill('2025-02');
    await page.locator('input[placeholder="Descrição"]').fill('Taxa de Inscrição');
    await page.locator('input[placeholder="Valor"]').fill('25.00');

    // Submit
    await page.locator('button[type="submit"]:has-text("Criar Fatura")').click();

    // Wait for success
    page.once('dialog', (dialog) => {
      expect(dialog.message()).toContain('sucesso');
      dialog.accept();
    });
  });

  test('should open gerar mensalidades modal', async ({ page }) => {
    await page.locator('button:has-text("Gerar Mensalidades")').click();

    await expect(page.locator('h2:has-text("Gerar Faturas de Mensalidade")')).toBeVisible();
  });

  test('should generate mensalidades', async ({ page }) => {
    await page.locator('button:has-text("Gerar Mensalidades")').click();

    // Mock gerar mensalidades API
    await page.route('**/api/v2/faturas/gerar-mensalidades', async (route) => {
      await route.fulfill({
        status: 201,
        contentType: 'application/json',
        body: JSON.stringify({
          message: '1 faturas geradas com sucesso',
          data: [],
        }),
      });
    });

    // Fill form
    await page.locator('select[required]').first().selectOption('1');
    await page.locator('input[type="month"]').first().fill('2025-02');

    // Submit
    await page.locator('button[type="submit"]:has-text("Gerar Faturas")').click();

    // Wait for success
    page.once('dialog', (dialog) => {
      expect(dialog.message()).toContain('faturas geradas');
      dialog.accept();
    });
  });

  test('should view fatura details', async ({ page }) => {
    // Click on first fatura row
    await page.locator('tr:has-text("FAT-2025-001")').click();

    await expect(page.locator('h2:has-text("Fatura FAT-2025-001")')).toBeVisible();
    await expect(page.locator('text=João Silva')).toBeVisible();
    await expect(page.locator('text=Mensalidade Janeiro 2025')).toBeVisible();
  });

  test('should register payment', async ({ page }) => {
    // Click on first fatura
    await page.locator('tr:has-text("FAT-2025-001")').click();

    // Click register payment button
    await page.locator('button:has-text("Registar Pagamento")').click();

    // Mock registar pagamento API
    await page.route('**/api/v2/faturas/*/pagamentos', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          message: 'Pagamento registado com sucesso',
          data: {},
        }),
      });
    });

    // Fill payment form
    await page.locator('input[type="date"]').first().fill('2025-01-15');
    await page.locator('input[type="number"][step="0.01"]').fill('50.00');
    await page.locator('select').last().selectOption('mb');

    // Submit
    await page.locator('button[type="submit"]:has-text("Registar")').click();

    // Wait for success
    page.once('dialog', (dialog) => {
      expect(dialog.message()).toContain('sucesso');
      dialog.accept();
    });
  });

  test('should add item to fatura', async ({ page }) => {
    // Click on first fatura
    await page.locator('tr:has-text("FAT-2025-001")').click();

    // Click add item button
    await page.locator('button:has-text("Adicionar Item")').first().click();

    // Mock adicionar item API
    await page.route('**/api/v2/faturas/*/itens', async (route) => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          message: 'Item adicionado com sucesso',
          data: {},
        }),
      });
    });

    // Fill item form
    await page.locator('input[placeholder="Descrição"]').fill('Taxa de Material');
    await page.locator('input[placeholder="Valor"]').fill('15.00');

    // Submit
    await page.locator('button[type="submit"]:has-text("Adicionar")').click();

    // Wait for success
    page.once('dialog', (dialog) => {
      expect(dialog.message()).toContain('sucesso');
      dialog.accept();
    });
  });

  test('should display status badges correctly', async ({ page }) => {
    await expect(page.locator('text=Pendente')).toBeVisible();
    await expect(page.locator('text=Paga')).toBeVisible();
  });

  test('should clear filters', async ({ page }) => {
    await page.locator('select').first().selectOption('pendente');
    await page.locator('input[type="month"]').fill('2025-01');

    await page.locator('button:has-text("Limpar Filtros")').click();

    // Verify filters are cleared
    const estadoSelect = await page.locator('select').first().inputValue();
    const mesInput = await page.locator('input[type="month"]').inputValue();

    expect(estadoSelect).toBe('');
    expect(mesInput).toBe('');
  });
});
