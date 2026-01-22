import { useEffect, useState } from "react";
import Modal from "../../components/Modal";
import api from "../../lib/api";

type MemberDetailsProps = {
  memberId: number | null;
  isOpen: boolean;
  onClose: () => void;
  onEdit: () => void;
};

export default function MemberDetails({ memberId, isOpen, onClose, onEdit }: MemberDetailsProps) {
  const [member, setMember] = useState<{
    id: number;
    numero_socio?: string;
    estado: string;
    data_inscricao: string;
    observacoes?: string;
    pessoa?: {
      nome_completo: string;
      nif?: string;
      email?: string;
      telefone?: string;
      data_nascimento?: string;
      nacionalidade?: string;
    };
    tipos?: { id: number; nome: string }[];
    atleta?: {
      numero_camisola?: number;
      posicao_principal?: string;
      pe_dominante?: string;
      altura?: number;
      peso?: number;
    };
  } | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (memberId && isOpen) {
      setLoading(true);
      api
        .get(`/api/membros/${memberId}`)
        .then((res) => setMember(res.data.data || res.data))
        .catch(() => setMember(null))
        .finally(() => setLoading(false));
    }
  }, [memberId, isOpen]);

  if (!isOpen) return null;

  return (
    <Modal isOpen={isOpen} onClose={onClose} title="Detalhes do Membro" size="lg">
      {loading && <div>A carregar...</div>}

      {!loading && member && (
        <div style={{ display: "grid", gap: 20 }}>
          {/* Informação Pessoal */}
          <div>
            <h3 style={{ fontSize: 16, fontWeight: 600, marginBottom: 12 }}>
              Informação Pessoal
            </h3>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Nome Completo
                </div>
                <div style={{ fontWeight: 500 }}>{member.pessoa?.nome_completo || "-"}</div>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  NIF
                </div>
                <div style={{ fontWeight: 500 }}>{member.pessoa?.nif || "-"}</div>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Email
                </div>
                <div style={{ fontWeight: 500 }}>{member.pessoa?.email || "-"}</div>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Telefone
                </div>
                <div style={{ fontWeight: 500 }}>{member.pessoa?.telefone || "-"}</div>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Data de Nascimento
                </div>
                <div style={{ fontWeight: 500 }}>
                  {member.pessoa?.data_nascimento
                    ? new Date(member.pessoa.data_nascimento).toLocaleDateString("pt-PT")
                    : "-"}
                </div>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Nacionalidade
                </div>
                <div style={{ fontWeight: 500 }}>{member.pessoa?.nacionalidade || "-"}</div>
              </div>
            </div>
          </div>

          {/* Informação de Membro */}
          <div>
            <h3 style={{ fontSize: 16, fontWeight: 600, marginBottom: 12 }}>
              Informação de Membro
            </h3>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Número de Sócio
                </div>
                <div style={{ fontWeight: 500 }}>{member.numero_socio || "-"}</div>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Estado
                </div>
                <span
                  className="badge"
                  style={{
                    background:
                      member.estado === "Ativo"
                        ? "#10b981"
                        : member.estado === "Inativo"
                        ? "#6b7280"
                        : "#f59e0b",
                    color: "white",
                    padding: "4px 8px",
                    borderRadius: 4,
                    fontSize: 12,
                  }}
                >
                  {member.estado}
                </span>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Data de Inscrição
                </div>
                <div style={{ fontWeight: 500 }}>
                  {member.data_inscricao
                    ? new Date(member.data_inscricao).toLocaleDateString("pt-PT")
                    : "-"}
                </div>
              </div>
              <div>
                <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                  Tipos de Membro
                </div>
                <div style={{ display: "flex", gap: 6, flexWrap: "wrap" }}>
                  {member.tipos?.map((tipo: { id: number; nome: string }) => (
                    <span
                      key={tipo.id}
                      className="badge"
                      style={{
                        background: "var(--bg-page)",
                        border: "1px solid var(--border-subtle)",
                        padding: "4px 8px",
                        borderRadius: 4,
                        fontSize: 12,
                      }}
                    >
                      {tipo.nome}
                    </span>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {/* Se for Atleta */}
          {member.atleta && (
            <div>
              <h3 style={{ fontSize: 16, fontWeight: 600, marginBottom: 12 }}>
                Informação Desportiva
              </h3>
              <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
                <div>
                  <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                    Número de Camisola
                  </div>
                  <div style={{ fontWeight: 500 }}>{member.atleta.numero_camisola || "-"}</div>
                </div>
                <div>
                  <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                    Posição Principal
                  </div>
                  <div style={{ fontWeight: 500 }}>{member.atleta.posicao_principal || "-"}</div>
                </div>
                <div>
                  <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                    Pé Dominante
                  </div>
                  <div style={{ fontWeight: 500 }}>{member.atleta.pe_dominante || "-"}</div>
                </div>
                <div>
                  <div style={{ fontSize: 12, color: "var(--text-muted)", marginBottom: 4 }}>
                    Altura / Peso
                  </div>
                  <div style={{ fontWeight: 500 }}>
                    {member.atleta.altura ? `${member.atleta.altura}cm` : "-"} /{" "}
                    {member.atleta.peso ? `${member.atleta.peso}kg` : "-"}
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Observações */}
          {member.observacoes && (
            <div>
              <h3 style={{ fontSize: 16, fontWeight: 600, marginBottom: 12 }}>Observações</h3>
              <div
                style={{
                  background: "var(--bg-page)",
                  padding: 12,
                  borderRadius: 8,
                  fontSize: 14,
                }}
              >
                {member.observacoes}
              </div>
            </div>
          )}

          {/* Botões */}
          <div style={{ display: "flex", gap: 10, justifyContent: "flex-end", marginTop: 10 }}>
            <button className="btn outline" onClick={onClose}>
              Fechar
            </button>
            <button className="btn primary" onClick={onEdit}>
              Editar
            </button>
          </div>
        </div>
      )}

      {!loading && !member && (
        <div style={{ textAlign: "center", padding: 20, color: "var(--text-muted)" }}>
          Não foi possível carregar os detalhes do membro.
        </div>
      )}
    </Modal>
  );
}
