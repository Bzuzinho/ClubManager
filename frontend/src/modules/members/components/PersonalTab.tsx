import { useState } from "react";
import { Save, X, Edit2 } from "lucide-react";
import api from "../../../lib/api";

interface Member {
  id: number;
  nome: string;
  email: string;
  data_nascimento?: string;
  nif?: string;
  morada?: string;
  codigo_postal?: string;
  localidade?: string;
  pais?: string;
  telefone?: string;
  observacoes?: string;
}

interface Props {
  member: Member;
  onUpdate: () => void;
}

export function PersonalTab({ member, onUpdate }: Props) {
  const [isEditing, setIsEditing] = useState(false);
  const [formData, setFormData] = useState<Member>(member);
  const [saving, setSaving] = useState(false);

  const handleChange = (field: keyof Member, value: string) => {
    setFormData({ ...formData, [field]: value });
  };

  const handleSave = async () => {
    try {
      setSaving(true);
      await api.put(`/v2/membros/${member.id}`, formData);
      setIsEditing(false);
      onUpdate();
      showToast("Dados atualizados com sucesso!", "success");
    } catch (error) {
      console.error("Erro ao atualizar:", error);
      showToast("Erro ao atualizar dados", "error");
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    setFormData(member);
    setIsEditing(false);
  };

  const showToast = (message: string, type: "success" | "error") => {
    // Toast simples com alert por enquanto
    alert(message);
  };

  return (
    <div>
      {/* Botões de ação */}
      <div className="flex justify-between items-center mb-3">
        <h3 style={{ fontSize: "16px", fontWeight: 700, margin: 0 }}>Informações Pessoais</h3>
        <div className="flex gap-2">
          {!isEditing ? (
            <button className="btn primary" onClick={() => setIsEditing(true)}>
              <Edit2 size={16} />
              Editar
            </button>
          ) : (
            <>
              <button
                className="btn success"
                onClick={handleSave}
                disabled={saving}
              >
                <Save size={16} />
                {saving ? "A guardar..." : "Guardar"}
              </button>
              <button
                className="btn outline"
                onClick={handleCancel}
                disabled={saving}
              >
                <X size={16} />
                Cancelar
              </button>
            </>
          )}
        </div>
      </div>

      {/* Formulário em Grid */}
      <div className="grid grid-2">
        {/* Nome Completo */}
        <div className="form-group">
          <label className="label">Nome Completo *</label>
          <input
            type="text"
            className="input"
            value={formData.nome}
            onChange={(e) => handleChange("nome", e.target.value)}
            disabled={!isEditing}
            required
          />
        </div>

        {/* NIF */}
        <div className="form-group">
          <label className="label">NIF</label>
          <input
            type="text"
            className="input"
            value={formData.nif || ""}
            onChange={(e) => handleChange("nif", e.target.value)}
            disabled={!isEditing}
            placeholder="000000000"
          />
        </div>

        {/* Email */}
        <div className="form-group">
          <label className="label">Email *</label>
          <input
            type="email"
            className="input"
            value={formData.email}
            onChange={(e) => handleChange("email", e.target.value)}
            disabled={!isEditing}
            required
          />
        </div>

        {/* Data de Nascimento */}
        <div className="form-group">
          <label className="label">Data de Nascimento</label>
          <input
            type="date"
            className="input"
            value={formData.data_nascimento || ""}
            onChange={(e) => handleChange("data_nascimento", e.target.value)}
            disabled={!isEditing}
          />
        </div>

        {/* Telefone */}
        <div className="form-group">
          <label className="label">Telefone</label>
          <input
            type="tel"
            className="input"
            value={formData.telefone || ""}
            onChange={(e) => handleChange("telefone", e.target.value)}
            disabled={!isEditing}
            placeholder="+351 000 000 000"
          />
        </div>

        {/* País */}
        <div className="form-group">
          <label className="label">País</label>
          <input
            type="text"
            className="input"
            value={formData.pais || ""}
            onChange={(e) => handleChange("pais", e.target.value)}
            disabled={!isEditing}
            placeholder="Portugal"
          />
        </div>

        {/* Morada (Span 2 columns) */}
        <div className="form-group" style={{ gridColumn: "span 2" }}>
          <label className="label">Morada</label>
          <input
            type="text"
            className="input"
            value={formData.morada || ""}
            onChange={(e) => handleChange("morada", e.target.value)}
            disabled={!isEditing}
            placeholder="Rua, número, andar"
          />
        </div>

        {/* Código Postal */}
        <div className="form-group">
          <label className="label">Código Postal</label>
          <input
            type="text"
            className="input"
            value={formData.codigo_postal || ""}
            onChange={(e) => handleChange("codigo_postal", e.target.value)}
            disabled={!isEditing}
            placeholder="0000-000"
          />
        </div>

        {/* Localidade */}
        <div className="form-group">
          <label className="label">Localidade</label>
          <input
            type="text"
            className="input"
            value={formData.localidade || ""}
            onChange={(e) => handleChange("localidade", e.target.value)}
            disabled={!isEditing}
          />
        </div>

        {/* Observações (Span 2 columns) */}
        <div className="form-group" style={{ gridColumn: "span 2" }}>
          <label className="label">Observações</label>
          <textarea
            className="input"
            rows={4}
            value={formData.observacoes || ""}
            onChange={(e) => handleChange("observacoes", e.target.value)}
            disabled={!isEditing}
            placeholder="Informações adicionais..."
          />
        </div>
      </div>
    </div>
  );
}
