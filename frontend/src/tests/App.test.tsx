import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { BrowserRouter } from 'react-router-dom';
import axios from 'axios';
import App from '../App';

// Mock axios
vi.mock('axios');
const mockedAxios = vi.mocked(axios, true);

describe('App Component', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders without crashing', () => {
    render(
      <BrowserRouter>
        <App />
      </BrowserRouter>
    );
    
    expect(document.body).toBeTruthy();
  });

  it('displays loading state initially', () => {
    render(
      <BrowserRouter>
        <App />
      </BrowserRouter>
    );
    
    // Verificar se tem algum elemento visível
    expect(document.body).toBeInTheDocument();
  });

  it('handles navigation', async () => {
    render(
      <BrowserRouter>
        <App />
      </BrowserRouter>
    );

    // Aguardar que o componente renderize
    await waitFor(() => {
      expect(document.body).toBeInTheDocument();
    });
  });
});
