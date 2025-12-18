import { Outlet } from "react-router-dom";
import Sidebar from "./Sidebar";

export default function DashboardLayout() {
  return (
    <div className="spark-app">
      <Sidebar />

      <main className="spark-main">
        <Outlet />
      </main>
    </div>
  );
}
