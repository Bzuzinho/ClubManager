/**
 * User and member related types
 */

import type { MemberType, MemberStatus, Sex, CivilStatus, UserProfile } from './common';

export interface User {
  id: string;
  numero_socio: string;
  
  // Personal information
  foto_perfil?: string;
  nome_completo: string;
  data_nascimento: string;
  nif?: string;
  cc?: string;
  morada?: string;
  codigo_postal?: string;
  localidade?: string;
  nacionalidade?: string;
  estado_civil?: CivilStatus;
  ocupacao?: string;
  empresa?: string;
  escola?: string;
  menor: boolean;
  sexo: Sex;
  numero_irmaos?: number;
  contacto?: string;
  email_secundario?: string;
  encarregado_educacao?: string[];
  educandos?: string[];
  tipo_membro: MemberType[];
  estado: MemberStatus;
  contacto_telefonico?: string;
  
  // Financial information
  tipo_mensalidade?: string;
  conta_corrente?: number;
  centro_custo?: string[];
  
  // Sports information
  num_federacao?: string;
  cartao_federacao?: string;
  numero_pmb?: string;
  data_inscricao?: string;
  inscricao?: string;
  escalao?: string[];
  data_atestado_medico?: string;
  arquivo_atestado_medico?: string[];
  informacoes_medicas?: string;
  ativo_desportivo?: boolean;
  
  // Configuration
  perfil: UserProfile;
  senha?: string;
  rgpd: boolean;
  data_rgpd?: string;
  arquivo_rgpd?: string;
  consentimento: boolean;
  data_consentimento?: string;
  arquivo_consentimento?: string;
  afiliacao: boolean;
  data_afiliacao?: string;
  arquivo_afiliacao?: string;
  declaracao_de_transporte: boolean;
  declaracao_transporte?: string;
  email_utilizador: string;
}

export type PartialUser = Partial<User> & {
  id: string;
  nome_completo: string;
  numero_socio: string;
};

export interface DadosDesportivos {
  id: string;
  user_id: string;
  num_federacao?: string;
  cartao_federacao?: string;
  numero_pmb?: string;
  data_inscricao?: string;
  inscricao_path?: string;
  escalao_id?: string;
  data_atestado_medico?: string;
  arquivo_atestado_medico?: string[];
  informacoes_medicas?: string;
  ativo: boolean;
  created_at?: string;
  updated_at?: string;
}
