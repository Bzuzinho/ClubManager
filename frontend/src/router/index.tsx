import { createBrowserRouter } from "react-router-dom";

import AppLayout from "../layouts/AppLayout";
import RequireAuth from "../auth/RequireAuth";

import Login from "../views/Login";
import Dashboard from "../views/Dashboard";

import Members from "../modules/members/Members";
import Financial from "../modules/financial/Financial";
import Sports from "../modules/sports/Sports";
import Events from "../modules/events/Events";

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
