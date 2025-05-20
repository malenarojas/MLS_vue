// composables/useAgents.ts

import { useQuery } from '@tanstack/vue-query';
import { useAgentStore } from '@/stores/Agents';
import { fetchAgent } from '@/src/services/AgentService';
import { mapAgentResponseListToEntities } from '@/src/mappers/AgentsMapper';

export const useAgents = () => {
	
  const store = useAgentStore();

  const { data: agents, isLoading, isError, error } = useQuery({
    queryKey: ['agents'],
    queryFn: fetchAgent,
  });

  watchEffect(() => {
    if (agents?.value) {
      store.setAgents(agents.value ?? []);
    }
  });

  return {
    agents,
    isLoading,
    isError,
    error,
  };
};
