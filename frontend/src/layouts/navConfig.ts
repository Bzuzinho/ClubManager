import {
  Home,
  Users,
  Trophy,
  Calendar,
  Euro,
  Box,
  Handshake,
  Mail,
  Megaphone,
  Settings,
} from "lucide-react";

export const mainNav = [
  {
    label: "Início",
    to: "/",
    icon: Home,
    title: "Dashboard",
    subtitle: "Visão geral do clube",
  },
  {
    label: "Membros",
    to: "/membros",
    icon: Users,
    title: "Gestão de Membros",
    subtitle: "Fichas, perfis e encarregados",
  },
  {
    label: "Desportivo",
    to: "/desportivo",
    icon: Trophy,
    title: "Gestão Desportiva",
    subtitle: "Treinos, presenças e resultados",
  },
  {
    label: "Eventos",
    to: "/eventos",
    icon: Calendar,
    title: "Gestão de Eventos",
    subtitle: "Provas, inscrições e logística",
  },
  {
    label: "Financeiro",
    to: "/financeiro",
    icon: Euro,
    title: "Módulo Financeiro",
    subtitle: "Faturas, mensalidades e contas",
  },
  {
    label: "Inventário",
    to: "/inventario",
    icon: Box,
    title: "Inventário",
    subtitle: "Material e equipamentos",
  },
  {
    label: "Patrocínios",
    to: "/patrocinios",
    icon: Handshake,
    title: "Patrocínios",
    subtitle: "Apoios e parcerias",
  },
  {
    label: "Comunicação",
    to: "/comunicacao",
    icon: Mail,
    title: "Comunicação",
    subtitle: "Emails e notificações",
  },
  {
    label: "Marketing",
    to: "/marketing",
    icon: Megaphone,
    title: "Marketing",
    subtitle: "Campanhas e divulgação",
  },
];

export const settingsNav = [
  {
    label: "Configurações",
    to: "/configuracoes",
    icon: Settings,
    title: "Configurações",
    subtitle: "Sistema e permissões",
  },
];
