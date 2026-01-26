import { useState, useEffect } from "react";
import { TrendingUp, TrendingDown, Clock, DollarSign } from "lucide-react";
import api from "../../../lib/api";

interface Transaction {
  id: number;
  data: string;
  descricao: string;
  valor: number;
  estado: string;
  tipo: string;
}

interface FinancialSummary {
  total: number;
  pago: number;
  pendente: number;
  em_atraso: number;
}

interface Props {
  memberId: number;
}

export function FinancialTab({ memberId }: Props) {
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [summary, setSummary] = useState<FinancialSummary>({
    total: 0,
    pago: 0,
    pendente: 0,
    em_atraso: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadFinancialData();
  }, [memberId]);

  const loadFinancialData = async () => {
    try {
      setLoading(true);
      const [transactionsRes, summaryRes] = await Promise.all([
        api.get(`/v2/membros/${memberId}/conta-corrente`),
        api.get(`/v2/membros/${memberId}/resumo-financeiro`),
      ]);
      
      setTransactions(transactionsRes.data.data || []);
      setSummary(summaryRes.data.data || { total: 0, pago: 0, pendente: 0, em_atraso: 0 });
    } catch (error) {
      console.error("Erro ao carregar dados financeiros:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="text-secondary">A carregar dados financeiros...</div>;
  }

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat("pt-PT", {
      style: "currency",
      currency: "EUR",
    }).format(value);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("pt-PT");
  };

  const getStatusBadge = (estado: string) => {
    switch (estado.toLowerCase()) {
      case "paga":
        return <span className="badge success">Paga</span>;
      case "pendente":
        return <span className="badge warning">Pendente</span>;
      case "cancelada":
        return <span className="badge danger">Cancelada</span>;
      default:
        return <span className="badge gray">{estado}</span>;
    }
  };

  return (
    <div>
      {/* Resumo Financeiro */}
      <div className="grid grid-4 mb-4">
        <div className="card" style={{ padding: "20px", backgroundColor: "#f0f9ff", border: "none" }}>
          <div className="flex items-center gap-2 mb-2">
            <div style={{
              width: "40px",
              height: "40px",
              borderRadius: "8px",
              backgroundColor: "#2563eb",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
            }}>
              <DollarSign size={20} color="white" />
            </div>
            <span className="text-secondary" style={{ fontSize: "13px", fontWeight: 600 }}>
              Total
            </span>
          </div>
          <p style={{ fontSize: "24px", fontWeight: 900, margin: 0, color: "#2563eb" }}>
            {formatCurrency(summary.total)}
          </p>
        </div>

        <div className="card" style={{ padding: "20px", backgroundColor: "#f0fdf4", border: "none" }}>
          <div className="flex items-center gap-2 mb-2">
            <div style={{
              width: "40px",
              height: "40px",
              borderRadius: "8px",
              backgroundColor: "#10b981",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
            }}>
              <TrendingUp size={20} color="white" />
            </div>
            <span className="text-secondary" style={{ fontSize: "13px", fontWeight: 600 }}>
              Pago
            </span>
          </div>
          <p style={{ fontSize: "24px", fontWeight: 900, margin: 0, color: "#10b981" }}>
            {formatCurrency(summary.pago)}
          </p>
        </div>

        <div className="card" style={{ padding: "20px", backgroundColor: "#fffbeb", border: "none" }}>
          <div className="flex items-center gap-2 mb-2">
            <div style={{
              width: "40px",
              height: "40px",
              borderRadius: "8px",
              backgroundColor: "#f59e0b",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
            }}>
              <Clock size={20} color="white" />
            </div>
            <span className="text-secondary" style={{ fontSize: "13px", fontWeight: 600 }}>
              Pendente
            </span>
          </div>
          <p style={{ fontSize: "24px", fontWeight: 900, margin: 0, color: "#f59e0b" }}>
            {formatCurrency(summary.pendente)}
          </p>
        </div>

        <div className="card" style={{ padding: "20px", backgroundColor: "#fef2f2", border: "none" }}>
          <div className="flex items-center gap-2 mb-2">
            <div style={{
              width: "40px",
              height: "40px",
              borderRadius: "8px",
              backgroundColor: "#ef4444",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
            }}>
              <TrendingDown size={20} color="white" />
            </div>
            <span className="text-secondary" style={{ fontSize: "13px", fontWeight: 600 }}>
              Em Atraso
            </span>
          </div>
          <p style={{ fontSize: "24px", fontWeight: 900, margin: 0, color: "#ef4444" }}>
            {formatCurrency(summary.em_atraso)}
          </p>
        </div>
      </div>

      {/* Conta Corrente */}
      <div className="card">
        <div className="card-header">
          <h3 className="card-title">Conta Corrente</h3>
        </div>

        {transactions.length === 0 ? (
          <p className="text-secondary text-center">Sem movimentos financeiros</p>
        ) : (
          <div style={{ overflowX: "auto" }}>
            <table className="table">
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Descrição</th>
                  <th>Tipo</th>
                  <th style={{ textAlign: "right" }}>Valor</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                {transactions.map((transaction) => (
                  <tr key={transaction.id}>
                    <td>{formatDate(transaction.data)}</td>
                    <td>{transaction.descricao}</td>
                    <td>
                      <span className="badge gray">{transaction.tipo}</span>
                    </td>
                    <td style={{ textAlign: "right", fontWeight: 700 }}>
                      {formatCurrency(transaction.valor)}
                    </td>
                    <td>{getStatusBadge(transaction.estado)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
