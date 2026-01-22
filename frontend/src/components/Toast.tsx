import { useEffect } from "react";
import { X } from "lucide-react";

type ToastProps = {
  message: string;
  type?: "success" | "error" | "info";
  onClose: () => void;
  duration?: number;
};

export default function Toast({ message, type = "info", onClose, duration = 3000 }: ToastProps) {
  useEffect(() => {
    const timer = setTimeout(onClose, duration);
    return () => clearTimeout(timer);
  }, [onClose, duration]);

  const bgColor = {
    success: "var(--success-bg, #10b981)",
    error: "var(--error-bg, #ef4444)",
    info: "var(--info-bg, #3b82f6)",
  }[type];

  return (
    <div
      style={{
        position: "fixed",
        top: 20,
        right: 20,
        background: bgColor,
        color: "white",
        padding: "12px 16px",
        borderRadius: 8,
        boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
        display: "flex",
        alignItems: "center",
        gap: 12,
        minWidth: 300,
        maxWidth: 500,
        zIndex: 9999,
        animation: "slideIn 0.3s ease",
      }}
    >
      <span style={{ flex: 1 }}>{message}</span>
      <button
        onClick={onClose}
        style={{
          background: "transparent",
          border: "none",
          color: "white",
          cursor: "pointer",
          padding: 4,
        }}
      >
        <X size={16} />
      </button>
    </div>
  );
}
