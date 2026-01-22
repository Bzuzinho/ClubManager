import { useEffect, useState, useCallback } from "react";
import api from "../../lib/api";
import Modal from "../../components/Modal";
import Toast from "../../components/Toast";
import ConfirmDialog from "../../components/ConfirmDialog";
import MemberForm from "./MemberForm";
import MemberDetails from "./MemberDetails";

type Member = {
  id: number;
  numero_socio?: string;
  estado?: string;
  pessoa?: {
    nome_completo: string;
    email?: string;
  };
  tipos?: { id: number; nome: string }[];
};

type TipoMembro = {
  id: number;
  nome: string;
};

export default function Members() {
  const [members, setMembers] = useState<Member[]>([]);
  const [tiposMembro, setTiposMembro] = useState<TipoMembro[]>([]);
  const [loading, setLoading] = useState(true);
  
  // Modais
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [showDetailsModal, setShowDetailsModal] = useState(false);
  const [showDeleteDialog, setShowDeleteDialog] = useState(false);
  const [selectedMember, setSelectedMember] = useState<Member | null>(null);
  
  // Filtros
  const [searchTerm, setSearchTerm] = useState("");
  const [estadoFilter, setEstadoFilter] = useState("");
  const [tipoFilter, setTipoFilter] = useState("");
  
  // Toast
  const [toast, setToast] = useState<{ message: string; type: "success" | "error" | "info" } | null>(null);

  const showToast = (message: string, type: "success" | "error" | "info" = "info") => {
    setToast({ message, type });
  };

  const loadMembers = useCallback(() => {
    setLoading(true);
    const params = new URLSearchParams();
    if (searchTerm) params.append("search", searchTerm);
    if (estadoFilter) params.append("estado", estadoFilter);
    if (tipoFilter) params.append("tipo_membro_id", tipoFilter);

    api
      .get(`/api/membros?${params.toString()}`)
      .then((res) => {
        const data = res.data.data || res.data;
        setMembers(Array.isArray(data) ? data : []);
      })
      .catch(() => {
        showToast("Erro ao carregar membros", "error");
        setMembers([]);
      })
      .finally(() => setLoading(false));
  }, [searchTerm, estadoFilter, tipoFilter]);

  const loadTiposMembro = () => {
    api
      .get("/api/tipos-membro")
      .then((res) => setTiposMembro(res.data.data || res.data))
      .catch(() => {
        // Mock se não existir endpoint
        setTiposMembro([
          { id: 1, nome: "Atleta" },
          { id: 2, nome: "Encarregado de Educação" },
          { id: 3, nome: "Dirigente" },
          { id: 4, nome: "Treinador" },
        ]);
      });
  };

  useEffect(() => {
    loadMembers();
    loadTiposMembro();
  }, [loadMembers]);

  const handleCreate = () => {
    setSelectedMember(null);
    setShowCreateModal(true);
  };

  const handleEdit = (member: Member) => {
    setSelectedMember(member);
    setShowDetailsModal(false);
    setShowEditModal(true);
  };

  const handleView = (member: Member) => {
    setSelectedMember(member);
    setShowDetailsModal(true);
  };

  const handleDeleteClick = (member: Member) => {
    setSelectedMember(member);
    setShowDeleteDialog(true);
  };

  const handleDeleteConfirm = async () => {
    if (!selectedMember) return;

    try {
      await api.delete(`/api/membros/${selectedMember.id}`);
      showToast("Membro eliminado com sucesso", "success");
      loadMembers();
    } catch {
      showToast("Erro ao eliminar membro", "error");
    }
  };

  const handleSave = () => {
    setShowCreateModal(false);
    setShowEditModal(false);
    showToast(
      selectedMember ? "Membro atualizado com sucesso" : "Membro criado com sucesso",
      "success"
    );
    loadMembers();
  };

  const getInitials = (name: string) => {
    return name
      .split(" ")
      .slice(0, 2)
      .map((n) => n[0])
      .join("")
      .toUpperCase();
  };

  const getEstadoBadgeColor = (estado: string) => {
    switch (estado) {
      case "Ativo":
        return "#10b981";
      case "Inativo":
        return "#6b7280";
      case "Pendente":
        return "#f59e0b";
      case "Suspenso":
        return "#ef4444";
      default:
        return "#6b7280";
    }
  };

  return (
    <div className="page">
      {/* Toast */}
      {toast && (
        <Toast
          message={toast.message}
          type={toast.type}
          onClose={() => setToast(null)}
        />
      )}

      {/* HEADER */}
      <div className="members-header">
        <div>
          <h1 className="page-title">Gestão de Membros</h1>
          <p className="page-subtitle">{members.length} membros</p>
        </div>

        <div className="members-header-actions">
          <button className="btn outline">Importar</button>
          <button className="btn primary" onClick={handleCreate}>
            + Novo Membro
          </button>
        </div>
      </div>

      {/* FILTROS */}
      <div className="card members-filters">
        <input
          className="members-search"
          placeholder="Pesquisar por nome, nº sócio ou email..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
        />

        <select
          className="members-select"
          value={estadoFilter}
          onChange={(e) => setEstadoFilter(e.target.value)}
        >
          <option value="">Todos os Estados</option>
          <option value="Ativo">Ativo</option>
          <option value="Inativo">Inativo</option>
          <option value="Pendente">Pendente</option>
          <option value="Suspenso">Suspenso</option>
        </select>

        <select
          className="members-select"
          value={tipoFilter}
          onChange={(e) => setTipoFilter(e.target.value)}
        >
          <option value="">Todos os Tipos</option>
          {tiposMembro.map((tipo) => (
            <option key={tipo.id} value={tipo.id}>
              {tipo.nome}
            </option>
          ))}
        </select>
      </div>

      {/* LOADING */}
      {loading && <div className="card">A carregar membros…</div>}

      {/* EMPTY STATE */}
      {!loading && members.length === 0 && (
        <div className="card" style={{ textAlign: "center", padding: 40 }}>
          <p style={{ color: "var(--text-muted)", marginBottom: 16 }}>
            Nenhum membro encontrado
          </p>
          <button className="btn primary" onClick={handleCreate}>
            + Criar Primeiro Membro
          </button>
        </div>
      )}

      {/* GRID */}
      {!loading && members.length > 0 && (
        <div className="grid members-grid">
          {members.map((member) => (
            <div key={member.id} className="card member-card">
              {/* Avatar */}
              <div className="member-avatar">
                {getInitials(member.pessoa?.nome_completo || "?")}
              </div>

              {/* Info */}
              <div className="member-info" onClick={() => handleView(member)} style={{ cursor: "pointer" }}>
                <div className="member-name">
                  {member.pessoa?.nome_completo || "Sem nome"}
                </div>

                {member.numero_socio && (
                  <div className="member-number">Nº {member.numero_socio}</div>
                )}

                <div className="member-badges">
                  <span
                    className="badge"
                    style={{
                      background: getEstadoBadgeColor(member.estado || "Ativo"),
                      color: "white",
                    }}
                  >
                    {member.estado ?? "Ativo"}
                  </span>

                  {member.tipos?.map((tipo) => (
                    <span key={tipo.id} className="badge">
                      {tipo.nome}
                    </span>
                  ))}
                </div>
              </div>

              {/* Ações */}
              <div className="member-actions">
                <button
                  className="icon-btn"
                  onClick={() => handleDeleteClick(member)}
                  title="Eliminar"
                >
                  🗑
                </button>
                <button
                  className="icon-btn"
                  onClick={() => handleEdit(member)}
                  title="Editar"
                >
                  ✎
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* MODAL CRIAR */}
      <Modal
        isOpen={showCreateModal}
        onClose={() => setShowCreateModal(false)}
        title="Novo Membro"
        size="lg"
      >
        <MemberForm
          onSave={handleSave}
          onCancel={() => setShowCreateModal(false)}
        />
      </Modal>

      {/* MODAL EDITAR */}
      <Modal
        isOpen={showEditModal}
        onClose={() => setShowEditModal(false)}
        title="Editar Membro"
        size="lg"
      >
        <MemberForm
          member={selectedMember || undefined}
          onSave={handleSave}
          onCancel={() => setShowEditModal(false)}
        />
      </Modal>

      {/* MODAL DETALHES */}
      <MemberDetails
        memberId={selectedMember?.id || null}
        isOpen={showDetailsModal}
        onClose={() => setShowDetailsModal(false)}
        onEdit={() => handleEdit(selectedMember!)}
      />

      {/* DIALOG ELIMINAR */}
      <ConfirmDialog
        isOpen={showDeleteDialog}
        onClose={() => setShowDeleteDialog(false)}
        onConfirm={handleDeleteConfirm}
        title="Eliminar Membro"
        message={`Tem a certeza que deseja eliminar o membro "${selectedMember?.pessoa?.nome_completo}"? Esta ação não pode ser revertida.`}
        confirmText="Eliminar"
        type="danger"
      />
    </div>
  );
}
