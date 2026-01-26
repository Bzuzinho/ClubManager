import api from '../../lib/api';
import type { Fatura, ContaCorrente, ResumoFinanceiro } from './types';

interface FaturasResponse {
  data: Fatura[];
  meta: {
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
  };
}

interface FaturaResponse {
  data: Fatura;
}

export const faturasApi = {
  // Listar faturas
  list: async (params?: {
    membro_id?: number;
    mes?: string;
    estado?: string;
    page?: number;
    per_page?: number;
  }): Promise<FaturasResponse> => {
    const response = await api.get('/v2/faturas', { params });
    return response.data;
  },

  // Buscar fatura por ID
  get: async (id: number): Promise<FaturaResponse> => {
    const response = await api.get(`/v2/faturas/${id}`);
    return response.data;
  },

  // Gerar faturas de mensalidade
  gerarMensalidades: async (data: {
    membro_id: number;
    mes_inicio: string;
    mes_fim?: string;
  }): Promise<{ message: string; data: Fatura[] }> => {
    const response = await api.post('/v2/faturas/gerar-mensalidades', data);
    return response.data;
  },

  // Criar fatura avulsa
  create: async (data: {
    membro_id: number;
    mes: string;
    data_emissao: string;
    data_vencimento: string;
    itens: Array<{
      descricao: string;
      tipo: string;
      valor: number;
    }>;
  }): Promise<FaturaResponse> => {
    const response = await api.post('/v2/faturas', data);
    return response.data;
  },

  // Adicionar item à fatura
  adicionarItem: async (
    faturaId: number,
    data: {
      descricao: string;
      tipo: string;
      valor: number;
    }
  ): Promise<{ message: string; data: Fatura }> => {
    const response = await api.post(`/v2/faturas/${faturaId}/itens`, data);
    return response.data;
  },

  // Registar pagamento
  registarPagamento: async (
    faturaId: number,
    data: {
      data: string;
      valor: number;
      metodo: string;
      referencia?: string;
      observacoes?: string;
    }
  ): Promise<{ message: string; data: Fatura }> => {
    const response = await api.post(`/v2/faturas/${faturaId}/pagamentos`, data);
    return response.data;
  },

  // Conta corrente do membro
  contaCorrente: async (membroId: number): Promise<{ data: ContaCorrente }> => {
    const response = await api.get(`/v2/membros/${membroId}/conta-corrente`);
    return response.data;
  },

  // Resumo financeiro do membro
  resumoFinanceiro: async (membroId: number): Promise<{ data: ResumoFinanceiro }> => {
    const response = await api.get(`/v2/membros/${membroId}/resumo-financeiro`);
    return response.data;
  },
};
