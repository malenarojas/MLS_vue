import type { ChangeUser } from "@/src/interfaces/UserChange";
import type { UserResponse } from "../interfaces/Users.response";
import { mapOfficeResponseListToEntities } from "./OfficeMapper";
import { mapAgentResponseListToEntities } from "./AgentsMapper";
import type { UserFilter } from '@/src/interfaces/UsersFilter';

// Mappear data para formulario
export const mapAcquisitionResponseToEntity = (response: UserResponse): ChangeUser => {
  return {
    agents: mapAgentResponseListToEntities(response.agents),
    offices: mapOfficeResponseListToEntities(response.offices),
  };
}


export const mapUserResponseToEntity = (response: UserResponse): UserFilter => ({
  id: response.id,
  username: response.username,
	ci:response.ci,
  email: response.email,
  // Mapear otras propiedades según sea necesario
});

export const mapUserResponseListToEntities = (responses: UserResponse[]): UserFilter[] => {
  return responses.map(mapUserResponseToEntity);
};


