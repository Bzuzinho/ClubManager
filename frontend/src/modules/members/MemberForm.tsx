import { useState, useEffect } from "react";
import api from "../../lib/api";

type Pessoa = {
  id: number;
  nome_completo: string;
  nif?: string;
  email?: string;
  telefone?: string;
  data_nascimento?: string;
};

type TipoMembro = {
  id: number;
  nome: string;
};

type Member = {
  id: number;
  pessoa_id: number;
  numero_socio?: string;
  estado: string;
  data_inscricao: string;
  observacoes?: string;
  tipos?: { id: number; nome: string }[];
};

type MemberFormProps = {
  member?: Member;
  onSave: () => void;
  onCancel: () => void;
};

export default function MemberForm({ member, onSave, onCancel }: MemberFormProps) {
  const [pessoas, setPessoas] = useState<Pessoa[]>([]);
  const [tiposMembro, setTiposMembro] = useState<TipoMembro[]>([]);
  const [loading, setLoading] = useState(false);
  
  const [formData, setFormData] = useState({
    pessoa_id: member?.pessoa_id || "",
    numero_socio: member?.numero_socio || "",
    estado: member?.estado || "Ativo",
    data_inscricao: member?.data_inscricao || new Date().toISOString().split("T")[0],
    observacoes: member?.observacoes || "",
    tipos: member?.tipos?.map((t: { id: number }) => t.id) || [],
  });

  const [errors, setErrors] = useState<Record<string, string[]>>({});

  useEffect(() => {
    // Carregar pessoas
    api.get("/api/pessoas").then((res) => setPessoas(res.data.data || res.data));
    
    // Carregar tipos de membro
    api.get("/api/tipos-membro").then((res) => setTiposMembro(res.data.data || res.data))
      .catch(() => {
        // Se não existir endpoint, usar tipos mock
        setTiposMembro([
          { id: 1, nome: "Atleta" },
          { id: 2, nome: "Encarregado de Educação" },
          { id: 3, nome: "Dirigente" },
          { id: 4, nome: "Treinador" },
        ]);
      });
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});

    try {
      const payload = {
        ...formData,
        tipos: formData.tipos.map((id: number) => ({ tipo_membro_id: id })),
      };

      if (member) {
        await api.put(`/api/membros/${member.id}`, payload);
      } else {
        await api.post("/api/membros", payload);
      }

      onSave();
    } catch (err: unknown) {
      const error = err as { response?: { data?: { errors?: Record<string, string[]>; message?: string } } };
      if (error.response?.data?.errors) {
        setErrors(error.response.data.errors);
      } else {
        setErrors({ general: [error.response?.data?.message || "Erro ao guardar membro"] });
      }
    } finally {
      setLoading(false);
    }
  };

  const handleTipoToggle = (tipoId: number) => {
    setFormData((prev) => ({
      ...prev,
      tipos: prev.tipos.includes(tipoId)
        ? prev.tipos.filter((id: number) => id !== tipoId)
        : [...prev.tipos, tipoId],
    }));
  };

  return (
    <form onSubmit={handleSubmit}>
      <div style={{ display: "grid", gap: 16 }}>
        {/* Pessoa */}
        <div>
          <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500 }}>
            Pessoa *
          </label>
          <select
            className="input"
            value={formData.pessoa_id}
            onChange={(e) => setFormData({ ...formData, pessoa_id: e.target.value })}
            required
            disabled={!!member}
            style={{ width: "100%", padding: 10, borderRadius: 8, border: "1px solid var(--border-subtle)" }}
          >
            <option value="">Selecione uma pessoa</option>
            {pessoas.map((p) => (
              <option key={p.id} value={p.id}>
                {p.nome_completo} {p.nif ? `(${p.nif})` : ""}
              </option>
            ))}
          </select>
          {errors.pessoa_id && (
            <span style={{ color: "var(--error-text, red)", fontSize: 12 }}>
              {errors.pessoa_id[0]}
            </span>
          )}
        </div>

        {/* Número Sócio */}
        <div>
          <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500 }}>
            Número de Sócio
          </label>
          <input
            type="text"
            className="input"
            value={formData.numero_socio}
            onChange={(e) => setFormData({ ...formData, numero_socio: e.target.value })}
            placeholder="Deixe vazio para gerar automaticamente"
            style={{ width: "100%", padding: 10, borderRadius: 8, border: "1px solid var(--border-subtle)" }}
          />
          {errors.numero_socio && (
            <span style={{ color: "var(--error-text, red)", fontSize: 12 }}>
              {errors.numero_socio[0]}
            </span>
          )}
        </div>

        {/* Estado */}
        <div>
          <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500 }}>
            Estado *
          </label>
          <select
            className="input"
            value={formData.estado}
            onChange={(e) => setFormData({ ...formData, estado: e.target.value })}
            required
            style={{ width: "100%", padding: 10, borderRadius: 8, border: "1px solid var(--border-subtle)" }}
          >
            <option value="Ativo">Ativo</option>
            <option value="Inativo">Inativo</option>
            <option value="Pendente">Pendente</option>
            <option value="Suspenso">Suspenso</option>
          </select>
        </div>

        {/* Data Inscrição */}
        <div>
          <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500 }}>
            Data de Inscrição *
          </label>
          <input
            type="date"
            className="input"
            value={formData.data_inscricao}
            onChange={(e) => setFormData({ ...formData, data_inscricao: e.target.value })}
            required
            style={{ width: "100%", padding: 10, borderRadius: 8, border: "1px solid var(--border-subtle)" }}
          />
        </div>

        {/* Tipos de Membro */}
        <div>
          <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500 }}>
            Tipos de Membro *
          </label>
          <div style={{ display: "flex", flexWrap: "wrap", gap: 8 }}>
            {tiposMembro.map((tipo) => (
              <label
                key={tipo.id}
                style={{
                  display: "flex",
                  alignItems: "center",
                  gap: 6,
                  padding: "8px 12px",
                  border: `2px solid ${
                    formData.tipos.includes(tipo.id)
                      ? "var(--primary, #3b82f6)"
                      : "var(--border-subtle)"
                  }`,
                  borderRadius: 8,
                  cursor: "pointer",
                  background: formData.tipos.includes(tipo.id)
                    ? "var(--primary-light, #dbeafe)"
                    : "transparent",
                }}
              >
                <input
                  type="checkbox"
                  checked={formData.tipos.includes(tipo.id)}
                  onChange={() => handleTipoToggle(tipo.id)}
                />
                {tipo.nome}
              </label>
            ))}
          </div>
          {errors.tipos && (
            <span style={{ color: "var(--error-text, red)", fontSize: 12 }}>
              {errors.tipos[0]}
            </span>
          )}
        </div>

        {/* Observações */}
        <div>
          <label style={{ display: "block", marginBottom: 6, fontSize: 14, fontWeight: 500 }}>
            Observações
          </label>
          <textarea
            className="input"
            value={formData.observacoes}
            onChange={(e) => setFormData({ ...formData, observacoes: e.target.value })}
            rows={3}
            style={{ width: "100%", padding: 10, borderRadius: 8, border: "1px solid var(--border-subtle)" }}
          />
        </div>

        {/* Erro geral */}
        {errors.general && (
          <div style={{ color: "var(--error-text, red)", fontSize: 14, fontWeight: 500 }}>
            {errors.general}
          </div>
        )}

        {/* Botões */}
        <div style={{ display: "flex", gap: 10, justifyContent: "flex-end", marginTop: 10 }}>
          <button type="button" className="btn outline" onClick={onCancel} disabled={loading}>
            Cancelar
          </button>
          <button type="submit" className="btn primary" disabled={loading}>
            {loading ? "A guardar..." : member ? "Atualizar" : "Criar"}
          </button>
        </div>
      </div>
    </form>
  );
}
