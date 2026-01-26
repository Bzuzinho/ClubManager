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

export function CreateFaturaModal({ onClose, onSuccess }: Props) {
  const [membros, setMembros] = useState<Membro[]>([]);
  const [loading, setLoading] = useState(false);

  const [formData, setFormData] = useState({
    membro_id: '',
    mes: new Date().toISOString().slice(0, 7), // YYYY-MM
    data_emissao: new Date().toISOString().slice(0, 10),
    data_vencimento: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().slice(0, 10),
  });

  const [itens, setItens] = useState([
    { descricao: '', tipo: 'mensalidade', valor: '' },
  ]);

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

  const handleAddItem = () => {
    setItens([...itens, { descricao: '', tipo: 'outro', valor: '' }]);
  };

  const handleRemoveItem = (index: number) => {
    setItens(itens.filter((_, i) => i !== index));
  };

  const handleItemChange = (index: number, field: string, value: string) => {
    const newItens = [...itens];
    newItens[index] = { ...newItens[index], [field]: value };
    setItens(newItens);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      await faturasApi.create({
        ...formData,
        membro_id: Number(formData.membro_id),
        itens: itens.map((item) => ({
          descricao: item.descricao,
          tipo: item.tipo,
          valor: parseFloat(item.valor),
        })),
      });

      alert('Fatura criada com sucesso!');
      onSuccess();
    } catch (error: any) {
      console.error('Erro ao criar fatura:', error);
      alert(error.response?.data?.message || 'Erro ao criar fatura');
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
        <h2 style={{ marginTop: 0 }}>Criar Fatura Avulsa</h2>

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

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 16, marginBottom: 16 }}>
            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Mês *</label>
              <input
                type="month"
                required
                value={formData.mes}
                onChange={(e) => setFormData({ ...formData, mes: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>

            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>Emissão *</label>
              <input
                type="date"
                required
                value={formData.data_emissao}
                onChange={(e) => setFormData({ ...formData, data_emissao: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>

            <div>
              <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
                Vencimento *
              </label>
              <input
                type="date"
                required
                value={formData.data_vencimento}
                onChange={(e) => setFormData({ ...formData, data_vencimento: e.target.value })}
                style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
              />
            </div>
          </div>

          <div style={{ marginBottom: 16 }}>
            <div
              style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                marginBottom: 8,
              }}
            >
              <label style={{ fontWeight: 500 }}>Itens *</label>
              <button type="button" className="btn" onClick={handleAddItem} style={{ fontSize: 12 }}>
                + Adicionar Item
              </button>
            </div>

            {itens.map((item, index) => (
              <div
                key={index}
                style={{
                  display: 'grid',
                  gridTemplateColumns: '2fr 1fr 1fr auto',
                  gap: 8,
                  marginBottom: 8,
                }}
              >
                <input
                  type="text"
                  required
                  placeholder="Descrição"
                  value={item.descricao}
                  onChange={(e) => handleItemChange(index, 'descricao', e.target.value)}
                  style={{ padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
                <select
                  value={item.tipo}
                  onChange={(e) => handleItemChange(index, 'tipo', e.target.value)}
                  style={{ padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                >
                  <option value="mensalidade">Mensalidade</option>
                  <option value="inscricao">Inscrição</option>
                  <option value="material">Material</option>
                  <option value="outro">Outro</option>
                </select>
                <input
                  type="number"
                  required
                  step="0.01"
                  min="0"
                  placeholder="Valor"
                  value={item.valor}
                  onChange={(e) => handleItemChange(index, 'valor', e.target.value)}
                  style={{ padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
                {itens.length > 1 && (
                  <button
                    type="button"
                    onClick={() => handleRemoveItem(index)}
                    style={{
                      padding: '8px 12px',
                      backgroundColor: '#fee2e2',
                      color: '#991b1b',
                      border: 'none',
                      borderRadius: 4,
                      cursor: 'pointer',
                    }}
                  >
                    ✕
                  </button>
                )}
              </div>
            ))}
          </div>

          <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
            <button type="button" className="btn" onClick={onClose}>
              Cancelar
            </button>
            <button type="submit" className="btn primary" disabled={loading}>
              {loading ? 'Criando...' : 'Criar Fatura'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
