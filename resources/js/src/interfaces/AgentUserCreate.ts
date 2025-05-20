import type { Agentscreate } from "./Agentscreate";

// Definir tipo para el payload combinado de usuario y agente
export interface AgentUserCreate {
  user: {
    first_name: string;
    middle_name?: string;
    last_name: string;
    name_to_show?: string;
    email: string;
    username: string;
    password: string;
    phone_number?: string;
    gender?: string;
    remax_start_date?: string;
    ci?: string;
    url?: string;
  };
  agent: Partial<Agentscreate>; // Utiliza tu interfaz ya existente
}