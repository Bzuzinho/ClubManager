import api from '../../lib/api';
import type { Atleta, Equipa, Treino, EstatisticasPresenca, EstatisticasAtleta } from './types';

interface Response<T> {
  data: T;
  meta?: {
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
  };
}

export const atletasApi = {
  list: async (params?: { estado?: string; escalao_id?: number; page?: number }): Promise<Response<Atleta[]>> => {
    const response = await api.get('/atletas', { params });
    return response.data;
  },

  get: async (id: number): Promise<Response<Atleta>> => {
    const response = await api.get(`/atletas/${id}`);
    return response.data;
  },

  create: async (data: {
    membro_id: number;
    escalao_id?: number;
    numero_atleta?: string;
    data_inscricao: string;
    estado: string;
  }): Promise<Response<Atleta>> => {
    const response = await api.post('/atletas', data);
    return response.data;
  },

  update: async (id: number, data: Partial<Atleta>): Promise<Response<Atleta>> => {
    const response = await api.put(`/atletas/${id}`, data);
    return response.data;
  },

  delete: async (id: number): Promise<void> => {
    await api.delete(`/atletas/${id}`);
  },

  updateEquipas: async (id: number, equipa_ids: number[]): Promise<Response<Atleta>> => {
    const response = await api.put(`/atletas/${id}/equipas`, { equipa_ids });
    return response.data;
  },

  estatisticas: async (id: number): Promise<Response<EstatisticasAtleta>> => {
    const response = await api.get(`/atletas/${id}/estatisticas`);
    return response.data;
  },
};

export const equipasApi = {
  list: async (params?: { temporada?: string; escalao_id?: number }): Promise<Response<Equipa[]>> => {
    const response = await api.get('/equipas', { params });
    return response.data;
  },

  get: async (id: number): Promise<Response<Equipa>> => {
    const response = await api.get(`/equipas/${id}`);
    return response.data;
  },

  create: async (data: {
    nome: string;
    escalao_id?: number;
    temporada: string;
    tipo?: string;
  }): Promise<Response<Equipa>> => {
    const response = await api.post('/equipas', data);
    return response.data;
  },

  update: async (id: number, data: Partial<Equipa>): Promise<Response<Equipa>> => {
    const response = await api.put(`/equipas/${id}`, data);
    return response.data;
  },

  delete: async (id: number): Promise<void> => {
    await api.delete(`/equipas/${id}`);
  },

  adicionarAtletas: async (id: number, atleta_ids: number[]): Promise<{ message: string }> => {
    const response = await api.post(`/equipas/${id}/atletas`, { atleta_ids });
    return response.data;
  },
};

export const treinosApi = {
  list: async (params?: {
    equipa_id?: number;
    data_inicio?: string;
    data_fim?: string;
    tipo?: string;
    page?: number;
  }): Promise<Response<Treino[]>> => {
    const response = await api.get('/treinos', { params });
    return response.data;
  },

  get: async (id: number): Promise<Response<Treino>> => {
    const response = await api.get(`/treinos/${id}`);
    return response.data;
  },

  create: async (data: {
    equipa_id: number;
    data: string;
    hora_inicio: string;
    hora_fim: string;
    local?: string;
    tipo: string;
    observacoes?: string;
  }): Promise<Response<Treino>> => {
    const response = await api.post('/treinos', data);
    return response.data;
  },

  update: async (id: number, data: Partial<Treino>): Promise<Response<Treino>> => {
    const response = await api.put(`/treinos/${id}`, data);
    return response.data;
  },

  delete: async (id: number): Promise<void> => {
    await api.delete(`/treinos/${id}`);
  },

  registarPresencas: async (
    id: number,
    presencas: Array<{ atleta_id: number; presente: boolean; justificada?: boolean; observacoes?: string }>
  ): Promise<{ message: string }> => {
    const response = await api.post(`/treinos/${id}/presencas`, { presencas });
    return response.data;
  },

  estatisticasPresenca: async (id: number): Promise<Response<EstatisticasPresenca>> => {
    const response = await api.get(`/treinos/${id}/estatisticas-presenca`);
    return response.data;
  },
};
