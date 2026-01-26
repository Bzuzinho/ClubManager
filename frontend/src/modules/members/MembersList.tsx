import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { Plus, Search, Filter } from "lucide-react";
import api from "../../lib/api";

interface Member {
  id: number;
  nome: string;
  email: string;
  numero_socio?: string;
  tipo_membro?: string;
  activo: boolean;
  telefone?: string;
}

export function MembersList() {
  const navigate = useNavigate();
  const [members, setMembers] = useState<Member[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadMembers();
  }, []);

  const loadMembers = async () => {
    try {
      setLoading(true);
      setError(null);
      
      console.log("Iniciando carregamento de membros...");
      const response = await api.get("/v2/membros");
      console.log("API Response:", response.data);
      console.log("Members data:", response.data.data);
      
      // Verificar se response.data é array direto ou está em response.data.data
      const membersData = Array.isArray(response.data) 
        ? response.data 
        : (response.data.data || []);
      
      console.log("Final members:", membersData);
      console.log("Total de membros:", membersData.length);
      
      setMembers(membersData);
    } catch (error: any) {
      console.error("Erro ao carregar membros:", error);
      console.error("Error details:", error.response?.data);
      
      // Se for erro de autenticação, mostrar mensagem específica
      if (error.response?.status === 401) {
        setError("Não autenticado. Por favor faça login.");
      } else {
        setError(error.response?.data?.message || error.message || "Erro ao carregar membros");
      }
      setMembers([]);
    } finally {
      console.log("Finalizando loading...");
      setLoading(false);
    }
  };

  const filteredMembers = members.filter((member) => {
    const searchLower = searchTerm.toLowerCase();
    return (
      member.nome?.toLowerCase().includes(searchLower) ||
      member.email?.toLowerCase().includes(searchLower) ||
      member.numero_socio?.toLowerCase().includes(searchLower) ||
      false
    );
  });

  const getInitials = (name: string) => {
    if (!name) return "??";
    return name
      .split(" ")
      .map((n) => n[0])
      .join("")
      .toUpperCase()
      .substring(0, 2);
  };

  return (
    <div className="page">
      <div className="page-header">
        <div>
          <h1 className="page-title">Membros</h1>
          <p className="page-subtitle">Gerir todos os membros do clube</p>
        </div>
        <button className="btn primary" onClick={() => navigate("/membros/novo")}>
          <Plus size={18} />
          Novo Membro
        </button>
      </div>

      <div className="card" style={{ marginBottom: "16px" }}>
        <div className="flex justify-between items-center gap-3" style={{ flexWrap: "wrap" }}>
          <div style={{ position: "relative", flex: 1, maxWidth: "400px", minWidth: "250px" }}>
            <Search
              size={20}
              style={{
                position: "absolute",
                left: "12px",
                top: "50%",
                transform: "translateY(-50%)",
                color: "#9ca3af",
                pointerEvents: "none"
              }}
            />
            <input
              type="text"
              className="input"
              placeholder="Pesquisar por nome, email ou número..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              style={{ paddingLeft: "40px", width: "100%" }}
            />
          </div>
          <button className="btn outline">
            <Filter size={18} />
            Filtros
          </button>
        </div>
      </div>

      <div className="card">
        {loading ? (
          <div style={{ padding: "40px", textAlign: "center" }}>
            <p className="text-secondary">A carregar membros...</p>
          </div>
        ) : error ? (
          <div style={{ padding: "40px", textAlign: "center" }}>
            <p style={{ color: "#ef4444", marginBottom: "16px" }}>{error}</p>
            <button className="btn primary" onClick={loadMembers}>
              Tentar novamente
            </button>
          </div>
        ) : filteredMembers.length === 0 ? (
          <div style={{ padding: "40px", textAlign: "center" }}>
            <p className="text-secondary">
              {searchTerm ? "Nenhum membro encontrado" : "Nenhum membro cadastrado"}
            </p>
          </div>
        ) : (
          <div style={{ overflowX: "auto" }}>
            <table className="table">
              <thead>
                <tr>
                  <th>Membro</th>
                  <th>Nº Sócio</th>
                  <th>Email</th>
                  <th>Telefone</th>
                  <th>Tipo</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                {filteredMembers.map((member) => (
                  <tr
                    key={member.id}
                    onClick={() => navigate(`/membros/${member.id}`)}
                    style={{ cursor: "pointer" }}
                  >
                    <td>
                      <div className="flex items-center gap-2">
                        <div className="avatar" style={{ backgroundColor: "#2563eb" }}>
                          {getInitials(member.nome)}
                        </div>
                        <span style={{ fontWeight: 600 }}>{member.nome || "Sem nome"}</span>
                      </div>
                    </td>
                    <td>
                      <span className="badge gray">
                        {member.numero_socio || "-"}
                      </span>
                    </td>
                    <td className="text-secondary">{member.email || "-"}</td>
                    <td className="text-secondary">{member.telefone || "-"}</td>
                    <td>
                      <span className="badge info">
                        {member.tipo_membro || "Sócio"}
                      </span>
                    </td>
                    <td>
                      {member.activo ? (
                        <span className="badge success">Ativo</span>
                      ) : (
                        <span className="badge danger">Inativo</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      <div className="card" style={{ marginTop: "16px" }}>
        <div className="flex justify-between items-center">
          <span className="text-secondary">
            {filteredMembers.length} {filteredMembers.length === 1 ? "membro" : "membros"}
            {searchTerm && ` encontrado(s)`}
          </span>
          <span className="text-secondary text-sm">
            Total no sistema: {members.length}
          </span>
        </div>
      </div>
    </div>
  );
}
