export interface Member {
  id: number;
  nome: string;
  email: string;
  numero_socio?: string | null;
  tipo_membro?: string | null;
  estado?: string | null;
  activo: boolean;
  telefone?: string | null;
  data_nascimento?: string | null;
  nif?: string | null;
  morada?: string | null;
  codigo_postal?: string | null;
  localidade?: string | null;
  pais?: string | null;
  observacoes?: string | null;
}

interface ApiMember {
  id: number;
  numero_socio?: string | null;
  estado?: string | null;
  observacoes?: string | null;
  user?: {
    name?: string | null;
    email?: string | null;
    telefone?: string | null;
    ativo?: boolean | null;
    dados_pessoais?: {
      nome_completo?: string | null;
      data_nascimento?: string | null;
      nif?: string | null;
      morada?: string | null;
      codigo_postal?: string | null;
      localidade?: string | null;
      nacionalidade?: string | null;
      contacto_telefonico?: string | null;
    } | null;
  } | null;
  tipos_utilizador?: Array<{ nome?: string | null } | null> | null;
}

export const mapMember = (apiMember: ApiMember): Member => {
  const dadosPessoais = apiMember?.user?.dados_pessoais || {};

  return {
    id: apiMember.id,
    nome: dadosPessoais.nome_completo || apiMember.user?.name || "Sem nome",
    email: apiMember.user?.email || "",
    numero_socio: apiMember.numero_socio || null,
    tipo_membro:
      apiMember.tipos_utilizador?.[0]?.nome || apiMember.estado || "Sócio",
    estado: apiMember.estado || null,
    activo: apiMember.estado ? apiMember.estado === "ativo" : Boolean(apiMember.user?.ativo),
    telefone: apiMember.user?.telefone || dadosPessoais.contacto_telefonico || null,
    data_nascimento: dadosPessoais.data_nascimento || null,
    nif: dadosPessoais.nif || null,
    morada: dadosPessoais.morada || null,
    codigo_postal: dadosPessoais.codigo_postal || null,
    localidade: dadosPessoais.localidade || null,
    pais: dadosPessoais.nacionalidade || null,
    observacoes: apiMember.observacoes || null,
  };
};

export const extractMembersArray = (responseData: any): ApiMember[] => {
  if (Array.isArray(responseData)) {
    return responseData;
  }
  if (Array.isArray(responseData?.data)) {
    return responseData.data;
  }
  return [];
};
