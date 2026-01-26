export interface Fatura {
  id: number;
  numero: string;
  membro_id: number;
  membro?: {
    id: number;
    numero_socio: string;
    user: {
      name: string;
      email: string;
    };
  };
  mes: string;
  data_emissao: string;
  data_vencimento: string;
  status_cache: 'pendente' | 'paga' | 'cancelada' | 'parcialmente_paga';
  valor_total: number;
  valor_pago: number;
  valor_pendente: number;
  itens?: FaturaItem[];
  pagamentos?: Pagamento[];
  created_at: string;
  updated_at: string;
}

export interface FaturaItem {
  id: number;
  fatura_id: number;
  descricao: string;
  tipo: string;
  valor: number;
  created_at: string;
  updated_at: string;
}

export interface Pagamento {
  id: number;
  fatura_id: number;
  data: string;
  valor: number;
  metodo: string;
  referencia?: string;
  observacoes?: string;
  created_at: string;
  updated_at: string;
}

export interface ContaCorrente {
  membro_id: number;
  membro: {
    numero_socio: string;
    user: {
      name: string;
      email: string;
    };
  };
  saldo: number;
  total_faturas: number;
  total_pago: number;
  total_pendente: number;
  movimentos: Movimento[];
}

export interface Movimento {
  id: number;
  tipo: 'fatura' | 'pagamento';
  data: string;
  descricao: string;
  valor: number;
  saldo: number;
}

export interface ResumoFinanceiro {
  total_faturas: number;
  valor_total: number;
  valor_pago: number;
  valor_pendente: number;
  faturas_pendentes: number;
  faturas_pagas: number;
  faturas_canceladas: number;
  por_mes: {
    mes: string;
    valor_total: number;
    valor_pago: number;
    valor_pendente: number;
  }[];
}
