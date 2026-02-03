/**
 * Event and attendance related types
 */

import type { EstadoPresenca, TipoPiscina } from './common';

export type EventoTipo = 'prova' | 'estagio' | 'reuniao' | 'evento_interno' | 'treino' | 'outro';
export type EventoVisibilidade = 'privado' | 'restrito' | 'publico';
export type EventoEstado = 'rascunho' | 'agendado' | 'em_curso' | 'concluido' | 'cancelado';

export interface EventoTipoConfig {
  id: string;
  nome: string;
  cor: string;
  icon: string;
  ativo: boolean;
  gera_taxa: boolean;
  requer_convocatoria: boolean;
  requer_transporte: boolean;
  visibilidade_default: EventoVisibilidade;
  created_at: string;
}

export interface Event {
  id: string;
  titulo: string;
  descricao: string;
  data_inicio: string;
  hora_inicio?: string;
  data_fim?: string;
  hora_fim?: string;
  local?: string;
  local_detalhes?: string;
  tipo: EventoTipo;
  tipo_config_id?: string;
  tipo_piscina?: TipoPiscina;
  visibilidade?: EventoVisibilidade;
  escaloes_elegiveis?: string[];
  transporte_necessario?: boolean;
  transporte_detalhes?: string;
  hora_partida?: string;
  local_partida?: string;
  taxa_inscricao?: number;
  custo_inscricao_por_prova?: number;
  custo_inscricao_por_salto?: number;
  custo_inscricao_estafeta?: number;
  centro_custo_id?: string;
  observacoes?: string;
  convocatoria_ficheiro?: string;
  regulamento_ficheiro?: string;
  estado: EventoEstado;
  criado_por: string;
  criado_em: string;
  atualizado_em?: string;
  recorrente?: boolean;
  recorrencia_data_inicio?: string;
  recorrencia_data_fim?: string;
  recorrencia_dias_semana?: number[];
  evento_pai_id?: string;
}

export interface EventoConvocatoria {
  id: string;
  evento_id: string;
  user_id: string;
  data_convocatoria: string;
  estado_confirmacao: 'pendente' | 'confirmado' | 'recusado';
  data_resposta?: string;
  justificacao?: string;
  observacoes?: string;
  transporte_clube?: boolean;
}

export interface ConvocatoriaGrupo {
  id: string;
  evento_id: string;
  data_criacao: string;
  criado_por: string;
  atletas_ids: string[];
  hora_encontro?: string;
  local_encontro?: string;
  observacoes?: string;
  tipo_custo: 'por_salto' | 'por_atleta';
  valor_por_salto?: number;
  valor_por_estafeta?: number;
  valor_inscricao_unitaria?: number;
  valor_inscricao_calculado?: number;
  movimento_id?: string;
}

export interface ConvocatoriaAtleta {
  convocatoria_grupo_id: string;
  atleta_id: string;
  provas: string[];
  presente: boolean;
  confirmado: boolean;
}

export interface ResultadoProva {
  id: string;
  atleta_id: string;
  evento_id: string;
  evento_nome?: string;
  prova: string;
  local: string;
  data: string;
  piscina: TipoPiscina;
  tempo_final: string;
  created_at: string;
  updated_at?: string;
}

export interface EventoPresenca {
  id: string;
  evento_id: string;
  user_id: string;
  estado: EstadoPresenca;
  hora_chegada?: string;
  observacoes?: string;
  registado_por: string;
  registado_em: string;
}

export interface EventoResultado {
  id: string;
  evento_id: string;
  user_id: string;
  prova: string;
  tempo?: string;
  classificacao?: number;
  piscina?: string;
  escalao?: string;
  observacoes?: string;
  epoca?: string;
  registado_por: string;
  registado_em: string;
}
