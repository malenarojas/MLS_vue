import type { AgentsResponse } from "./AgentsResponse";
import type { ApiResponse } from "./ApiResponse";
import type { OfficeResponse } from "./Office.response";

export type AcquisitionResponseApi = ApiResponse<UserResponse>;

export interface UserResponse {
  agents: AgentsResponse[];
  offices: OfficeResponse[];
}

