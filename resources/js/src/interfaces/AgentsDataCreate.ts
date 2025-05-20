import type { AgentsResponse } from "./AgentsResponse";

import type { FormOptions } from "./fetchAgentOptions";

export interface AgentDatacreate {
	formOptions: FormOptions; // Donde FormOptions es la interfaz con areas, regions...
	agent: AgentsResponse; // Otra interfaz para agent
  }
  