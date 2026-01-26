import { useState, useEffect } from 'react';
import { faturasApi } from '../api';
import api from '../../../lib/api';

interface Props {
  onClose: () => void;
  onSuccess: () => void;
}

interface Membro {
  id: number;
  numero_socio: string;
  user: {
    name: string;
    email: string;
  };
}

export function GerarMensalidadesModal({ onClose, onSuccess }: Props) {
  const [membros, setMembros] = useState<Membro[]>([]);
  const [loading, setLoading] = useState(false);

  const [formData, setFormData] = useState({
    membro_id: '',
    mes_inicio: new Date().toISOString().slice(0, 7),
    mes_fim: '',
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

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      const result = await faturasApi.gerarMensalidades({
        membro_id: Number(formData.membro_id),
        mes_inicio: formData.mes_inicio,
        mes_fim: formData.mes_fim || undefined,
      });

      alert(result.message);
      onSuccess();
    } catch (error: any) {
      console.error('Erro ao gerar mensalidades:', error);
      alert(error.response?.data?.message || 'Erro ao gerar mensalidades');
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
        style={{ maxWidth: 500, width: '100%' }}
        onClick={(e) => e.stopPropagation()}
      >
        <h2 style={{ marginTop: 0 }}>Gerar Faturas de Mensalidade</h2>

        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: 16 }}>
            <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
              Membro *
            </label>
            <select
              required
              value={formData.membro_id}
              onChange={(e) => setFormData({ ...formData, membro_id: e.target.value })}
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

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
                Mês Início *
              </label>
              <input
                type="month"
                required
                value={formData.mes_inicio}
                onChange={(e) => setFormData({ ...formData, mes_inicio: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>

            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
                Mês Fim (opcional)
              </label>
              <input
                type="month"
                value={formData.mes_fim}
                onChange={(e) => setFormData({ ...formData, mes_fim: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>
          </div>

          <div
            style={{
              padding: 12,
              backgroundColor: '#dbeafe',
              borderRadius: 4,
              marginBottom: 16,
              fontSize: 14,
            }}
          >
            <strong>Nota:</strong> Se não especificar o mês fim, será gerada apenas uma fatura para o
            mês de início.
          </div>

          <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
            <button type="button" className="btn" onClick={onClose}>
              Cancelar
            </button>
            <button type="submit" className="btn primary" disabled={loading}>
              {loading ? 'Gerando...' : 'Gerar Faturas'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
