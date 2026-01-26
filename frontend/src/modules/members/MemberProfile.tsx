import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { ChevronLeft, User, DollarSign, Activity } from "lucide-react";
import { PersonalTab } from "./components/PersonalTab";
import { FinancialTab } from "./components/FinancialTab";
import { SportsTab } from "./components/SportsTab";
import api from "../../lib/api";

interface Member {
  id: number;
  nome: string;
  email: string;
  numero_socio?: string;
  tipo_membro?: string;
  estado: string;
  activo: boolean;
  data_nascimento?: string;
  nif?: string;
  morada?: string;
  codigo_postal?: string;
  localidade?: string;
  pais?: string;
  telefone?: string;
  observacoes?: string;
}

type TabType = "personal" | "financial" | "sports";

export function MemberProfile() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [member, setMember] = useState<Member | null>(null);
  const [activeTab, setActiveTab] = useState<TabType>("personal");
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (id) {
      loadMember();
    }
  }, [id]);

  const loadMember = async () => {
    try {
      setLoading(true);
      const response = await api.get(`/v2/membros/${id}`);
      setMember(response.data.data);
    } catch (error) {
      console.error("Erro ao carregar membro:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="page">
        <div className="card">
          <p className="text-secondary">A carregar...</p>
        </div>
      </div>
    );
  }

  if (!member) {
    return (
      <div className="page">
        <div className="card">
          <p className="text-secondary">Membro não encontrado</p>
        </div>
      </div>
    );
  }

  const initials = member.nome
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .substring(0, 2);

  return (
    <div className="page">
      <div className="flex items-center gap-2 mb-3">
        <button className="btn outline icon" onClick={() => navigate("/membros")}>
          <ChevronLeft size={20} />
        </button>
        <h1 className="page-title" style={{ margin: 0 }}>Perfil do Membro</h1>
      </div>

      <div className="card" style={{ marginBottom: "24px" }}>
        <div className="flex items-center gap-3 mb-4" style={{ paddingBottom: "20px", borderBottom: "1px solid var(--border-color)" }}>
          <div className="avatar lg" style={{ backgroundColor: "#2563eb" }}>
            {initials}
          </div>
          <div style={{ flex: 1 }}>
            <h2 style={{ fontSize: "24px", fontWeight: 900, margin: "0 0 4px 0" }}>
              {member.nome}
            </h2>
            <div className="flex items-center gap-2">
              <span className="text-secondary" style={{ fontSize: "14px" }}>
                {member.email}
              </span>
              {member.numero_socio && (
                <>
                  <span className="text-tertiary">•</span>
                  <span className="text-secondary" style={{ fontSize: "14px" }}>
                    Nº Sócio: {member.numero_socio}
                  </span>
                </>
              )}
            </div>
          </div>
          <div>
            {member.activo ? (
              <span className="badge success">Ativo</span>
            ) : (
              <span className="badge danger">Inativo</span>
            )}
          </div>
        </div>

        <div className="tabs">
          <button
            className={`tab ${activeTab === "personal" ? "active" : ""}`}
            onClick={() => setActiveTab("personal")}
          >
            <User size={18} style={{ marginRight: "8px", verticalAlign: "middle" }} />
            Dados Pessoais
          </button>
          <button
            className={`tab ${activeTab === "sports" ? "active" : ""}`}
            onClick={() => setActiveTab("sports")}
          >
            <Activity size={18} style={{ marginRight: "8px", verticalAlign: "middle" }} />
            Dados Desportivos
          </button>
          <button
            className={`tab ${activeTab === "financial" ? "active" : ""}`}
            onClick={() => setActiveTab("financial")}
          >
            <DollarSign size={18} style={{ marginRight: "8px", verticalAlign: "middle" }} />
            Financeiro
          </button>
        </div>

        <div style={{ marginTop: "24px" }}>
          {activeTab === "personal" && <PersonalTab member={member} onUpdate={loadMember} />}
          {activeTab === "sports" && <SportsTab memberId={member.id} />}
          {activeTab === "financial" && <FinancialTab memberId={member.id} />}
        </div>
      </div>
    </div>
  );
}
