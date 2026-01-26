import { useState, useEffect } from 'react';
import { faturasApi } from './api';
import type { Fatura } from './types';
import { CreateFaturaModal } from './components/CreateFaturaModal';
import { GerarMensalidadesModal } from './components/GerarMensalidadesModal';
import { FaturaDetailsModal } from './components/FaturaDetailsModal';

export default function Financial() {
  const [faturas, setFaturas] = useState<Fatura[]>([]);
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [filters, setFilters] = useState({
    estado: '',
    mes: '',
  });

  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showGerarModal, setShowGerarModal] = useState(false);
  const [selectedFatura, setSelectedFatura] = useState<Fatura | null>(null);

  useEffect(() => {
    loadFaturas();
  }, [currentPage, filters]);

  const loadFaturas = async () => {
    try {
      setLoading(true);
      const response = await faturasApi.list({
        page: currentPage,
        per_page: 15,
        ...filters,
      });
      setFaturas(response.data);
      setTotalPages(response.meta.last_page);
    } catch (error) {
      console.error('Erro ao carregar faturas:', error);
    } finally {
      setLoading(false);
    }
  };

  const getStatusBadge = (status: string) => {
    const styles: Record<string, React.CSSProperties> = {
      pendente: { backgroundColor: '#fef3c7', color: '#92400e', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      paga: { backgroundColor: '#d1fae5', color: '#065f46', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      cancelada: { backgroundColor: '#fee2e2', color: '#991b1b', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
      parcialmente_paga: { backgroundColor: '#dbeafe', color: '#1e40af', padding: '4px 8px', borderRadius: 4, fontSize: 12 },
    };

    const labels: Record<string, string> = {
      pendente: 'Pendente',
      paga: 'Paga',
      cancelada: 'Cancelada',
      parcialmente_paga: 'Parcialmente Paga',
    };

    return <span style={styles[status] || styles.pendente}>{labels[status] || status}</span>;
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
  };

  const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT');
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
        <div>
          <h1 style={{ fontSize: 24, fontWeight: 900, margin: 0 }}>Financeiro</h1>
          <p style={{ opacity: 0.7, margin: '4px 0 0 0' }}>Gestão de faturas e pagamentos</p>
        </div>
        <div style={{ display: 'flex', gap: 10 }}>
          <button className="btn" onClick={() => setShowGerarModal(true)}>
            Gerar Mensalidades
          </button>
          <button className="btn primary" onClick={() => setShowCreateModal(true)}>
            Criar Fatura
          </button>
        </div>
      </div>

      {/* Filtros */}
      <div className="card" style={{ marginBottom: 20 }}>
        <div style={{ display: 'flex', gap: 16 }}>
          <div style={{ flex: 1 }}>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>Estado</label>
            <select
              value={filters.estado}
              onChange={(e) => setFilters({ ...filters, estado: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            >
              <option value="">Todos os Estados</option>
              <option value="pendente">Pendente</option>
              <option value="paga">Paga</option>
              <option value="parcialmente_paga">Parcialmente Paga</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>

          <div style={{ flex: 1 }}>
            <label style={{ display: 'block', marginBottom: 4, fontSize: 14, fontWeight: 500 }}>Mês</label>
            <input
              type="month"
              value={filters.mes}
              onChange={(e) => setFilters({ ...filters, mes: e.target.value })}
              style={{ width: '100%', padding: 8, borderRadius: 4, border: '1px solid #e2e8f0' }}
            />
          </div>

          <div style={{ flex: 1, display: 'flex', alignItems: 'flex-end' }}>
            <button
              className="btn"
              onClick={() => setFilters({ estado: '', mes: '' })}
              style={{ width: '100%' }}
            >
              Limpar Filtros
            </button>
          </div>
        </div>
      </div>

      {/* Tabela de Faturas */}
      <div className="card">
        {loading ? (
          <div style={{ textAlign: 'center', padding: 40 }}>Carregando...</div>
        ) : faturas.length === 0 ? (
          <div style={{ textAlign: 'center', padding: 40, opacity: 0.7 }}>
            Nenhuma fatura encontrada
          </div>
        ) : (
          <>
            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
              <thead>
                <tr style={{ textAlign: 'left', borderBottom: '2px solid #e2e8f0' }}>
                  <th style={{ padding: 12 }}>Número</th>
                  <th style={{ padding: 12 }}>Membro</th>
                  <th style={{ padding: 12 }}>Mês</th>
                  <th style={{ padding: 12 }}>Emissão</th>
                  <th style={{ padding: 12 }}>Vencimento</th>
                  <th style={{ padding: 12 }}>Estado</th>
                  <th style={{ padding: 12, textAlign: 'right' }}>Total</th>
                  <th style={{ padding: 12, textAlign: 'right' }}>Pago</th>
                  <th style={{ padding: 12, textAlign: 'right' }}>Pendente</th>
                  <th style={{ padding: 12 }}>Ações</th>
                </tr>
              </thead>
              <tbody>
                {faturas.map((fatura) => (
                  <tr
                    key={fatura.id}
                    style={{ borderBottom: '1px solid #e2e8f0', cursor: 'pointer' }}
                    onClick={() => setSelectedFatura(fatura)}
                  >
                    <td style={{ padding: 12, fontWeight: 500 }}>{fatura.numero}</td>
                    <td style={{ padding: 12 }}>
                      <div>{fatura.membro?.user?.name || 'N/A'}</div>
                      <div style={{ fontSize: 12, opacity: 0.7 }}>
                        {fatura.membro?.numero_socio}
                      </div>
                    </td>
                    <td style={{ padding: 12 }}>{fatura.mes}</td>
                    <td style={{ padding: 12 }}>{formatDate(fatura.data_emissao)}</td>
                    <td style={{ padding: 12 }}>{formatDate(fatura.data_vencimento)}</td>
                    <td style={{ padding: 12 }}>{getStatusBadge(fatura.status_cache)}</td>
                    <td style={{ padding: 12, textAlign: 'right', fontWeight: 500 }}>
                      {formatCurrency(fatura.valor_total)}
                    </td>
                    <td style={{ padding: 12, textAlign: 'right', color: '#059669' }}>
                      {formatCurrency(fatura.valor_pago)}
                    </td>
                    <td style={{ padding: 12, textAlign: 'right', color: '#dc2626' }}>
                      {formatCurrency(fatura.valor_pendente)}
                    </td>
                    <td style={{ padding: 12 }}>
                      <button
                        className="btn"
                        onClick={(e) => {
                          e.stopPropagation();
                          setSelectedFatura(fatura);
                        }}
                        style={{ fontSize: 12 }}
                      >
                        Ver Detalhes
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {/* Paginação */}
            {totalPages > 1 && (
              <div style={{ display: 'flex', justifyContent: 'center', gap: 8, marginTop: 20 }}>
                <button
                  className="btn"
                  disabled={currentPage === 1}
                  onClick={() => setCurrentPage(currentPage - 1)}
                >
                  Anterior
                </button>
                <span style={{ padding: '8px 16px' }}>
                  Página {currentPage} de {totalPages}
                </span>
                <button
                  className="btn"
                  disabled={currentPage === totalPages}
                  onClick={() => setCurrentPage(currentPage + 1)}
                >
                  Próximo
                </button>
              </div>
            )}
          </>
        )}
      </div>

      {/* Modals */}
      {showCreateModal && (
        <CreateFaturaModal
          onClose={() => setShowCreateModal(false)}
          onSuccess={() => {
            setShowCreateModal(false);
            loadFaturas();
          }}
        />
      )}

      {showGerarModal && (
        <GerarMensalidadesModal
          onClose={() => setShowGerarModal(false)}
          onSuccess={() => {
            setShowGerarModal(false);
            loadFaturas();
          }}
        />
      )}

      {selectedFatura && (
        <FaturaDetailsModal
          fatura={selectedFatura}
          onClose={() => setSelectedFatura(null)}
          onUpdate={loadFaturas}
        />
      )}
    </div>
  );
}
