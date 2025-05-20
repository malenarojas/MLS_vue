import type { AgentsFilter } from '@/src/interfaces/AgentsFilter';
import type { AgentsResponse } from '@/src/interfaces/AgentsResponse';
import type { AgentEntity } from '../interfaces/Agent.entity';
import type { AgentResponse } from '../interfaces/Agent.response';
import { mapUserResponseToEntity } from './User.mapper';

export const mapAgentResponseToEntity = (response: AgentsResponse): AgentsFilter => ({
  id: response.id ,
  agent_internal_id: response.agent_internal_id,
  office_id: response.office_id,
  region_id: response.region_id,
  agent_status_id: response.agent_status_id ? 1 : 0,
  user_id: response.user.id,
  user: {

	id: response.user.id ?? "default-id",
	first_name: response.user.first_name??'',
	middle_name:response.user.middle_name??'',
	last_name:response.user.last_name??'',
	rol: response.user.rol[0].name,
  },
  commission_percentage: response.commission_percentage,
  studies: response.studies

});

export const mapAgentResponseListToEntities = (responses: AgentsResponse[]): AgentsFilter[] => {
  return responses.map(mapAgentResponseToEntity);
};

export const mapAgentResponseToAgentEntity = (response: AgentResponse): AgentEntity => ({
  id: response.id,
  officeId: response.office_id,
  user: mapUserResponseToEntity(response.user)
});

export const mapAgentResponseListToAgentEntities = (responses: AgentResponse[]): AgentEntity[] => {
  return responses.map(mapAgentResponseToAgentEntity);
};

