import { useState, useEffect } from 'react';
import { treinosApi, equipasApi } from '../api';
import type { Treino, Equipa } from '../types';

export function TreinosTab() {
  const [treinos, setTreinos] = useState<Treino[]>([]);
  const [equipas, setEquipas] = useState<Equipa[]>([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    equipa_id: '',
    tipo: '',
    data_inicio: '',
    data_fim: '',
  });

  useEffect(() => {
    loadEquipas();
    loadTreinos();
  }, [filters]);

  const loadEquipas = async () => {
    try {
      const response = await equipasApi.list();
      setEquipas(response.data);
    } catch (error) {
      console.error('Erro ao carregar equipas:', error);
    }
  };

  const loadTreinos = async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (filters.equipa_id) params.equipa_id = Number(filters.equipa_id);
      if (filters.tipo) params.tipo = filters.tipo;
      if (filters.data_inicio) params.data_inicio = filters.data_inicio;
      if (filters.data_fim) params.data_fim = filters.data_fim;

      const response = await treinosApi.list(params);
      setTreinos(response.data);
    } catch (error) {
      console.error('Erro ao carregar treinos:', error);
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT');
  };

  const formatTime = (time: string) => {
    return time.slice(0, 5); // HH:MM
  };

  const getTipoBadge = (tipo: string) => {
    const styles: Record<string, React.CSSProperties> = {
      treino: { backgroundColor: '#dbeafe', color: '#1e40af', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      jogo: { backgroundColor: '#d1fae5', color: '#065f46', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      competicao: {
        backgroundColor: '#fef3c7',
        color: '#92400e',
        padding: '4px 8px',
        borderRadius: 4,
        fontSize: 12,
      },
    };

    const labels: Record<string, string> = {
      treino: 'Treino',
      jogo: 'Jogo',
      competicao: 'Competição',
    };

    return <span style={styles[tipo] || styles.treino}>{labels[tipo] || tipo}</span>;
  };

  return (
    <div>
      {/* Filtros */}
      <div className="card" style={{ marginBottom: 16 }}>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 12 }}>
          <div>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>
              Equipa
            </label>
            <select
              value={filters.equipa_id}
              onChange={(e) => setFilters({ ...filters, equipa_id: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            >
              <option value="">Todas as Equipas</option>
              {equipas.map((equipa) => (
                <option key={equipa.id} value={equipa.id}>
                  {equipa.nome}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>Tipo</label>
            <select
              value={filters.tipo}
              onChange={(e) => setFilters({ ...filters, tipo: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            >
              <option value="">Todos os Tipos</option>
              <option value="treino">Treino</option>
              <option value="jogo">Jogo</option>
              <option value="competicao">Competição</option>
            </select>
          </div>

          <div>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>
              Data Início
            </label>
            <input
              type="date"
              value={filters.data_inicio}
              onChange={(e) => setFilters({ ...filters, data_inicio: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            />
          </div>

          <div>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>
              Data Fim
            </label>
            <input
              type="date"
              value={filters.data_fim}
              onChange={(e) => setFilters({ ...filters, data_fim: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            />
          </div>
        </div>

        <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 12 }}>
          <button
            className="btn"
            onClick={() => setFilters({ equipa_id: '', tipo: '', data_inicio: '', data_fim: '' })}
          >
            Limpar Filtros
          </button>
          <button className="btn primary">Novo Treino</button>
        </div>
      </div>

      {/* Lista de Treinos */}
      <div className="card">
        {loading ? (
          <div style={{ textAlign: 'center', padding: 40 }}>Carregando...</div>
        ) : treinos.length === 0 ? (
          <div style={{ textAlign: 'center', padding: 40, opacity: 0.7 }}>
            Nenhum treino encontrado
          </div>
        ) : (
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ textAlign: 'left', borderBottom: '2px solid #e2e8f0' }}>
                <th style={{ padding: 12 }}>Data</th>
                <th style={{ padding: 12 }}>Horário</th>
                <th style={{ padding: 12 }}>Equipa</th>
                <th style={{ padding: 12 }}>Local</th>
                <th style={{ padding: 12 }}>Tipo</th>
                <th style={{ padding: 12 }}>Presenças</th>
                <th style={{ padding: 12 }}>Ações</th>
              </tr>
            </thead>
            <tbody>
              {treinos.map((treino) => (
                <tr key={treino.id} style={{ borderBottom: '1px solid #e2e8f0' }}>
                  <td style={{ padding: 12, fontWeight: 500 }}>{formatDate(treino.data)}</td>
                  <td style={{ padding: 12 }}>
                    {formatTime(treino.hora_inicio)} - {formatTime(treino.hora_fim)}
                  </td>
                  <td style={{ padding: 12 }}>{treino.equipa?.nome || 'N/A'}</td>
                  <td style={{ padding: 12 }}>{treino.local || '-'}</td>
                  <td style={{ padding: 12 }}>{getTipoBadge(treino.tipo)}</td>
                  <td style={{ padding: 12 }}>
                    <span
                      style={{
                        backgroundColor: '#f8fafc',
                        padding: '4px 8px',
                        borderRadius: 4,
                        fontSize: 12,
                      }}
                    >
                      {treino.presencas_count || 0} registadas
                    </span>
                  </td>
                  <td style={{ padding: 12 }}>
                    <div style={{ display: 'flex', gap: 4 }}>
                      <button className="btn" style={{ fontSize: 12 }}>
                        Presenças
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
