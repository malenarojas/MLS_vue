// Comentario: Código en inglés, comentarios en español
import type { Agentscreate } from '@/src/interfaces/Agentscreate'; // Ajusta según tu proyecto
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAgentStore = defineStore('agentStore', () => {
  // Comentario: estado local de agentes
  const agentscreate = ref<Agentscreate[]>([]);

  // Comentario: Obtenemos el QueryClient para manipular caché
  const queryClient = useQueryClient();

  // Comentario: Definimos la mutación para crear un agente
  const mutation = useMutation(createAgent, {
    onSuccess: (newAgent) => {
      // Comentario: Al crear exitosamente el agente, lo agregamos al estado
      agentscreate.value.push(newAgent);
      // Comentario: Invalidamos las queries relacionadas, por ejemplo ['agents']
      queryClient.invalidateQueries(['agents']);
    },
    onError: (error) => {
      console.error('Error al crear el agente:', error);
    },
  });

  // Comentario: Método para disparar la mutación
  const createAgent = (agentcreate: Agentscreate) => {
    mutation.mutate(Agentscreate);
  };

  return {
    agentscreate,
    createAgent,
    mutation,
  };
});
