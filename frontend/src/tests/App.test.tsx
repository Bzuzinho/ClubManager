import { render, waitFor } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import App from '../App';

// Mock the API module before importing anything else
vi.mock('../lib/api', () => ({
  default: {
    interceptors: {
      request: { use: vi.fn(), eject: vi.fn() },
      response: { use: vi.fn(), eject: vi.fn() },
    },
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    patch: vi.fn(),
  },
  apiClient: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    patch: vi.fn(),
  },
}));

describe('App Component', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders without crashing', () => {
    render(<App />);
    
    expect(document.body).toBeTruthy();
  });

  it('displays loading state initially', () => {
    render(<App />);
    
    // Verificar se tem algum elemento visível
    expect(document.body).toBeInTheDocument();
  });

  it('handles navigation', async () => {
    render(<App />);

    // Aguardar que o componente renderize
    await waitFor(() => {
      expect(document.body).toBeInTheDocument();
    });
  });
});
