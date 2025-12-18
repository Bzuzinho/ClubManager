import { NavLink } from "react-router-dom";
import { mainNav, settingsNav } from "./navConfig";
import logo from "../assets/logo-bscn.png";

function initialsFromName(name: string) {
  const parts = name.trim().split(/\s+/).filter(Boolean);
  if (parts.length === 0) return "U";
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

export default function Sidebar() {
  // depois ligas isto ao utilizador logado
  const userName = "Administrador";
  const userEmail = "admin@bscn.pt";
  const initials = initialsFromName(userName);

  return (
    <aside className="spark-sidebar">
      {/* HEADER */}
      <div className="spark-sidebar__header">
        <img src={logo} alt="BSCN" className="spark-sidebar__logo" />
        <div>
          <div className="spark-sidebar__club">BSCN</div>
          <div className="spark-sidebar__subtitle">Gestão de Clube</div>
        </div>
      </div>

      {/* NAV */}
      <nav className="spark-sidebar__nav">
        {mainNav.map((item) => (
          <NavLink
            key={item.to}
            to={item.to}
            className={({ isActive }) =>
              "spark-sidebar__item" + (isActive ? " active" : "")
            }
          >
            <item.icon size={18} />
            <span>{item.label}</span>
          </NavLink>
        ))}
      </nav>

      {/* BOTTOM */}
      <div className="spark-sidebar__bottom">
        <div className="spark-sidebar__section">
          {settingsNav.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              className={({ isActive }) =>
                "spark-sidebar__item" + (isActive ? " active" : "")
              }
            >
              <item.icon size={18} />
              <span>{item.label}</span>
            </NavLink>
          ))}
        </div>

        <div className="spark-user">
          <div className="spark-user__avatar" aria-label="Avatar">
            {initials}
          </div>
          <div className="spark-user__meta">
            <div className="name">{userName}</div>
            <div className="email">{userEmail}</div>
          </div>
        </div>

        <button className="spark-logout" type="button">
          Sair
        </button>
      </div>
    </aside>
  );
}
