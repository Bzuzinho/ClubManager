/**
 * Configuration and general application types
 */

export type TipoSponsor = 'principal' | 'secundario' | 'apoio';

export interface NewsItem {
  id: string;
  titulo: string;
  conteudo: string;
  imagem?: string;
  destaque: boolean;
  autor: string;
  data_publicacao: string;
  categorias: string[];
}

export interface Sponsor {
  id: string;
  nome: string;
  logo?: string;
  tipo: TipoSponsor;
  contrato_inicio: string;
  contrato_fim?: string;
  valor_anual?: number;
  contacto_nome?: string;
  contacto_email?: string;
  contacto_telefone?: string;
  ativo: boolean;
}
