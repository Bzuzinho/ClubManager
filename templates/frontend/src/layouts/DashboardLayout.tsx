import { Outlet, NavLink, useNavigate } from "react-router-dom";

const linkBase: React.CSSProperties = {
  display: "block",
  padding: "10px 12px",
  borderRadius: 12,
  opacity: 0.9,
};

export default function DashboardLayout() {
  const nav = useNavigate();

  const logout = () => {
    localStorage.removeItem("token");
    nav("/login");
  };

  return (
    <div style={{ display: "flex", height: "100vh" }}>
      <aside style={{ width: 260, background: "#0f172a", color: "white", padding: 16 }}>
        <div style={{ fontWeight: 800, fontSize: 18, marginBottom: 16 }}>ClubManager</div>

        <nav style={{ display: "grid", gap: 8 }}>
          <NavLink to="/" end style={({ isActive }) => ({ ...linkBase, background: isActive ? "rgba(255,255,255,.12)" : "transparent" })}>
            Dashboard
          </NavLink>
          <NavLink to="/members" style={({ isActive }) => ({ ...linkBase, background: isActive ? "rgba(255,255,255,.12)" : "transparent" })}>
            Membros
          </NavLink>
          <NavLink to="/financial" style={({ isActive }) => ({ ...linkBase, background: isActive ? "rgba(255,255,255,.12)" : "transparent" })}>
            Financeiro
          </NavLink>
          <NavLink to="/sports" style={({ isActive }) => ({ ...linkBase, background: isActive ? "rgba(255,255,255,.12)" : "transparent" })}>
            Desportivo
          </NavLink>
          <NavLink to="/events" style={({ isActive }) => ({ ...linkBase, background: isActive ? "rgba(255,255,255,.12)" : "transparent" })}>
            Eventos
          </NavLink>
        </nav>

        <div style={{ marginTop: 16 }}>
          <button className="btn" onClick={logout} style={{ width: "100%" }}>Sair</button>
        </div>
      </aside>

      <main style={{ flex: 1, padding: 20, overflow: "auto" }}>
        <Outlet />
      </main>
    </div>
  );
}
