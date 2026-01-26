import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import axios from 'axios';
import { renderWithRouter, mockMembro, mockPaginatedResponse, setupAuthenticatedAPI } from '../tests/testUtils';

// Mock do componente de membros (exemplo - ajustar conforme implementação real)
const MembrosPage = () => {
  const [membros, setMembros] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    axios.get('/api/v2/membros')
      .then(res => {
        setMembros(res.data.data);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  if (loading) return <div>Loading...</div>;

  return (
    <div>
      <h1>Membros</h1>
      <table>
        <tbody>
          {membros.map((membro: any) => (
            <tr key={membro.id}>
              <td>{membro.numero_socio}</td>
              <td>{membro.user.name}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

vi.mock('axios');
const mockedAxios = vi.mocked(axios, true);

describe('MembrosPage', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    setupAuthenticatedAPI();
  });

  it('displays loading state initially', () => {
    mockedAxios.get.mockReturnValue(new Promise(() => {})); // Never resolves
    
    renderWithRouter(<MembrosPage />);
    
    expect(screen.getByText(/loading/i)).toBeInTheDocument();
  });

  it('displays membros list after loading', async () => {
    const membros = [mockMembro];
    mockedAxios.get.mockResolvedValue({
      data: mockPaginatedResponse(membros),
    });

    renderWithRouter(<MembrosPage />);

    await waitFor(() => {
      expect(screen.getByText('Membros')).toBeInTheDocument();
    });

    expect(screen.getByText('M001')).toBeInTheDocument();
    expect(screen.getByText('João Silva')).toBeInTheDocument();
  });

  it('handles API error gracefully', async () => {
    mockedAxios.get.mockRejectedValue(new Error('API Error'));

    renderWithRouter(<MembrosPage />);

    await waitFor(() => {
      expect(screen.queryByText(/loading/i)).not.toBeInTheDocument();
    });
  });

  it('makes correct API call', async () => {
    mockedAxios.get.mockResolvedValue({
      data: mockPaginatedResponse([]),
    });

    renderWithRouter(<MembrosPage />);

    await waitFor(() => {
      expect(mockedAxios.get).toHaveBeenCalledWith('/api/v2/membros');
    });
  });
});
