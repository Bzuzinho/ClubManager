import { createBrowserRouter, Navigate } from "react-router-dom";
import DashboardLayout from "../layouts/DashboardLayout";
import Login from "../views/Login";
import Dashboard from "../views/Dashboard";
import Members from "../modules/members/Members";
import Financial from "../modules/financial/Financial";
import Sports from "../modules/sports/Sports";
import Events from "../modules/events/Events";

function requireAuth(element: JSX.Element) {
  const token = localStorage.getItem("token");
  return token ? element : <Navigate to="/login" replace />;
}

export const router = createBrowserRouter([
  { path: "/login", element: <Login /> },
  {
    path: "/",
    element: requireAuth(<DashboardLayout />),
    children: [
      { index: true, element: <Dashboard /> },
      { path: "members", element: <Members /> },
      { path: "financial", element: <Financial /> },
      { path: "sports", element: <Sports /> },
      { path: "events", element: <Events /> },
    ],
  },
]);
