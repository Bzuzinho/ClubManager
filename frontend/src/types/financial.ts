/**
 * Financial, billing and transaction related types
 */

import type { EstadoPagamento, TipoTransacao, MetodoPagamento, TipoCentroCusto } from './common';

export interface Mensalidade {
  id: string;
  designacao: string;
  valor: number;
  ativo: boolean;
  created_at?: string;
}

export interface CentroCusto {
  id: string;
  nome: string;
  tipo: TipoCentroCusto;
  descricao?: string;
  orcamento?: number;
  ativo: boolean;
  created_at?: string;
}

export type TipoFatura = 'mensalidade' | 'inscricao' | 'material' | 'servico' | 'outro';

export interface Fatura {
  id: string;
  user_id: string;
  data_fatura: string;
  mes?: string;
  data_emissao: string;
  data_vencimento: string;
  valor_total: number;
  estado_pagamento: EstadoPagamento;
  numero_recibo?: string;
  referencia_pagamento?: string;
  centro_custo_id?: string;
  tipo: TipoFatura;
  observacoes?: string;
  created_at?: string;
}

export interface FaturaItem {
  id: string;
  fatura_id: string;
  descricao: string;
  valor_unitario: number;
  quantidade: number;
  imposto_percentual: number;
  total_linha: number;
  produto_id?: string;
  centro_custo_id?: string;
  created_at?: string;
}

export interface LancamentoFinanceiro {
  id: string;
  data: string;
  tipo: TipoTransacao;
  categoria?: string;
  descricao: string;
  valor: number;
  centro_custo_id?: string;
  user_id?: string;
  fatura_id?: string;
  metodo_pagamento?: string;
  comprovativo?: string;
  created_at?: string;
}

export interface ExtratoBancario {
  id: string;
  conta?: string;
  data_movimento: string;
  descricao: string;
  valor: number;
  saldo?: number;
  referencia?: string;
  centro_custo_id?: string;
  conciliado: boolean;
  lancamento_id?: string;
  created_at?: string;
}

export type TipoMovimento = 'inscricao' | 'material' | 'servico' | 'outro';

export interface Movimento {
  id: string;
  user_id?: string;
  nome_manual?: string;
  nif_manual?: string;
  morada_manual?: string;
  classificacao: TipoTransacao;
  data_emissao: string;
  data_vencimento: string;
  valor_total: number;
  estado_pagamento: EstadoPagamento;
  numero_recibo?: string;
  referencia_pagamento?: string;
  centro_custo_id?: string;
  tipo: TipoMovimento;
  observacoes?: string;
  created_at?: string;
}

export interface MovimentoItem {
  id: string;
  movimento_id: string;
  descricao: string;
  valor_unitario: number;
  quantidade: number;
  imposto_percentual: number;
  total_linha: number;
  produto_id?: string;
  centro_custo_id?: string;
  fatura_id?: string;
  created_at?: string;
}

export interface MovimentoConvocatoria {
  id: string;
  user_id: string;
  convocatoria_grupo_id: string;
  evento_id: string;
  evento_nome: string;
  tipo: 'convocatoria';
  data_emissao: string;
  valor: number;
  itens: MovimentoConvocatoriaItem[];
  created_at: string;
}

export interface MovimentoConvocatoriaItem {
  id: string;
  movimento_convocatoria_id: string;
  descricao: string;
  valor: number;
}

export interface Transaction {
  id: string;
  tipo: TipoTransacao;
  categoria: string;
  descricao: string;
  valor: number;
  data: string;
  centro_custo?: string;
  user_id?: string;
  metodo_pagamento?: string;
  comprovativo?: string;
}
