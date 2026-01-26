export interface Evento {
  id: number;
  titulo: string;
  descricao?: string;
  tipo: 'prova' | 'competicao' | 'torneio' | 'social' | 'outro';
  data_inicio: string;
  data_fim?: string;
  local?: string;
  capacidade_maxima?: number;
  inscricoes_abertas: boolean;
  data_limite_inscricao?: string;
  valor_inscricao?: number;
  observacoes?: string;
  inscricoes?: Inscricao[];
  inscricoes_count?: number;
  created_at: string;
  updated_at: string;
}

export interface Inscricao {
  id: number;
  evento_id: number;
  evento?: Evento;
  membro_id: number;
  membro?: {
    id: number;
    numero_socio: string;
    user: {
      name: string;
      email: string;
    };
  };
  atleta_id?: number;
  atleta?: {
    id: number;
    numero_atleta?: string;
  };
  estado: 'pendente' | 'confirmada' | 'cancelada';
  data_inscricao: string;
  valor_pago?: number;
  observacoes?: string;
  created_at: string;
  updated_at: string;
}
