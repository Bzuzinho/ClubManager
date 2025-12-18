import { useState } from "react";
import api from "../lib/api";

export default function Login() {
  const [email, setEmail] = useState("admin@admin.pt");
  const [password, setPassword] = useState("password");
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await api.post("/api/login", { email, password });
      localStorage.setItem("token", res.data.token);
      window.location.href = "/";
    } catch (e: any) {
      setError(e?.response?.data?.message ?? "Erro no login");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ height: "100vh", display: "grid", placeItems: "center" }}>
      <div className="card" style={{ width: 420 }}>
        <div style={{ fontSize: 18, fontWeight: 800, marginBottom: 10 }}>Entrar</div>

        <div style={{ display: "grid", gap: 10 }}>
          <label>
            <div style={{ fontSize: 12, opacity: .7, marginBottom: 6 }}>Email</div>
            <input value={email} onChange={e => setEmail(e.target.value)} style={{ width: "100%", padding: 10, borderRadius: 10, border: "1px solid #cbd5e1" }} />
          </label>

          <label>
            <div style={{ fontSize: 12, opacity: .7, marginBottom: 6 }}>Password</div>
            <input type="password" value={password} onChange={e => setPassword(e.target.value)} style={{ width: "100%", padding: 10, borderRadius: 10, border: "1px solid #cbd5e1" }} />
          </label>

          {error && <div style={{ color: "#b91c1c", fontWeight: 600 }}>{error}</div>}

          <button className="btn primary" onClick={submit} disabled={loading}>
            {loading ? "A entrar..." : "Entrar"}
          </button>

          <div style={{ fontSize: 12, opacity: .7 }}>
            Se falhar: backend parado / migrations por fazer / DB inacessível / CORS.
          </div>
        </div>
      </div>
    </div>
  );
}
