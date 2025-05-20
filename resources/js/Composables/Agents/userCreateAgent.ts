import type { Agentscreate } from "@/src/interfaces/Agentscreate";
import { createAgent as createAgentService } from "@/src/services/AgentService";
import { useAgentStore } from "@/stores/Agents";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import type { AgentCreate } from "~/src/interfaces/AgentCreate";

export function useCreateAgent() {
  const queryClient = useQueryClient();
  const agentStore = useAgentStore();

  const mutation = useMutation<AgentCreate, Error, AgentCreate>({
    mutationFn: createAgentService,
    onSuccess: (newAgent) => {
      agentStore.addAgentToState(newAgent);
      queryClient.invalidateQueries({ queryKey: ["agents"] });
    },
    onError: (error) => {
      console.error("Error al crear el agente:", error);
    },
  });

  return {
    mutate: mutation.mutate,
    isPending: mutation.isPending, // 🔹 En lugar de isLoading
    isError: mutation.isError,
    isSuccess: mutation.isSuccess,
    error: mutation.error,
  };
}

