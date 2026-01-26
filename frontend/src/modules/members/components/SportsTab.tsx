import { useState, useEffect } from "react";
import { Activity, Users, Calendar, Target } from "lucide-react";
import api from "../../../lib/api";

interface SportsData {
  atleta: boolean;
  numero?: number;
  posicao?: string;
  altura?: number;
  peso?: number;
  pe_dominante?: string;
}

interface Props {
  memberId: number;
}

export function SportsTab({ memberId }: Props) {
  const [sportsData, setSportsData] = useState<SportsData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadSportsData();
  }, [memberId]);

  const loadSportsData = async () => {
    try {
      setLoading(true);
      const response = await api.get(`/v2/membros/${memberId}`);
      setSportsData({
        atleta: response.data.data.atleta || false,
        numero: response.data.data.numero_atleta,
        posicao: response.data.data.posicao,
        altura: response.data.data.altura,
        peso: response.data.data.peso,
        pe_dominante: response.data.data.pe_dominante,
      });
    } catch (error) {
      console.error("Erro ao carregar dados desportivos:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="text-secondary">A carregar dados desportivos...</div>;
  }

  if (!sportsData?.atleta) {
    return (
      <div className="card" style={{ padding: "40px", textAlign: "center" }}>
        <Activity size={48} color="var(--gray-400)" style={{ margin: "0 auto 16px" }} />
        <h3 style={{ fontSize: "18px", fontWeight: 700, margin: "0 0 8px 0" }}>
          Este membro não é atleta
        </h3>
        <p className="text-secondary">
          Não existem dados desportivos associados a este membro.
        </p>
      </div>
    );
  }

  return (
    <div>
      {/* Dados Básicos */}
      <div className="card mb-3">
        <div className="card-header">
          <h3 className="card-title">Dados Básicos do Atleta</h3>
        </div>

        <div className="grid grid-4">
          <div>
            <label className="label">Número</label>
            <div style={{ fontSize: "24px", fontWeight: 900, color: "var(--primary)" }}>
              {sportsData.numero || "-"}
            </div>
          </div>

          <div>
            <label className="label">Posição</label>
            <div style={{ fontSize: "16px", fontWeight: 600 }}>
              {sportsData.posicao || "-"}
            </div>
          </div>

          <div>
            <label className="label">Altura (cm)</label>
            <div style={{ fontSize: "16px", fontWeight: 600 }}>
              {sportsData.altura || "-"}
            </div>
          </div>

          <div>
            <label className="label">Peso (kg)</label>
            <div style={{ fontSize: "16px", fontWeight: 600 }}>
              {sportsData.peso || "-"}
            </div>
          </div>
        </div>
      </div>

      {/* Secções em desenvolvimento */}
      <div className="grid grid-2">
        <div className="card" style={{ padding: "40px", textAlign: "center", border: "2px dashed var(--border-color)" }}>
          <Users size={32} color="var(--gray-400)" style={{ margin: "0 auto 12px" }} />
          <h4 style={{ fontSize: "14px", fontWeight: 700, margin: "0 0 4px 0" }}>Treinos</h4>
          <p className="text-secondary text-sm">Em desenvolvimento</p>
        </div>

        <div className="card" style={{ padding: "40px", textAlign: "center", border: "2px dashed var(--border-color)" }}>
          <Calendar size={32} color="var(--gray-400)" style={{ margin: "0 auto 12px" }} />
          <h4 style={{ fontSize: "14px", fontWeight: 700, margin: "0 0 4px 0" }}>Presenças</h4>
          <p className="text-secondary text-sm">Em desenvolvimento</p>
        </div>

        <div className="card" style={{ padding: "40px", textAlign: "center", border: "2px dashed var(--border-color)" }}>
          <Target size={32} color="var(--gray-400)" style={{ margin: "0 auto 12px" }} />
          <h4 style={{ fontSize: "14px", fontWeight: 700, margin: "0 0 4px 0" }}>Convocatórias</h4>
          <p className="text-secondary text-sm">Em desenvolvimento</p>
        </div>

        <div className="card" style={{ padding: "40px", textAlign: "center", border: "2px dashed var(--border-color)" }}>
          <Activity size={32} color="var(--gray-400)" style={{ margin: "0 auto 12px" }} />
          <h4 style={{ fontSize: "14px", fontWeight: 700, margin: "0 0 4px 0" }}>Resultados</h4>
          <p className="text-secondary text-sm">Em desenvolvimento</p>
        </div>
      </div>
    </div>
  );
}
