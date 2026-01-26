export interface Atleta {
  id: number;
  membro_id: number;
  membro?: {
    id: number;
    numero_socio: string;
    user: {
      name: string;
      email: string;
    };
  };
  escalao_id?: number;
  escalao?: {
    id: number;
    nome: string;
    ano_inicio: number;
    ano_fim: number;
  };
  equipas?: Equipa[];
  numero_atleta?: string;
  data_inscricao: string;
  estado: 'ativo' | 'inativo' | 'suspenso';
  observacoes?: string;
  created_at: string;
  updated_at: string;
}

export interface Equipa {
  id: number;
  nome: string;
  escalao_id?: number;
  escalao?: {
    nome: string;
  };
  temporada: string;
  tipo?: string;
  atletas?: Atleta[];
  atletas_count?: number;
  created_at: string;
  updated_at: string;
}

export interface Treino {
  id: number;
  equipa_id: number;
  equipa?: Equipa;
  data: string;
  hora_inicio: string;
  hora_fim: string;
  local?: string;
  tipo: 'treino' | 'jogo' | 'competicao';
  observacoes?: string;
  presencas?: Presenca[];
  presencas_count?: number;
  created_at: string;
  updated_at: string;
}

export interface Presenca {
  id: number;
  treino_id: number;
  atleta_id: number;
  atleta?: Atleta;
  presente: boolean;
  justificada: boolean;
  observacoes?: string;
  created_at: string;
  updated_at: string;
}

export interface EstatisticasPresenca {
  total_treinos: number;
  total_presencas: number;
  total_faltas: number;
  percentagem_presenca: number;
  por_atleta: {
    atleta_id: number;
    atleta_nome: string;
    presencas: number;
    faltas: number;
    percentagem: number;
  }[];
}

export interface EstatisticasAtleta {
  total_treinos: number;
  presencas: number;
  faltas: number;
  percentagem_presenca: number;
  equipas: string[];
}
