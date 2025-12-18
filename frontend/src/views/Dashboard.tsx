export default function Dashboard() {
  return (
    <div>
      {/* TÍTULO */}
      <div style={{ marginBottom: 20 }}>
        <h1 style={{ margin: 0 }}>Dashboard</h1>
        <p style={{ margin: "4px 0 0", color: "var(--text-muted)" }}>
          Visão geral do clube
        </p>
      </div>

      {/* KPI CARDS */}
      <div
        style={{
          display: "grid",
          gridTemplateColumns: "repeat(5, minmax(0, 1fr))",
          gap: 16,
          marginBottom: 24,
        }}
      >
        <div className="card">
          <div>Membros Ativos</div>
          <strong style={{ fontSize: 20 }}>97</strong>
        </div>

        <div className="card">
          <div>Atletas Ativos</div>
          <strong style={{ fontSize: 20 }}>71</strong>
        </div>

        <div className="card">
          <div>Enc. Educação</div>
          <strong style={{ fontSize: 20 }}>28</strong>
        </div>

        <div className="card">
          <div>Eventos Próximos</div>
          <strong style={{ fontSize: 20 }}>1</strong>
        </div>

        <div className="card">
          <div>Receitas do Mês</div>
          <strong style={{ fontSize: 20 }}>€85.00</strong>
        </div>
      </div>

      {/* CONTEÚDO PRINCIPAL */}
      <div
        style={{
          display: "grid",
          gridTemplateColumns: "2fr 1fr",
          gap: 24,
          marginBottom: 24,
        }}
      >
        {/* PRÓXIMOS EVENTOS */}
        <div className="card">
          <h3>Próximos Eventos</h3>

          <div
            style={{
              background: "var(--bg-page)",
              border: "1px solid var(--border-subtle)",
              borderRadius: 10,
              padding: 12,
              marginBottom: 12,
            }}
          >
            <strong>Prova de Teste</strong>
            <div style={{ fontSize: 12, color: "var(--text-muted)" }}>
              20/12/2025
            </div>
          </div>

          <button className="btn-outline" style={{ width: "100%" }}>
            Ver Todos os Eventos
          </button>
        </div>

        {/* ATIVIDADE RECENTE */}
        <div className="card">
          <h3>Atividade Recente</h3>

          {[
            ["Alexandre", "+€35.00"],
            ["Ana", "+€25.00"],
            ["André", "+€25.00"],
          ].map((item, i) => (
            <div
              key={i}
              style={{
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                background: "var(--bg-page)",
                border: "1px solid var(--border-subtle)",
                borderRadius: 10,
                padding: "10px 12px",
                marginBottom: 8,
              }}
            >
              <span>Pagamento mensalidade – {item[0]}</span>
              <strong style={{ color: "green" }}>{item[1]}</strong>
            </div>
          ))}
        </div>
      </div>

      {/* ACESSO RÁPIDO */}
      <div className="card">
        <h3>Acesso Rápido</h3>

        <div
          style={{
            display: "grid",
            gridTemplateColumns: "repeat(4, 1fr)",
            gap: 16,
          }}
        >
          {["Membros", "Desportivo", "Eventos", "Financeiro"].map(item => (
            <div
              key={item}
              style={{
                border: "1px solid var(--border-subtle)",
                borderRadius: 10,
                padding: 14,
                textAlign: "center",
                background: "var(--bg-page)",
              }}
            >
              {item}
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
