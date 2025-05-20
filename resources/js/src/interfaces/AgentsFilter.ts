import type { UserCreate } from "./UserCreate";

// Datos que usará el frontend
export interface AgentsFilter {
	id: string;
	agent_internal_id: string;
	office_id: number;
	region_id: number;
	agent_status_id: number;
	studies: string;
	commission_percentage: number;
	user_id: number;
	user: UserCreate
  }
