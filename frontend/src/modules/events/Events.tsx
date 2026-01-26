import { useState, useEffect } from 'react';
import { eventosApi } from './api';
import type { Evento } from './types';
import { CreateEventoModal } from './components/CreateEventoModal';
import { EventoDetailsModal } from './components/EventoDetailsModal';

export default function Events() {
  const [eventos, setEventos] = useState<Evento[]>([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    tipo: '',
    inscricoes_abertas: '',
  });

  const [showCreateModal, setShowCreateModal] = useState(false);
  const [selectedEvento, setSelectedEvento] = useState<Evento | null>(null);

  useEffect(() => {
    loadEventos();
  }, [filters]);

  const loadEventos = async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (filters.tipo) params.tipo = filters.tipo;
      if (filters.inscricoes_abertas !== '') {
        params.inscricoes_abertas = filters.inscricoes_abertas === 'true';
      }

      const response = await eventosApi.list(params);
      setEventos(response.data);
    } catch (error) {
      console.error('Erro ao carregar eventos:', error);
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT');
  };

  const formatDateTime = (date: string) => {
    return new Date(date).toLocaleString('pt-PT', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const getTipoBadge = (tipo: string) => {
    const styles: Record<string, React.CSSProperties> = {
      prova: { backgroundColor: '#dbeafe', color: '#1e40af', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      competicao: {
        backgroundColor: '#fef3c7',
        color: '#92400e',
        padding: '4px 8px',
        borderRadius: 4,
        fontSize: 12,
      },
      torneio: { backgroundColor: '#f3e8ff', color: '#6b21a8', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      social: { backgroundColor: '#d1fae5', color: '#065f46', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      outro: { backgroundColor: '#f8fafc', color: '#475569', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
    };

    const labels: Record<string, string> = {
      prova: 'Prova',
      competicao: 'Competição',
      torneio: 'Torneio',
      social: 'Social',
      outro: 'Outro',
    };

    return <span style={styles[tipo] || styles.outro}>{labels[tipo] || tipo}</span>;
  };

  const getInscricoesStatus = (evento: Evento) => {
    if (!evento.inscricoes_abertas) {
      return (
        <span
          style={{
            backgroundColor: '#fee2e2',
            color: '#991b1b',
            padding: '4px 8px',
            borderRadius: 4,
            fontSize: 12,
          }}
        >
          Fechadas
        </span>
      );
    }

    const limite = evento.data_limite_inscricao
      ? new Date(evento.data_limite_inscricao)
      : null;
    const hoje = new Date();

    if (limite && limite < hoje) {
      return (
        <span
          style={{
            backgroundColor: '#fef3c7',
            color: '#92400e',
            padding: '4px 8px',
            borderRadius: 4,
            fontSize: 12,
          }}
        >
          Prazo Expirado
        </span>
      );
    }

    return (
      <span
        style={{
          backgroundColor: '#d1fae5',
          color: '#065f46',
          padding: '4px 8px',
          borderRadius: 4,
          fontSize: 12,
        }}
      >
        Abertas
      </span>
    );
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <div>
          <h1 style={{ fontSize: 24, fontWeight: 900, margin: 0 }}>Eventos</h1>
          <p style={{ opacity: 0.7, margin: '4px 0 0 0' }}>Gestão de provas, competições e eventos</p>
        </div>
        <button className="btn primary" onClick={() => setShowCreateModal(true)}>
          Novo Evento
        </button>
      </div>

      {/* Filtros */}
      <div className="card" style={{ marginBottom: 20 }}>
        <div style={{ display: 'flex', gap: 16 }}>
          <div style={{ flex: 1 }}>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>Tipo</label>
            <select
              value={filters.tipo}
              onChange={(e) => setFilters({ ...filters, tipo: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            >
              <option value="">Todos os Tipos</option>
              <option value="prova">Prova</option>
              <option value="competicao">Competição</option>
              <option value="torneio">Torneio</option>
              <option value="social">Social</option>
              <option value="outro">Outro</option>
            </select>
          </div>

          <div style={{ flex: 1 }}>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>
              Inscrições
            </label>
            <select
              value={filters.inscricoes_abertas}
              onChange={(e) => setFilters({ ...filters, inscricoes_abertas: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            >
              <option value="">Todas</option>
              <option value="true">Abertas</option>
              <option value="false">Fechadas</option>
            </select>
          </div>

          <div style={{ flex: 1, display: 'flex', alignItems: 'flex-end' }}>
            <button
              className="btn"
              onClick={() => setFilters({ tipo: '', inscricoes_abertas: '' })}
              style={{ width: '100%' }}
            >
              Limpar Filtros
            </button>
          </div>
        </div>
      </div>

      {/* Lista de Eventos */}
      <div className="card">
        {loading ? (
          <div style={{ textAlign: 'center', padding: 40 }}>Carregando...</div>
        ) : eventos.length === 0 ? (
          <div style={{ textAlign: 'center', padding: 40, opacity: 0.7 }}>
            Nenhum evento encontrado
          </div>
        ) : (
          <div style={{ display: 'grid', gap: 16 }}>
            {eventos.map((evento) => (
              <div
                key={evento.id}
                style={{
                  border: '1px solid #e2e8f0',
                  borderRadius: 8,
                  padding: 16,
                  cursor: 'pointer',
                  transition: 'all 0.2s',
                }}
                onClick={() => setSelectedEvento(evento)}
                onMouseEnter={(e) => {
                  e.currentTarget.style.borderColor = '#3b82f6';
                  e.currentTarget.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.borderColor = '#e2e8f0';
                  e.currentTarget.style.boxShadow = 'none';
                }}
              >
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start' }}>
                  <div style={{ flex: 1 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 8 }}>
                      <h3 style={{ margin: 0, fontSize: 18, fontWeight: 600 }}>{evento.titulo}</h3>
                      {getTipoBadge(evento.tipo)}
                    </div>

                    {evento.descricao && (
                      <p style={{ margin: '4px 0', opacity: 0.7, fontSize: 14 }}>
                        {evento.descricao}
                      </p>
                    )}

                    <div
                      style={{
                        display: 'grid',
                        gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
                        gap: 12,
                        marginTop: 12,
                      }}
                    >
                      <div>
                        <div style={{ fontSize: 12, opacity: 0.7 }}>📅 Data</div>
                        <div style={{ fontSize: 14, fontWeight: 500 }}>
                          {formatDate(evento.data_inicio)}
                          {evento.data_fim && ` - ${formatDate(evento.data_fim)}`}
                        </div>
                      </div>

                      {evento.local && (
                        <div>
                          <div style={{ fontSize: 12, opacity: 0.7 }}>📍 Local</div>
                          <div style={{ fontSize: 14, fontWeight: 500 }}>{evento.local}</div>
                        </div>
                      )}

                      <div>
                        <div style={{ fontSize: 12, opacity: 0.7 }}>👥 Inscrições</div>
                        <div style={{ fontSize: 14, fontWeight: 500 }}>
                          {evento.inscricoes_count || 0}
                          {evento.capacidade_maxima && ` / ${evento.capacidade_maxima}`}
                        </div>
                      </div>

                      {evento.valor_inscricao !== null && evento.valor_inscricao !== undefined && (
                        <div>
                          <div style={{ fontSize: 12, opacity: 0.7 }}>💰 Valor</div>
                          <div style={{ fontSize: 14, fontWeight: 500 }}>
                            {evento.valor_inscricao === 0
                              ? 'Gratuito'
                              : new Intl.NumberFormat('pt-PT', {
                                  style: 'currency',
                                  currency: 'EUR',
                                }).format(evento.valor_inscricao)}
                          </div>
                        </div>
                      )}
                    </div>
                  </div>

                  <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end', gap: 8 }}>
                    {getInscricoesStatus(evento)}
                    {evento.data_limite_inscricao && evento.inscricoes_abertas && (
                      <div style={{ fontSize: 12, opacity: 0.7, textAlign: 'right' }}>
                        Até: {formatDateTime(evento.data_limite_inscricao)}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Modals */}
      {showCreateModal && (
        <CreateEventoModal
          onClose={() => setShowCreateModal(false)}
          onSuccess={() => {
            setShowCreateModal(false);
            loadEventos();
          }}
        />
      )}

      {selectedEvento && (
        <EventoDetailsModal
          evento={selectedEvento}
          onClose={() => setSelectedEvento(null)}
          onUpdate={loadEventos}
        />
      )}
    </div>
  );
}
