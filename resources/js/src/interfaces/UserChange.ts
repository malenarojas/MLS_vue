import type { Office } from "./Office";
import type { AgentsFilter } from "./AgentsFilter";

export interface ChangeUser {
	agents: AgentsFilter[];
	offices: Office[];
}