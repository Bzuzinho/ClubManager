import { useState } from 'react';
import { faturasApi } from '../api';
import type { Fatura } from '../types';

interface Props {
  fatura: Fatura;
  onClose: () => void;
  onUpdate: () => void;
}

export function FaturaDetailsModal({ fatura, onClose, onUpdate }: Props) {
  const [showPagamentoForm, setShowPagamentoForm] = useState(false);
  const [showItemForm, setShowItemForm] = useState(false);
  const [loading, setLoading] = useState(false);

  const [pagamentoData, setPagamentoData] = useState({
    data: new Date().toISOString().slice(0, 10),
    valor: fatura.valor_pendente.toString(),
    metodo: 'dinheiro',
    referencia: '',
    observacoes: '',
  });

  const [itemData, setItemData] = useState({
    descricao: '',
    tipo: 'outro',
    valor: '',
  });

  const handleRegistarPagamento = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      await faturasApi.registarPagamento(fatura.id, {
        ...pagamentoData,
        valor: parseFloat(pagamentoData.valor),
      });

      alert('Pagamento registado com sucesso!');
      setShowPagamentoForm(false);
      onUpdate();
      onClose();
    } catch (error: any) {
      console.error('Erro ao registar pagamento:', error);
      alert(error.response?.data?.message || 'Erro ao registar pagamento');
    } finally {
      setLoading(false);
    }
  };

  const handleAdicionarItem = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      await faturasApi.adicionarItem(fatura.id, {
        ...itemData,
        valor: parseFloat(itemData.valor),
      });

      alert('Item adicionado com sucesso!');
      setShowItemForm(false);
      onUpdate();
      onClose();
    } catch (error: any) {
      console.error('Erro ao adicionar item:', error);
      alert(error.response?.data?.message || 'Erro ao adicionar item');
    } finally {
      setLoading(false);
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
  };

  const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT');
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
        style={{ maxWidth: 800, width: '100%', maxHeight: '90vh', overflow: 'auto' }}
        onClick={(e) => e.stopPropagation()}
      >
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start' }}>
          <div>
            <h2 style={{ marginTop: 0 }}>Fatura {fatura.numero}</h2>
            <p style={{ opacity: 0.7, margin: '4px 0 0 0' }}>
              {fatura.membro?.user?.name} ({fatura.membro?.numero_socio})
            </p>
          </div>
          <button onClick={onClose} className="btn">
            ✕
          </button>
        </div>

        {/* Informações Gerais */}
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: '1fr 1fr 1fr',
            gap: 16,
            marginTop: 20,
            padding: 16,
            backgroundColor: '#f8fafc',
            borderRadius: 8,
          }}
        >
          <div>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Mês</div>
            <div style={{ fontWeight: 500 }}>{fatura.mes}</div>
          </div>
          <div>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Emissão</div>
            <div style={{ fontWeight: 500 }}>{formatDate(fatura.data_emissao)}</div>
          </div>
          <div>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Vencimento</div>
            <div style={{ fontWeight: 500 }}>{formatDate(fatura.data_vencimento)}</div>
          </div>
        </div>

        {/* Resumo Financeiro */}
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: '1fr 1fr 1fr',
            gap: 16,
            marginTop: 16,
          }}
        >
          <div style={{ padding: 16, backgroundColor: '#f8fafc', borderRadius: 8 }}>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Valor Total</div>
            <div style={{ fontSize: 20, fontWeight: 700 }}>{formatCurrency(fatura.valor_total)}</div>
          </div>
          <div style={{ padding: 16, backgroundColor: '#d1fae5', borderRadius: 8 }}>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Valor Pago</div>
            <div style={{ fontSize: 20, fontWeight: 700, color: '#059669' }}>
              {formatCurrency(fatura.valor_pago)}
            </div>
          </div>
          <div style={{ padding: 16, backgroundColor: '#fee2e2', borderRadius: 8 }}>
            <div style={{ fontSize: 12, opacity: 0.7 }}>Valor Pendente</div>
            <div style={{ fontSize: 20, fontWeight: 700, color: '#dc2626' }}>
              {formatCurrency(fatura.valor_pendente)}
            </div>
          </div>
        </div>

        {/* Itens */}
        <div style={{ marginTop: 24 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <h3 style={{ margin: 0 }}>Itens</h3>
            {fatura.status_cache !== 'cancelada' && (
              <button
                className="btn"
                onClick={() => setShowItemForm(!showItemForm)}
                style={{ fontSize: 12 }}
              >
                + Adicionar Item
              </button>
            )}
          </div>

          {showItemForm && (
            <form onSubmit={handleAdicionarItem} style={{ marginTop: 12 }}>
              <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr 1fr', gap: 8 }}>
                <input
                  type="text"
                  required
                  placeholder="Descrição"
                  value={itemData.descricao}
                  onChange={(e) => setItemData({ ...itemData, descricao: e.target.value })}
                  style={{ padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
                <select
                  value={itemData.tipo}
                  onChange={(e) => setItemData({ ...itemData, tipo: e.target.value })}
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
                  value={itemData.valor}
                  onChange={(e) => setItemData({ ...itemData, valor: e.target.value })}
                  style={{ padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
              </div>
              <div style={{ display: 'flex', gap: 8, marginTop: 8 }}>
                <button type="submit" className="btn primary" disabled={loading} style={{ fontSize: 12 }}>
                  Adicionar
                </button>
                <button
                  type="button"
                  className="btn"
                  onClick={() => setShowItemForm(false)}
                  style={{ fontSize: 12 }}
                >
                  Cancelar
                </button>
              </div>
            </form>
          )}

          <table style={{ width: '100%', marginTop: 12, borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ textAlign: 'left', borderBottom: '2px solid #e2e8f0' }}>
                <th style={{ padding: 8 }}>Descrição</th>
                <th style={{ padding: 8 }}>Tipo</th>
                <th style={{ padding: 8, textAlign: 'right' }}>Valor</th>
              </tr>
            </thead>
            <tbody>
              {fatura.itens?.map((item) => (
                <tr key={item.id} style={{ borderBottom: '1px solid #e2e8f0' }}>
                  <td style={{ padding: 8 }}>{item.descricao}</td>
                  <td style={{ padding: 8 }}>{item.tipo}</td>
                  <td style={{ padding: 8, textAlign: 'right' }}>{formatCurrency(item.valor)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Pagamentos */}
        <div style={{ marginTop: 24 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <h3 style={{ margin: 0 }}>Pagamentos</h3>
            {fatura.status_cache !== 'cancelada' && fatura.valor_pendente > 0 && (
              <button
                className="btn primary"
                onClick={() => setShowPagamentoForm(!showPagamentoForm)}
                style={{ fontSize: 12 }}
              >
                + Registar Pagamento
              </button>
            )}
          </div>

          {showPagamentoForm && (
            <form
              onSubmit={handleRegistarPagamento}
              style={{ marginTop: 12, padding: 16, backgroundColor: '#f8fafc', borderRadius: 8 }}
            >
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 12 }}>
                <div>
                  <label style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>Data *</label>
                  <input
                    type="date"
                    required
                    value={pagamentoData.data}
                    onChange={(e) => setPagamentoData({ ...pagamentoData, data: e.target.value })}
                    style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                  />
                </div>
                <div>
                  <label style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>Valor *</label>
                  <input
                    type="number"
                    required
                    step="0.01"
                    min="0.01"
                    max={fatura.valor_pendente}
                    value={pagamentoData.valor}
                    onChange={(e) => setPagamentoData({ ...pagamentoData, valor: e.target.value })}
                    style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                  />
                </div>
                <div>
                  <label style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>Método *</label>
                  <select
                    value={pagamentoData.metodo}
                    onChange={(e) => setPagamentoData({ ...pagamentoData, metodo: e.target.value })}
                    style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                  >
                    <option value="dinheiro">Dinheiro</option>
                    <option value="mb">MB</option>
                    <option value="mbway">MBWay</option>
                    <option value="transferencia">Transferência</option>
                    <option value="cheque">Cheque</option>
                  </select>
                </div>
              </div>
              <div style={{ marginTop: 12 }}>
                <label style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>Referência</label>
                <input
                  type="text"
                  value={pagamentoData.referencia}
                  onChange={(e) => setPagamentoData({ ...pagamentoData, referencia: e.target.value })}
                  style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                />
              </div>
              <div style={{ marginTop: 12 }}>
                <label style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>Observações</label>
                <textarea
                  value={pagamentoData.observacoes}
                  onChange={(e) => setPagamentoData({ ...pagamentoData, observacoes: e.target.value })}
                  style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
                  rows={2}
                />
              </div>
              <div style={{ display: 'flex', gap: 8, marginTop: 12 }}>
                <button type="submit" className="btn primary" disabled={loading}>
                  {loading ? 'Registando...' : 'Registar'}
                </button>
                <button
                  type="button"
                  className="btn"
                  onClick={() => setShowPagamentoForm(false)}
                >
                  Cancelar
                </button>
              </div>
            </form>
          )}

          <table style={{ width: '100%', marginTop: 12, borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ textAlign: 'left', borderBottom: '2px solid #e2e8f0' }}>
                <th style={{ padding: 8 }}>Data</th>
                <th style={{ padding: 8 }}>Método</th>
                <th style={{ padding: 8 }}>Referência</th>
                <th style={{ padding: 8, textAlign: 'right' }}>Valor</th>
              </tr>
            </thead>
            <tbody>
              {fatura.pagamentos?.length === 0 ? (
                <tr>
                  <td colSpan={4} style={{ padding: 20, textAlign: 'center', opacity: 0.7 }}>
                    Nenhum pagamento registado
                  </td>
                </tr>
              ) : (
                fatura.pagamentos?.map((pagamento) => (
                  <tr key={pagamento.id} style={{ borderBottom: '1px solid #e2e8f0' }}>
                    <td style={{ padding: 8 }}>{formatDate(pagamento.data)}</td>
                    <td style={{ padding: 8 }}>{pagamento.metodo}</td>
                    <td style={{ padding: 8 }}>{pagamento.referencia || '-'}</td>
                    <td style={{ padding: 8, textAlign: 'right', color: '#059669', fontWeight: 500 }}>
                      {formatCurrency(pagamento.valor)}
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
