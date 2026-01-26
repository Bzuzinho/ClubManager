import { useState } from 'react';
import { eventosApi } from '../api';

interface Props {
  onClose: () => void;
  onSuccess: () => void;
}

export function CreateEventoModal({ onClose, onSuccess }: Props) {
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    titulo: '',
    descricao: '',
    tipo: 'prova',
    data_inicio: '',
    data_fim: '',
    local: '',
    capacidade_maxima: '',
    inscricoes_abertas: true,
    data_limite_inscricao: '',
    valor_inscricao: '',
    observacoes: '',
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      const data: any = {
        titulo: formData.titulo,
        tipo: formData.tipo,
        data_inicio: formData.data_inicio,
        inscricoes_abertas: formData.inscricoes_abertas,
      };

      if (formData.descricao) data.descricao = formData.descricao;
      if (formData.data_fim) data.data_fim = formData.data_fim;
      if (formData.local) data.local = formData.local;
      if (formData.capacidade_maxima) data.capacidade_maxima = parseInt(formData.capacidade_maxima);
      if (formData.data_limite_inscricao) data.data_limite_inscricao = formData.data_limite_inscricao;
      if (formData.valor_inscricao) data.valor_inscricao = parseFloat(formData.valor_inscricao);
      if (formData.observacoes) data.observacoes = formData.observacoes;

      await eventosApi.create(data);
      alert('Evento criado com sucesso!');
      onSuccess();
    } catch (error: any) {
      console.error('Erro ao criar evento:', error);
      alert(error.response?.data?.message || 'Erro ao criar evento');
    } finally {
      setLoading(false);
    }
  };

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
        style={{ maxWidth: 600, width: '100%', maxHeight: '90vh', overflow: 'auto' }}
        onClick={(e) => e.stopPropagation()}
      >
        <h2 style={{ marginTop: 0 }}>Criar Novo Evento</h2>

        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: 16 }}>
            <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Título *</label>
            <input
              type="text"
              required
              value={formData.titulo}
              onChange={(e) => setFormData({ ...formData, titulo: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            />
          </div>

          <div style={{ marginBottom: 16 }}>
            <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Descrição</label>
            <textarea
              value={formData.descricao}
              onChange={(e) => setFormData({ ...formData, descricao: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              rows={3}
            />
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Tipo *</label>
              <select
                value={formData.tipo}
                onChange={(e) => setFormData({ ...formData, tipo: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              >
                <option value="prova">Prova</option>
                <option value="competicao">Competição</option>
                <option value="torneio">Torneio</option>
                <option value="social">Social</option>
                <option value="outro">Outro</option>
              </select>
            </div>

            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Local</label>
              <input
                type="text"
                value={formData.local}
                onChange={(e) => setFormData({ ...formData, local: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Data Início *</label>
              <input
                type="date"
                required
                value={formData.data_inicio}
                onChange={(e) => setFormData({ ...formData, data_inicio: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>

            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Data Fim</label>
              <input
                type="date"
                value={formData.data_fim}
                onChange={(e) => setFormData({ ...formData, data_fim: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>
          </div>

          <div style={{ marginBottom: 16 }}>
            <label style={{ display: 'flex', alignItems: 'center', gap: 8, cursor: 'pointer' }}>
              <input
                type="checkbox"
                checked={formData.inscricoes_abertas}
                onChange={(e) => setFormData({ ...formData, inscricoes_abertas: e.target.checked })}
              />
              <span style={{ fontWeight: 500 }}>Inscrições Abertas</span>
            </label>
          </div>

          {formData.inscricoes_abertas && (
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 16, marginBottom: 16 }}>
              <div>
                <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
                  Data Limite Inscrição
                </label>
                <input
                  type="datetime-local"
                  value={formData.data_limite_inscricao}
                  onChange={(e) => setFormData({ ...formData, data_limite_inscricao: e.target.value })}
                  style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
              </div>

              <div>
                <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
                  Capacidade Máxima
                </label>
                <input
                  type="number"
                  min="0"
                  value={formData.capacidade_maxima}
                  onChange={(e) => setFormData({ ...formData, capacidade_maxima: e.target.value })}
                  style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
              </div>

              <div>
                <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
                  Valor Inscrição (€)
                </label>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  value={formData.valor_inscricao}
                  onChange={(e) => setFormData({ ...formData, valor_inscricao: e.target.value })}
                  style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
              </div>
            </div>
          )}

          <div style={{ marginBottom: 16 }}>
            <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Observações</label>
            <textarea
              value={formData.observacoes}
              onChange={(e) => setFormData({ ...formData, observacoes: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              rows={2}
            />
          </div>

          <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
            <button type="button" className="btn" onClick={onClose}>
              Cancelar
            </button>
            <button type="submit" className="btn primary" disabled={loading}>
              {loading ? 'Criando...' : 'Criar Evento'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
