import { useEffect, useState } from "react";

type Member = {
  id: number;
  name: string;
  numero_socio?: string;
  estado?: string;
  roles?: { name: string }[];
};

export default function Members() {
  const [members, setMembers] = useState<Member[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("http://localhost:8000/api/members")
      .then(res => res.json())
      .then(data => {
        setMembers(data);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  return (
    <div className="page">

      {/* HEADER */}
      <div className="members-header">
        <div>
          <h1 className="page-title">Gestão de Membros</h1>
          <p className="page-subtitle">
            {members.length} membros
          </p>
        </div>

        <div className="members-header-actions">
          <button className="btn outline">Importar</button>
          <button className="btn primary">+ Novo Membro</button>
        </div>
      </div>

      {/* FILTROS */}
      <div className="card members-filters">
        <input
          className="members-search"
          placeholder="Pesquisar por nome, nº sócio ou email..."
        />

        <select className="members-select">
          <option>Todos os Estados</option>
          <option>Ativo</option>
          <option>Inativo</option>
        </select>

        <select className="members-select">
          <option>Todos os Tipos</option>
          <option>Atleta</option>
          <option>Encarregado</option>
          <option>Dirigente</option>
        </select>
      </div>

      {/* LOADING */}
      {loading && <div className="card">A carregar membros…</div>}

      {/* GRID */}
      {!loading && (
        <div className="grid members-grid">
          {members.map(member => (
            <div key={member.id} className="card member-card">

              {/* Avatar */}
              <div className="member-avatar">
                {member.name
                  .split(" ")
                  .slice(0, 2)
                  .map(n => n[0])
                  .join("")}
              </div>

              {/* Info */}
              <div className="member-info">
                <div className="member-name">{member.name}</div>

                {member.numero_socio && (
                  <div className="member-number">
                    Nº {member.numero_socio}
                  </div>
                )}

                <div className="member-badges">
                  <span className="badge success">
                    {member.estado ?? "Ativo"}
                  </span>

                  {member.roles?.map(role => (
                    <span key={role.name} className="badge">
                      {role.name}
                    </span>
                  ))}
                </div>
              </div>

              {/* Ações */}
              <div className="member-actions">
                <button className="icon-btn">🗑</button>
                <button className="icon-btn">✎</button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
