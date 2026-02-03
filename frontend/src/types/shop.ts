/**
 * Shop, products, and inventory related types
 */

import type { MetodoPagamento } from './common';

export type EstadoEncomenda = 'pendente' | 'aprovada' | 'em_preparacao' | 'entregue' | 'cancelada';
export type LocalEntrega = 'clube' | 'morada_atleta' | 'outro';
export type TipoMovimentoStock = 'entrada' | 'saida' | 'ajuste' | 'devolucao';

export interface Product {
  id: string;
  nome: string;
  descricao?: string;
  imagem?: string;
  categoria: string;
  preco: number;
  stock: number;
  stock_minimo: number;
  ativo: boolean;
}

export interface Sale {
  id: string;
  produto_id: string;
  quantidade: number;
  preco_unitario: number;
  total: number;
  cliente_id?: string;
  vendedor_id: string;
  data: string;
  metodo_pagamento: MetodoPagamento;
}

export interface Fornecedor {
  id: string;
  nome: string;
  nif?: string;
  morada?: string;
  codigo_postal?: string;
  localidade?: string;
  contacto_telefone?: string;
  contacto_email?: string;
  contacto_nome?: string;
  iban?: string;
  observacoes?: string;
  ativo: boolean;
  created_at?: string;
}

export interface ArtigoLoja {
  id: string;
  nome: string;
  descricao?: string;
  categoria: string;
  preco_venda: number;
  preco_custo?: number;
  stock_atual: number;
  stock_minimo: number;
  fornecedor_id?: string;
  centro_custo_id?: string;
  imagem?: string;
  ativo: boolean;
  created_at?: string;
}

export interface EncomendaArtigo {
  id: string;
  data_encomenda: string;
  user_id: string;
  artigo_id: string;
  quantidade: number;
  valor_unitario: number;
  valor_total: number;
  escalao_id: string;
  centro_custo_id: string;
  local_entrega: LocalEntrega;
  morada_entrega?: string;
  estado: EstadoEncomenda;
  data_entrega?: string;
  observacoes?: string;
  fatura_id?: string;
  movimento_id?: string;
  criado_por?: string;
  created_at?: string;
}

export interface MovimentoStock {
  id: string;
  artigo_id: string;
  tipo: TipoMovimentoStock;
  quantidade: number;
  stock_anterior: number;
  stock_novo: number;
  motivo?: string;
  fornecedor_id?: string;
  encomenda_id?: string;
  valor_unitario?: number;
  centro_custo_id?: string;
  registado_por?: string;
  data_movimento: string;
  created_at?: string;
}
