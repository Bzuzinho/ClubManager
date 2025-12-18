import React from "react";
import ReactDOM from "react-dom/client";
import { RouterProvider } from "react-router-dom";
import { router } from "./router";

/* ⬇️ ISTO É O MAIS IMPORTANTE */
import "./styles/spark-tokens.css";
import "./styles/spark-base.css";
import "./styles/spark-layout.css";

ReactDOM.createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <RouterProvider router={router} />
  </React.StrictMode>
);
