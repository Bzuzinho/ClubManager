export default function Financial() {
  return (
    <div>
      <div style={{ display:"flex", justifyContent:"space-between", alignItems:"center", marginBottom: 14 }}>
        <div>
          <div style={{ fontSize: 20, fontWeight: 900 }}>Financeiro</div>
          <div style={{ opacity: .7 }}>Tabela base (stub). Próximo passo: faturas + itens.</div>
        </div>
        <div style={{ display:"flex", gap: 10 }}>
          <button className="btn">Gerar Faturas</button>
          <button className="btn primary">Criar Fatura</button>
        </div>
      </div>

      <div className="card">
        <div style={{ fontWeight: 900, marginBottom: 10 }}>Faturas</div>
        <table style={{ width:"100%", borderCollapse:"collapse" }}>
          <thead>
            <tr style={{ textAlign:"left", opacity:.7 }}>
              <th style={{ padding: 8 }}>Nº</th>
              <th style={{ padding: 8 }}>Cliente</th>
              <th style={{ padding: 8 }}>Mês</th>
              <th style={{ padding: 8 }}>Estado</th>
              <th style={{ padding: 8, textAlign:"right" }}>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr style={{ borderTop:"1px solid #e2e8f0" }}>
              <td style={{ padding: 8 }}>#1</td>
              <td style={{ padding: 8 }}>Admin</td>
              <td style={{ padding: 8 }}>2025-12</td>
              <td style={{ padding: 8 }}>Pendente</td>
              <td style={{ padding: 8, textAlign:"right" }}>25,00€</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  );
}
