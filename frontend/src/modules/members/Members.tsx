export default function Members() {
  return (
    <div>
      <div style={{ display:"flex", justifyContent:"space-between", alignItems:"center", marginBottom: 14 }}>
        <div>
          <div style={{ fontSize: 20, fontWeight: 900 }}>Membros</div>
          <div style={{ opacity: .7 }}>Cards/lista base (stub). Próximo passo: CRUD real.</div>
        </div>
        <button className="btn primary">Novo Membro</button>
      </div>

      <div className="grid" style={{ gridTemplateColumns: "repeat(3, minmax(0, 1fr))" }}>
        <div className="card">
          <div style={{ fontWeight: 900 }}>Ricardo Ferreira</div>
          <div style={{ opacity: .7 }}>Atleta</div>
        </div>
        <div className="card">
          <div style={{ fontWeight: 900 }}>Exemplo</div>
          <div style={{ opacity: .7 }}>Encarregado</div>
        </div>
      </div>
    </div>
  );
}
