// stores/agents.store.ts

import { deleteAgent, fetchAgent, fetchAgentOptions, fetchAgentShow, getAgentsByFilters, getAgentsByOffice, getAgentsByRegion } from '@/src/services/AgentService';
import { defineStore } from 'pinia';
import type { AgentCreate } from '@/src/interfaces/AgentCreate';
import type { AgentsFilter } from '@/src/interfaces/AgentsFilter';
import type { AgentShow } from '@/src/interfaces/AgentShow';
import type { AgentsResponse } from '@/src/interfaces/AgentsResponse';
import type { AgentsUpdate } from '@/src/interfaces/AgentsUpdate';
import type { FormOptions } from '@/src/interfaces/fetchAgentOptions';
import { mapAgentResponseListToEntities } from '@/src/mappers/AgentsMapper';

export const useAgentStore = defineStore('agents', {

  state: () => ({
    agents: [] as AgentsResponse[], // Lista de agentes transformados
		agentscreate: [] as AgentCreate[],
    filteredAgents: [] as AgentsFilter[],
		selectedAgent: null as AgentShow | null, // Agente seleccionado
		agentsupdate: [] as AgentsUpdate[],
		formOptions: {} as FormOptions,
		loading: false, // Indicador de carga
		error: null as string | null, // Mensaje de error
		// Agentes filtrados
		filters: {
		id: '',
		office_id: '',         // ID de la oficina
		region_id: '',         // ID de la región
		agent_status_id: 0,         // Estado activo o inactivo
		commission_percentage: '',// porcetaje de comision
		search: '',
		},
  }),

  actions: {
    // Obtener agentes por región
    async fetchAgentsByRegion(regionId: string): Promise<void> {
		this.loading = true;
		this.error = null;
		try {
		  const data = await getAgentsByRegion(regionId);
		  this.agents = data; // Actualiza la tabla con los agentes obtenidos
		  this.filteredAgents = mapAgentResponseListToEntities(data); // Actualiza los agentes filtrados
		} catch (error) {
		  console.error('Error al obtener los agentes por región:', error);
		  this.error = 'Error al obtener los agentes por región.';
		  throw new Error(this.error);
		} finally {
		  this.loading = false;
		}
	  },

	  // Obtener agentes por oficina
	  async fetchAgentsByOffice(officeId: string): Promise<void> {
		this.loading = true;
		this.error = null;
		try {
		  const data = await getAgentsByOffice(officeId );
		  this.agents = data; // Actualiza la tabla con los agentes obtenidos
		  this.filteredAgents = mapAgentResponseListToEntities(data); // Actualiza los agentes filtrados
		} catch (error) {
		  console.error('Error al obtener los agentes por oficina:', error);
		  this.error = 'Error al obtener los agentes por oficina.';
		  throw new Error(this.error);
		} finally {
		  this.loading = false;
		}
	  },

	  // Obtener agentes por filtros
	async fetchAgentsByFilters(filters: Record<string, any>): Promise<void> {
		this.loading = true;
		this.error = null;
		try {
		const data = await getAgentsByFilters(filters);
		this.agents = data.data; // Actualiza la tabla con los agentes obtenidos
		//this.filteredAgents = mapAgentResponseListToEntities(data.data); // Actualiza los agentes filtrados
		} catch (error) {
		console.error('Error al obtener los agentes con filtros:', error);
		this.error = 'Error al obtener los agentes con filtros.';
		throw new Error(this.error);
		} finally {
		this.loading = false;
		}
	},



    // Establecer agentes (después de transformarlos)
    setAgents(agents: AgentsResponse[]) {
      this.agents = agents;
      this.filteredAgents = mapAgentResponseListToEntities(agents); // Inicialmente sin filtros
    },

	// Obtener agentes desde la API
    async fetchAgents() {
		try {
		  const data = await fetchAgent(); // Llama al servicio para obtener los agentes
		  this.agents = data;
		  this.filteredAgents = data; // Inicialmente, sin filtros
		} catch (error) {
		  console.error('Error al obtener agentes:', error);
		  throw new Error('No se pudieron cargar los agentes');
		}
	  },

	  async fetchAgentById(id: string) {
			this.loading = true;
			this.error = null;

			try {
				const agent = await fetchAgentShow(id);
				this.selectedAgent = agent; // Guarda el agente en el estado
				return agent;
			} catch (error) {
				console.error(`Error al obtener el agente con ID ${id}:`, error);
				this.error = 'No se pudo cargar el agente.';
				throw new Error(this.error);
			} finally {
				this.loading = false; // Finaliza el estado de carga
			}
		},


		   // Crear un agente
		     addAgentToState(newAgent: AgentCreate) {
				this.agentscreate.push(newAgent);
			},
			async fetchAgentOptions() {
				this.loading = true;
				this.error = null;
				try {
				  const response = await fetchAgentOptions();
				  console.log('Datos obtenidos del API:', response); // Log en la consola
				  this.formOptions = response.formOptions; // Asigna el objeto completo
				} catch (error) {
				  console.error('Error al obtener las opciones del formulario:', error);
				  this.error = 'No se pudieron cargar las opciones del formulario.';
				  throw new Error(this.error);
				} finally {
				  this.loading = false;
				}
			  },

			updateAgentInState(updatedAgent: AgentsUpdate) {
				const index = this.agents.findIndex(agent => agent.id === updatedAgent.id);
				if (index !== -1) {
					this.agentsupdate[index] = { ...this.agents[index], ...updatedAgent };
				}
			},


			// Eliminar un agente
			async deleteAgentById(id: string) {
				try {
					await deleteAgent(id);
					this.agents = this.agents.filter(agent => agent.id !== id); // Elimina el agente del estado
				} catch (error) {
					console.error(`Error al eliminar el agente con ID ${id}:`, error);
					throw new Error('No se pudo eliminar el agente.');
				}
			},

    // Aplicar filtros a los agentes
    applyFilters() {
      this.filteredAgents = mapAgentResponseListToEntities(this.agents).filter((agent: AgentsFilter) => {
        return (
		  (!this.filters.id ||agent.id === this.filters.id) &&
          (!this.filters.office_id || agent.office_id.toString() === this.filters.office_id) &&
          (!this.filters.region_id || agent.region_id.toString() === this.filters.region_id) &&
		  (!this.filters.commission_percentage || agent.commission_percentage.toString() === this.filters.commission_percentage) &&
          (!this.filters.agent_status_id || agent.agent_status_id === this.filters.agent_status_id)
        );
      });
    },
  },
});
