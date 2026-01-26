import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../lib/api';

interface DashboardStats {
  membros_ativos: number;
  atletas_ativos: number;
  encarregados_educacao: number;
  eventos_proximos: number;
  receitas_mes: number;
}

interface Evento {
  id: number;
  titulo: string;
  data_inicio: string;
  local?: string;
  tipo: string;
}

interface AtividadeRecente {
  id: number;
  descricao: string;
  valor?: number;
  data: string;
  tipo: 'pagamento' | 'inscricao' | 'outro';
}

export default function Dashboard() {
  const navigate = useNavigate();
  const [stats, setStats] = useState<DashboardStats>({
    membros_ativos: 0,
    atletas_ativos: 0,
    encarregados_educacao: 0,
    eventos_proximos: 0,
    receitas_mes: 0,
  });
  const [eventos, setEventos] = useState<Evento[]>([]);
  const [atividades, setAtividades] = useState<AtividadeRecente[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    try {
      setLoading(true);
      
      // Carregar estatísticas
      await loadStats();
      
      // Carregar próximos eventos
      await loadEventos();
      
      // Carregar atividades recentes
      await loadAtividades();
    } catch (error) {
      console.error('Erro ao carregar dados do dashboard:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadStats = async () => {
    try {
      // Membros ativos - com tratamento de erro
      try {
        const membrosResponse = await api.get('/v2/membros', {
          params: { estado: 'ativo', per_page: 1 },
        });
        
        const atletasResponse = await api.get('/v2/membros', {
          params: { atleta: true, per_page: 1 },
        });

        setStats({
          membros_ativos: membrosResponse.data.meta?.total || membrosResponse.data.data?.length || 0,
          atletas_ativos: atletasResponse.data.meta?.total || atletasResponse.data.data?.length || 0,
          encarregados_educacao: 0,
          eventos_proximos: 0,
          receitas_mes: 0,
        });
      } catch (err) {
        console.error('Erro ao carregar stats:', err);
        // Manter stats em 0 se houver erro
        setStats({
          membros_ativos: 0,
          atletas_ativos: 0,
          encarregados_educacao: 0,
          eventos_proximos: 0,
          receitas_mes: 0,
        });
      }
    } catch (error) {
      console.error('Erro ao carregar estatísticas:', error);
    }
  };

  const loadEventos = async () => {
    try {
      const hoje = new Date().toISOString().split('T')[0];
      const response = await api.get('/eventos', {
        params: {
          data_inicio: hoje,
          per_page: 3,
        },
      });
      
      const eventosData = response.data.data || [];
      setEventos(eventosData);
      
      // Atualizar contador de eventos próximos
      setStats(prev => ({
        ...prev,
        eventos_proximos: response.data.meta?.total || eventosData.length,
      }));
    } catch (error) {
      console.error('Erro ao carregar eventos:', error);
    }
  };

  const loadAtividades = async () => {
    try {
      // Carregar últimos pagamentos (faturas recentes)
      const faturasResponse = await api.get('/v2/faturas', {
        params: { per_page: 5 },
      });
      
      const faturas = faturasResponse.data.data || [];
      const atividadesData: AtividadeRecente[] = faturas
        .filter((f: any) => f.valor_pago > 0)
        .slice(0, 3)
        .map((fatura: any) => ({
          id: fatura.id,
          descricao: `Pagamento mensalidade – ${fatura.membro?.user?.name || 'N/A'}`,
          valor: fatura.valor_pago,
          data: fatura.created_at,
          tipo: 'pagamento' as const,
        }));
      
      setAtividades(atividadesData);
    } catch (error) {
      console.error('Erro ao carregar atividades:', error);
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
  };

  const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT');
  };

  const getTipoEventoBadge = (tipo: string) => {
    const badges: Record<string, string> = {
      prova: '🏊',
      competicao: '🏆',
      torneio: '🥇',
      social: '🎉',
      outro: '📅',
    };
    return badges[tipo] || '📅';
  };

  if (loading) {
    return (
      <div style={{ textAlign: 'center', padding: 60 }}>
        <div style={{ fontSize: 18, opacity: 0.7 }}>Carregando dashboard...</div>
      </div>
    );
  }

  return (
    <div>
      {/* TÍTULO */}
      <div className="page-header page-header--stack">
        <h1 className="page-title">Dashboard</h1>
        <p className="page-subtitle">Visão geral do clube</p>
      </div>

      {/* KPI CARDS */}
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(5, minmax(0, 1fr))',
          gap: 15,
          marginBottom: 15,
        }}
      >
        <div className="card" style={{ cursor: 'pointer' }} onClick={() => navigate('/membros')}>
          <div style={{ fontSize: 14, opacity: 0.7 }}>Membros Ativos</div>
          <strong style={{ fontSize: 24, color: '#3b82f6' }}>{stats.membros_ativos}</strong>
        </div>

        <div className="card" style={{ cursor: 'pointer' }} onClick={() => navigate('/desportivo')}>
          <div style={{ fontSize: 14, opacity: 0.7 }}>Atletas Ativos</div>
          <strong style={{ fontSize: 24, color: '#10b981' }}>{stats.atletas_ativos}</strong>
        </div>

        <div className="card">
          <div style={{ fontSize: 14, opacity: 0.7 }}>Enc. Educação</div>
          <strong style={{ fontSize: 24, color: '#f59e0b' }}>
            {stats.encarregados_educacao || '-'}
          </strong>
        </div>

        <div className="card" style={{ cursor: 'pointer' }} onClick={() => navigate('/eventos')}>
          <div style={{ fontSize: 14, opacity: 0.7 }}>Eventos Próximos</div>
          <strong style={{ fontSize: 24, color: '#8b5cf6' }}>{stats.eventos_proximos}</strong>
        </div>

        <div className="card" style={{ cursor: 'pointer' }} onClick={() => navigate('/financeiro')}>
          <div style={{ fontSize: 14, opacity: 0.7 }}>Receitas do Mês</div>
          <strong style={{ fontSize: 24, color: '#059669' }}>
            {stats.receitas_mes > 0 ? formatCurrency(stats.receitas_mes) : '-'}
          </strong>
        </div>
      </div>

      {/* CONTEÚDO PRINCIPAL */}
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: '2fr 1fr',
          gap: 15,
          marginBottom: 15,
        }}
      >
        {/* PRÓXIMOS EVENTOS */}
        <div className="card">
          <h3 style={{ marginTop: 0 }}>Próximos Eventos</h3>

          {eventos.length === 0 ? (
            <div
              style={{
                background: 'var(--bg-page)',
                border: '1px solid var(--border-subtle)',
                borderRadius: 10,
                padding: 20,
                textAlign: 'center',
                opacity: 0.7,
              }}
            >
              Nenhum evento próximo
            </div>
          ) : (
            eventos.map((evento) => (
              <div
                key={evento.id}
                style={{
                  background: 'var(--bg-page)',
                  border: '1px solid var(--border-subtle)',
                  borderRadius: 10,
                  padding: 15,
                  marginBottom: 12,
                  cursor: 'pointer',
                  transition: 'all 0.2s',
                }}
                onClick={() => navigate('/eventos')}
                onMouseEnter={(e) => {
                  e.currentTarget.style.borderColor = '#3b82f6';
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.borderColor = 'var(--border-subtle)';
                }}
              >
                <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                  <span style={{ fontSize: 20 }}>{getTipoEventoBadge(evento.tipo)}</span>
                  <div style={{ flex: 1 }}>
                    <strong>{evento.titulo}</strong>
                    <div style={{ fontSize: 12, color: 'var(--text-muted)', marginTop: 4 }}>
                      📅 {formatDate(evento.data_inicio)}
                      {evento.local && ` • 📍 ${evento.local}`}
                    </div>
                  </div>
                </div>
              </div>
            ))
          )}

          <button
            className="btn-outline"
            style={{ width: '100%', marginTop: 8 }}
            onClick={() => navigate('/eventos')}
          >
            Ver Todos os Eventos
          </button>
        </div>

        {/* ATIVIDADE RECENTE */}
        <div className="card">
          <h3 style={{ marginTop: 0 }}>Atividade Recente</h3>

          {atividades.length === 0 ? (
            <div
              style={{
                background: 'var(--bg-page)',
                border: '1px solid var(--border-subtle)',
                borderRadius: 10,
                padding: 20,
                textAlign: 'center',
                opacity: 0.7,
                fontSize: 14,
              }}
            >
              Nenhuma atividade recente
            </div>
          ) : (
            atividades.map((atividade) => (
              <div
                key={atividade.id}
                style={{
                  display: 'flex',
                  justifyContent: 'space-between',
                  alignItems: 'center',
                  background: 'var(--bg-page)',
                  border: '1px solid var(--border-subtle)',
                  borderRadius: 10,
                  padding: '10px 12px',
                  marginBottom: 8,
                }}
              >
                <span style={{ fontSize: 13, flex: 1 }}>{atividade.descricao}</span>
                {atividade.valor !== undefined && (
                  <strong style={{ color: 'green', fontSize: 14 }}>
                    +{formatCurrency(atividade.valor)}
                  </strong>
                )}
              </div>
            ))
          )}
        </div>
      </div>

      {/* ACESSO RÁPIDO */}
      <div className="card">
        <h3 style={{ marginTop: 0 }}>Acesso Rápido</h3>

        <div
          style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(4, 1fr)',
            gap: 16,
          }}
        >
          {[
            { label: 'Membros', icon: '👥', path: '/membros' },
            { label: 'Desportivo', icon: '🏊', path: '/desportivo' },
            { label: 'Eventos', icon: '📅', path: '/eventos' },
            { label: 'Financeiro', icon: '💰', path: '/financeiro' },
          ].map((item) => (
            <div
              key={item.label}
              style={{
                border: '1px solid var(--border-subtle)',
                borderRadius: 10,
                padding: 20,
                textAlign: 'center',
                background: 'var(--bg-page)',
                cursor: 'pointer',
                transition: 'all 0.2s',
              }}
              onClick={() => navigate(item.path)}
              onMouseEnter={(e) => {
                e.currentTarget.style.borderColor = '#3b82f6';
                e.currentTarget.style.transform = 'translateY(-2px)';
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.borderColor = 'var(--border-subtle)';
                e.currentTarget.style.transform = 'translateY(0)';
              }}
            >
              <div style={{ fontSize: 32, marginBottom: 8 }}>{item.icon}</div>
              <div style={{ fontWeight: 500 }}>{item.label}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
