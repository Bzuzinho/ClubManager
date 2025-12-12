import { useEffect, useState } from "react";
import { api } from "../api";

export default function Dashboard() {
  const [me, setMe] = useState<any>(null);

  useEffect(() => {
    api.get("/me").then(r => setMe(r.data)).catch(() => setMe(null));
  }, []);

  return (
    <div>
      <div style={{ display:"flex", justifyContent:"space-between", alignItems:"center", marginBottom: 14 }}>
        <div>
          <div style={{ fontSize: 22, fontWeight: 900 }}>Dashboard</div>
          <div style={{ opacity: .7 }}>Base pronta. Agora construímos os módulos a sério.</div>
        </div>
        <div className="card" style={{ padding: 12 }}>
          <div style={{ fontSize: 12, opacity: .7 }}>Utilizador</div>
          <div style={{ fontWeight: 800 }}>{me?.name ?? "—"}</div>
          <div style={{ fontSize: 12, opacity: .7 }}>{me?.email ?? ""}</div>
        </div>
      </div>

      <div className="grid" style={{ gridTemplateColumns: "repeat(4, minmax(0, 1fr))" }}>
        <div className="card"><div style={{fontWeight:900}}>Membros</div><div style={{opacity:.7}}>Fichas, perfis, encarregados</div></div>
        <div className="card"><div style={{fontWeight:900}}>Financeiro</div><div style={{opacity:.7}}>Faturas, mensalidades, conta-corrente</div></div>
        <div className="card"><div style={{fontWeight:900}}>Desportivo</div><div style={{opacity:.7}}>Treinos, presenças, resultados</div></div>
        <div className="card"><div style={{fontWeight:900}}>Eventos</div><div style={{opacity:.7}}>Provas, inscrições, logística</div></div>
      </div>
    </div>
  );
}
