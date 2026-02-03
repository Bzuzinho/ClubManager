/**
 * Sports, training, and competition related types
 */

import type { EstadoPresenca, TipoPiscina } from './common';

// Season/Period types
export type TipoEpoca = 'principal' | 'secundaria' | 'verao' | 'preparacao' | 'pre_epoca';
export type EstadoEpoca = 'planeada' | 'em_curso' | 'concluida' | 'arquivada';
export type TipoMacrociclo = 'preparacao_geral' | 'preparacao_especifica' | 'competicao' | 'taper' | 'transicao';

// Training types
export type TipoTreino = 'aerobio' | 'sprint' | 'tecnica' | 'forca' | 'recuperacao' | 'misto';
export type ZonaIntensidade = 'Z1' | 'Z2' | 'Z3' | 'Z4' | 'Z5';
export type Estilo = 'crawl' | 'costas' | 'brucos' | 'mariposa' | 'estilos' | 'livres';

// Competition types
export type TipoCompeticao = 'oficial' | 'interna' | 'masters' | 'formacao' | 'outro';
export type GeneroProva = 'masculino' | 'feminino' | 'misto';
export type EstadoInscricao = 'inscrito' | 'confirmado' | 'desistiu';

export interface Epoca {
  id: string;
  nome: string;
  ano_temporada: string;
  data_inicio: string;
  data_fim: string;
  tipo: TipoEpoca;
  estado: EstadoEpoca;
  piscina_principal?: TipoPiscina;
  escaloes_abrangidos?: string[];
  descricao?: string;
  provas_alvo?: string[];
  volume_total_previsto?: number;
  volume_medio_semanal?: number;
  num_semanas_previsto?: number;
  num_competicoes_previstas?: number;
  objetivos_performance?: string;
  objetivos_tecnicos?: string;
  created_at?: string;
  updated_at?: string;
}

export interface Macrociclo {
  id: string;
  epoca_id: string;
  nome: string;
  tipo: TipoMacrociclo;
  data_inicio: string;
  data_fim: string;
  escalao?: string;
  created_at?: string;
  updated_at?: string;
}

export interface Mesociclo {
  id: string;
  macrociclo_id: string;
  nome: string;
  foco: string;
  data_inicio: string;
  data_fim: string;
  created_at?: string;
}

export interface Microciclo {
  id: string;
  mesociclo_id: string;
  semana: string;
  volume_previsto?: number;
  notas?: string;
  created_at?: string;
}

export interface Treino {
  id: string;
  numero_treino?: string;
  data: string;
  hora_inicio?: string;
  hora_fim?: string;
  local?: string;
  epoca_id?: string;
  microciclo_id?: string;
  grupo_escalao_id?: string;
  escaloes?: string[];
  tipo_treino: TipoTreino;
  volume_planeado_m?: number;
  notas_gerais?: string;
  descricao_treino?: string;
  criado_por?: string;
  created_at?: string;
  atualizado_em?: string;
  evento_id?: string;
}

export interface TreinoSerie {
  id: string;
  treino_id: string;
  ordem: number;
  descricao_texto: string;
  distancia_total_m: number;
  zona_intensidade?: ZonaIntensidade;
  estilo?: Estilo;
  repeticoes?: number;
  intervalo?: string;
  observacoes?: string;
  created_at?: string;
}

export interface TreinoAtleta {
  id: string;
  treino_id: string;
  user_id: string;
  presente: boolean;
  estado?: EstadoPresenca;
  volume_real_m?: number;
  rpe?: number;
  observacoes_tecnicas?: string;
  registado_por?: string;
  registado_em?: string;
  created_at?: string;
}

export interface Presenca {
  id: string;
  user_id: string;
  data: string;
  treino_id?: string;
  tipo: 'treino' | 'competicao' | 'reuniao' | 'estagio' | 'outro';
  justificacao?: string;
  presente: boolean;
  created_at?: string;
}

export interface Competicao {
  id: string;
  nome: string;
  local: string;
  data_inicio: string;
  data_fim?: string;
  tipo: TipoCompeticao;
  evento_id?: string;
  created_at?: string;
}

export interface Prova {
  id: string;
  competicao_id: string;
  estilo: Estilo;
  distancia_m: number;
  genero: GeneroProva;
  escalao_id?: string;
  ordem_prova?: number;
  created_at?: string;
}

export interface InscricaoProva {
  id: string;
  prova_id: string;
  user_id: string;
  estado: EstadoInscricao;
  valor_inscricao?: number;
  fatura_id?: string;
  movimento_id?: string;
  created_at?: string;
}

export interface Resultado {
  id: string;
  prova_id: string;
  user_id: string;
  tempo_oficial: number;
  posicao?: number;
  pontos_fina?: number;
  desclassificado: boolean;
  observacoes?: string;
  created_at?: string;
}

export interface ResultadoSplit {
  id: string;
  resultado_id: string;
  distancia_parcial_m: number;
  tempo_parcial: number;
  created_at?: string;
}
