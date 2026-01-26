import { useState, useEffect } from 'react';
import { eventosApi } from '../api';
import api from '../../../lib/api';
import type { Evento } from '../types';

interface Props {
  evento: Evento;
  onClose: () => void;
  onUpdate: () => void;
}

interface Membro {
  id: number;
  numero_socio: string;
  user: {
    name: string;
    email: string;
  };
}

export function EventoDetailsModal({ evento, onClose, onUpdate }: Props) {
  const [showInscricaoForm, setShowInscricaoForm] = useState(false);
  const [membros, setMembros] = useState<Membro[]>([]);
  const [loading, setLoading] = useState(false);

  const [inscricaoData, setInscricaoData] = useState({
    membro_id: '',
    observacoes: '',
  });

  useEffect(() => {
    loadMembros();
  }, []);

  const loadMembros = async () => {
    try {
      const response = await api.get('/v2/membros', {
        params: { per_page: 100, estado: 'ativo' },
      });
      setMembros(response.data.data);
    } catch (error) {
      console.error('Erro ao carregar membros:', error);
    }
  };

  const handleInscrever = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      await eventosApi.inscrever(evento.id, {
        membro_id: Number(inscricaoData.membro_id),
        observacoes: inscricaoData.observacoes || undefined,
      });

      alert('Inscrição realizada com sucesso!');
      setShowInscricaoForm(false);
      onUpdate();
      onClose();
    } catch (error: any) {
      console.error('Erro ao realizar inscrição:', error);
      alert(error.response?.data?.message || 'Erro ao realizar inscrição');
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

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
  };

  const getEstadoBadge = (estado: string) => {
    const styles: Record<string, React.CSSProperties> = {
      pendente: { backgroundColor: '#fef3c7', color: '#92400e', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      confirmada: { backgroundColor: '#d1fae5', color: '#065f46', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      cancelada: { backgroundColor: '#fee2e2', color: '#991b1b', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
    };

    const labels: Record<string, string> = {
      pendente: 'Pendente',
      confirmada: 'Confirmada',
      cancelada: 'Cancelada',
    };

    return <span style={styles[estado] || styles.pendente}>{labels[estado] || estado}</span>;
  };

  const canInscrever = evento.inscricoes_abertas && 
    (!evento.capacidade_maxima || (evento.inscricoes_count || 0) < evento.capacidade_maxima) &&
    (!evento.data_limite_inscricao || new Date(evento.data_limite_inscricao) > new Date());

  return (
    <div
      style={{
        position: 'fixed',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: 'rgba(0,0,0,0.5)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: 1000,
      }}
      onClick={onClose}
    >
      <div
        className="card"
        style={{ maxWidth: 800, width: '100%', maxHeight: '90vh', overflow: 'auto' }}
        onClick={(e) => e.stopPropagation()}
      >
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start' }}>
          <div>
            <h2 style={{ marginTop: 0 }}>{evento.titulo}</h2>
            {evento.descricao && (
              <p style={{ opacity: 0.7, margin: '4px 0 0 0' }}>{evento.descricao}</p>
            )}
          </div>
          <button onClick={onClose} className="btn">
            ✕
          </button>
        </div>

        {/* Informações do Evento */}
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
            gap: 16,
            marginTop: 20,
            padding: 16,
            backgroundColor: '#f8fafc',
            borderRadius: 8,
          }}
        >
          <div>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Data Início</div>
            <div style={{ fontWeight: 500 }}>{formatDate(evento.data_inicio)}</div>
          </div>

          {evento.data_fim && (
            <div>
              <div style={{ fontSize: 12, opacity: 0.7 }}>Data Fim</div>
              <div style={{ fontWeight: 500 }}>{formatDate(evento.data_fim)}</div>
            </div>
          )}

          {evento.local && (
            <div>
              <div style={{ fontSize: 12, opacity: 0.7 }}>Local</div>
              <div style={{ fontWeight: 500 }}>{evento.local}</div>
            </div>
          )}

          <div>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Inscrições</div>
            <div style={{ fontWeight: 500 }}>
              {evento.inscricoes_count || 0}
              {evento.capacidade_maxima && ` / ${evento.capacidade_maxima}`}
            </div>
          </div>

          {evento.valor_inscricao !== null && evento.valor_inscricao !== undefined && (
            <div>
              <div style={{ fontSize: 12, opacity: 0.7 }}>Valor</div>
              <div style={{ fontWeight: 500 }}>
                {evento.valor_inscricao === 0 ? 'Gratuito' : formatCurrency(evento.valor_inscricao)}
              </div>
            </div>
          )}

          {evento.data_limite_inscricao && (
            <div>
              <div style={{ fontSize: 12, opacity: 0.7 }}>Limite Inscrição</div>
              <div style={{ fontWeight: 500 }}>{formatDateTime(evento.data_limite_inscricao)}</div>
            </div>
          )}
        </div>

        {evento.observacoes && (
          <div
            style={{
              marginTop: 16,
              padding: 12,
              backgroundColor: '#dbeafe',
              borderRadius: 8,
              fontSize: 14,
            }}
          >
            <strong>Observações:</strong> {evento.observacoes}
          </div>
        )}

        {/* Inscrições */}
        <div style={{ marginTop: 24 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <h3 style={{ margin: 0 }}>Inscrições</h3>
            {canInscrever && (
              <button
                className="btn primary"
                onClick={() => setShowInscricaoForm(!showInscricaoForm)}
                style={{ fontSize: 12 }}
              >
                + Nova Inscrição
              </button>
            )}
          </div>

          {!evento.inscricoes_abertas && (
            <div
              style={{
                marginTop: 12,
                padding: 12,
                backgroundColor: '#fee2e2',
                borderRadius: 8,
                fontSize: 14,
                color: '#991b1b',
              }}
            >
              As inscrições para este evento estão fechadas.
            </div>
          )}

          {evento.inscricoes_abertas && !canInscrever && (
            <div
              style={{
                marginTop: 12,
                padding: 12,
                backgroundColor: '#fef3c7',
                borderRadius: 8,
                fontSize: 14,
                color: '#92400e',
              }}
            >
              {evento.capacidade_maxima && (evento.inscricoes_count || 0) >= evento.capacidade_maxima
                ? 'Evento lotado - Capacidade máxima atingida'
                : 'Prazo de inscrição expirado'}
            </div>
          )}

          {showInscricaoForm && (
            <form
              onSubmit={handleInscrever}
              style={{ marginTop: 12, padding: 16, backgroundColor: '#f8fafc', borderRadius: 8 }}
            >
              <div style={{ marginBottom: 12 }}>
                <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>
                  Membro *
                </label>
                <select
                  required
                  value={inscricaoData.membro_id}
                  onChange={(e) => setInscricaoData({ ...inscricaoData, membro_id: e.target.value })}
                  style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                >
                  <option value="">Selecione um membro</option>
                  {membros.map((membro) => (
                    <option key={membro.id} value={membro.id}>
                      {membro.numero_socio} - {membro.user.name}
                    </option>
                  ))}
                </select>
              </div>

              <div style={{ marginBottom: 12 }}>
                <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>
                  Observações
                </label>
                <textarea
                  value={inscricaoData.observacoes}
                  onChange={(e) => setInscricaoData({ ...inscricaoData, observacoes: e.target.value })}
                  style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                  rows={2}
                />
              </div>

              <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" className="btn primary" disabled={loading}>
                  {loading ? 'Inscrevendo...' : 'Realizar Inscrição'}
                </button>
                <button
                  type="button"
                  className="btn"
                  onClick={() => setShowInscricaoForm(false)}
                >
                  Cancelar
                </button>
              </div>
            </form>
          )}

          <table style={{ width: '100%', marginTop: 12, borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ textAlign: 'left', borderBottom: '2px solid #e2e8f0' }}>
                <th style={{ padding: 8 }}>Nº Sócio</th>
                <th style={{ padding: 8 }}>Nome</th>
                <th style={{ padding: 8 }}>Data Inscrição</th>
                <th style={{ padding: 8 }}>Estado</th>
                <th style={{ padding: 8 }}>Observações</th>
              </tr>
            </thead>
            <tbody>
              {(!evento.inscricoes || evento.inscricoes.length === 0) ? (
                <tr>
                  <td colSpan={5} style={{ padding: 20, textAlign: 'center', opacity: 0.7 }}>
                    Nenhuma inscrição registada
                  </td>
                </tr>
              ) : (
                evento.inscricoes.map((inscricao) => (
                  <tr key={inscricao.id} style={{ borderBottom: '1px solid #e2e8f0' }}>
                    <td style={{ padding: 8 }}>{inscricao.membro?.numero_socio || '-'}</td>
                    <td style={{ padding: 8 }}>{inscricao.membro?.user?.name || 'N/A'}</td>
                    <td style={{ padding: 8 }}>{formatDate(inscricao.data_inscricao)}</td>
                    <td style={{ padding: 8 }}>{getEstadoBadge(inscricao.estado)}</td>
                    <td style={{ padding: 8, fontSize: 12, opacity: 0.7 }}>
                      {inscricao.observacoes || '-'}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
