/**
 * Common types and enums used across the application
 */

// Member types
export type MemberType = 
  | "atleta"
  | "encarregado_educacao"
  | "treinador"
  | "dirigente"
  | "socio"
  | "funcionario";

export type MemberStatus = "ativo" | "inativo" | "suspenso";

// Personal information types
export type Sex = "masculino" | "feminino";

export type CivilStatus = "solteiro" | "casado" | "divorciado" | "viuvo";

export type UserProfile = "admin" | "encarregado" | "atleta" | "staff";

// Presence status
export type EstadoPresenca = 'presente' | 'ausente' | 'justificado';

// Pool types
export type TipoPiscina = 'piscina_25m' | 'piscina_50m' | 'aguas_abertas';

// Payment methods
export type MetodoPagamento = 'dinheiro' | 'cartao' | 'mbway' | 'transferencia';

// Payment status
export type EstadoPagamento = 'pendente' | 'pago' | 'vencido' | 'parcial' | 'cancelado';

// Transaction types
export type TipoTransacao = 'receita' | 'despesa';

// Cost center types
export type TipoCentroCusto = 'equipa' | 'departamento' | 'pessoa' | 'projeto';
