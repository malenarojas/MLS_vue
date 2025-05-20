import type { UserResponse } from "./UserResponse";

export interface AgentResponse {
    id: string;
    office_id: string;
    user: UserResponse;
}