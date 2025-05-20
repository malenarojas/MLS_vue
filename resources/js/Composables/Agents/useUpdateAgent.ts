import { updateAgent as updateAgentService } from '@/src/services/AgentService';
import { useAgentStore } from '@/stores/Agents';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type { AgentsUpdate } from '~/src/interfaces/AgentsUpdate';

export function useUpdateAgent() {
  const queryClient = useQueryClient();
  const agentStore = useAgentStore();

  const mutation = useMutation<AgentsUpdate, Error, { id: string; data: Partial<AgentsUpdate> }>({
    mutationFn: async ({ id, data }) => {
      return await updateAgentService(id, data); // Llamada al servicio para actualizar el agente
    },
    onSuccess: (updatedAgent) => {
      agentStore.updateAgentInState(updatedAgent); // Actualiza el agente en el estado local
      queryClient.invalidateQueries({ queryKey: ['agents'] }); // Invalida la consulta para actualizar la lista
    },
    onError: (error) => {
      console.error('Error al actualizar el agente:', error);
    },
  });

  return mutation;
}
