import { createBrowserRouter } from "react-router-dom";

import RequireAuth from "../auth/RequireAuth";
import AppLayout from "../layout/AppLayout";

import Login from "../pages/Login";
import Dashboard from "../pages/Dashboard";

// módulos (ajusta os paths se necessário)
import Members from "../modules/members";
import Sports from "../modules/sports";
import Events from "../modules/events";
import Financial from "../modules/financial";

export const router = createBrowserRouter([
  {
    path: "/login",
    element: <Login />,
  },
  {
    path: "/",
    element: (
      <RequireAuth>
        <AppLayout />
      </RequireAuth>
    ),
    children: [
      { index: true, element: <Dashboard /> },

      { path: "membros/*", element: <Members /> },
      { path: "desportivo/*", element: <Sports /> },
      { path: "eventos/*", element: <Events /> },
      { path: "financeiro/*", element: <Financial /> },
    ],
  },
]);
