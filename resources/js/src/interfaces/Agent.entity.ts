import type { UserFilter } from "./UsersFilter";

export interface AgentEntity {
    id: string;
    officeId: string;
    user: UserFilter;
}