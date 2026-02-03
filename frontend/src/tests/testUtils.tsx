import { render } from '@testing-library/react';
import { vi } from 'vitest';
import { BrowserRouter } from 'react-router-dom';
import axios from 'axios';

// Mock axios
vi.mock('axios');
const mockedAxios = vi.mocked(axios, true);

// Helper para renderizar com router
export const renderWithRouter = (component: React.ReactElement) => {
  return render(<BrowserRouter>{component}</BrowserRouter>);
};

// Mock de dados de teste
export const mockMembro = {
  id: 1,
  numero_socio: 'M001',
  estado: 'ativo',
  data_inicio: '2024-01-01',
  user: {
    id: 1,
    name: 'João Silva',
    email: 'joao@example.com',
  },
};

export const mockFatura = {
  id: 1,
  numero_fatura: 'F2024001',
  data_emissao: '2024-01-01',
  data_vencimento: '2024-01-31',
  mes: '2024-01',
  valor_total: 100.0,
  valor_pago: 0.0,
  saldo: 100.0,
  status_cache: 'pendente',
  membro: mockMembro,
};

export const mockUser = {
  id: 1,
  name: 'Admin User',
  email: 'admin@example.com',
  club_id: 1,
  roles: ['admin'],
  permissions: ['membros.view', 'financeiro.view'],
};

// Mock de resposta API paginada
export const mockPaginatedResponse = <T,>(data: T[]) => ({
  data,
  links: {
    first: 'http://api.test?page=1',
    last: 'http://api.test?page=1',
    prev: null,
    next: null,
  },
  meta: {
    current_page: 1,
    from: 1,
    last_page: 1,
    per_page: 15,
    to: data.length,
    total: data.length,
  },
});

// Helper para simular delay de API
export const delay = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

// Mock de localStorage
export const mockLocalStorage = () => {
  let store: Record<string, string> = {};

  return {
    getItem: vi.fn((key: string) => store[key] || null),
    setItem: vi.fn((key: string, value: string) => {
      store[key] = value.toString();
    }),
    removeItem: vi.fn((key: string) => {
      delete store[key];
    }),
    clear: vi.fn(() => {
      store = {};
    }),
  };
};

// Setup de API mock com token
export const setupAuthenticatedAPI = () => {
  const token = 'mock-token-123';
  localStorage.setItem('auth_token', token);
  
  mockedAxios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  
  return token;
};

// Mock de erro API
export const mockAPIError = (status: number, message: string) => ({
  response: {
    status,
    data: {
      message,
      errors: {},
    },
  },
});
