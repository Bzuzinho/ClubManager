import api from '../../lib/api';
import type { Evento, Inscricao } from './types';

interface Response<T> {
  data: T;
  meta?: {
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
  };
}

export const eventosApi = {
  list: async (params?: {
    tipo?: string;
    data_inicio?: string;
    data_fim?: string;
    inscricoes_abertas?: boolean;
    page?: number;
  }): Promise<Response<Evento[]>> => {
    const response = await api.get('/eventos', { params });
    return response.data;
  },

  get: async (id: number): Promise<Response<Evento>> => {
    const response = await api.get(`/eventos/${id}`);
    return response.data;
  },

  create: async (data: {
    titulo: string;
    descricao?: string;
    tipo: string;
    data_inicio: string;
    data_fim?: string;
    local?: string;
    capacidade_maxima?: number;
    inscricoes_abertas: boolean;
    data_limite_inscricao?: string;
    valor_inscricao?: number;
    observacoes?: string;
  }): Promise<Response<Evento>> => {
    const response = await api.post('/eventos', data);
    return response.data;
  },

  update: async (id: number, data: Partial<Evento>): Promise<Response<Evento>> => {
    const response = await api.put(`/eventos/${id}`, data);
    return response.data;
  },

  delete: async (id: number): Promise<void> => {
    await api.delete(`/eventos/${id}`);
  },

  inscrever: async (
    eventoId: number,
    data: {
      membro_id: number;
      atleta_id?: number;
      observacoes?: string;
    }
  ): Promise<{ message: string; data: Inscricao }> => {
    const response = await api.post(`/eventos/${eventoId}/inscrever`, data);
    return response.data;
  },
};
