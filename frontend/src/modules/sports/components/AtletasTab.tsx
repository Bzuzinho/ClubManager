import { useState, useEffect } from 'react';
import { atletasApi } from '../api';
import type { Atleta } from '../types';

export function AtletasTab() {
  const [atletas, setAtletas] = useState<Atleta[]>([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({ estado: '' });

  useEffect(() => {
    loadAtletas();
  }, [filters]);

  const loadAtletas = async () => {
    try {
      setLoading(true);
      const response = await atletasApi.list(filters);
      setAtletas(response.data);
    } catch (error) {
      console.error('Erro ao carregar atletas:', error);
    } finally {
      setLoading(false);
    }
  };

  const getStatusBadge = (status: string) => {
    const styles: Record<string, React.CSSProperties> = {
      ativo: { backgroundColor: '#d1fae5', color: '#065f46', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      inativo: { backgroundColor: '#fee2e2', color: '#991b1b', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      suspenso: { backgroundColor: '#fef3c7', color: '#92400e', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
    };

    const labels: Record<string, string> = {
      ativo: 'Ativo',
      inativo: 'Inativo',
      suspenso: 'Suspenso',
    };

    return <span style={styles[status] || styles.ativo}>{labels[status] || status}</span>;
  };

  const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT');
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 16 }}>
        <div style={{ flex: 1, maxWidth: 200 }}>
          <select
            value={filters.estado}
            onChange={(e) => setFilters({ ...filters, estado: e.target.value })}
            style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
          >
            <option value="">Todos os Estados</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
            <option value="suspenso">Suspenso</option>
          </select>
        </div>
        <button className="btn primary">Novo Atleta</button>
      </div>

      <div className="card">
        {loading ? (
          <div style={{ textAlign: 'center', padding: 40 }}>Carregando...</div>
        ) : atletas.length === 0 ? (
          <div style={{ textAlign: 'center', padding: 40, opacity: 0.7 }}>
            Nenhum atleta encontrado
          </div>
        ) : (
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ textAlign: 'left', borderBottom: '2px solid #e2e8f0' }}>
                <th style={{ padding: 12 }}>Nº Atleta</th>
                <th style={{ padding: 12 }}>Nome</th>
                <th style={{ padding: 12 }}>Nº Sócio</th>
                <th style={{ padding: 12 }}>Escalão</th>
                <th style={{ padding: 12 }}>Equipas</th>
                <th style={{ padding: 12 }}>Data Inscrição</th>
                <th style={{ padding: 12 }}>Estado</th>
                <th style={{ padding: 12 }}>Ações</th>
              </tr>
            </thead>
            <tbody>
              {atletas.map((atleta) => (
                <tr key={atleta.id} style={{ borderBottom: '1px solid #e2e8f0' }}>
                  <td style={{ padding: 12, fontWeight: 500 }}>{atleta.numero_atleta || '-'}</td>
                  <td style={{ padding: 12 }}>{atleta.membro?.user?.name || 'N/A'}</td>
                  <td style={{ padding: 12 }}>{atleta.membro?.numero_socio || '-'}</td>
                  <td style={{ padding: 12 }}>{atleta.escalao?.nome || '-'}</td>
                  <td style={{ padding: 12 }}>
                    {atleta.equipas && atleta.equipas.length > 0
                      ? atleta.equipas.map((eq) => eq.nome).join(', ')
                      : '-'}
                  </td>
                  <td style={{ padding: 12 }}>{formatDate(atleta.data_inscricao)}</td>
                  <td style={{ padding: 12 }}>{getStatusBadge(atleta.estado)}</td>
                  <td style={{ padding: 12 }}>
                    <div style={{ display: 'flex', gap: 4 }}>
                      <button className="btn" style={{ fontSize: 12 }}>
                        Ver
                      </button>
                      <button className="btn" style={{ fontSize: 12 }}>
                        Editar
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
