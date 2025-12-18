import { useLocation, useNavigate } from "react-router-dom";
import { mainNav, settingsNav } from "./navConfig";

export default function TopBar() {
  const location = useLocation();
  const navigate = useNavigate();

  const allNav = [...mainNav, ...settingsNav];

  const current =
    allNav.find(item =>
      location.pathname === "/"
        ? item.to === "/"
        : location.pathname.startsWith(item.to)
    ) ?? allNav[0];

  const logout = () => {
    localStorage.removeItem("token");
    navigate("/login");
  };

  return (
    <header className="topbar">
      <div className="topbar-left">
        <h1 className="page-title">{current.title}</h1>
        <span className="page-subtitle">{current.subtitle}</span>
      </div>

      <div className="topbar-right">
        <div className="user-info">
          <div className="avatar">A</div>
          <div className="user-text">
            <strong>Administrador</strong>
            <span>admin@bscn.pt</span>
          </div>
        </div>

        <button className="logout-btn" onClick={logout}>
          Sair
        </button>
      </div>
    </header>
  );
}
