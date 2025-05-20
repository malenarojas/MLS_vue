import apiClient from "../constansts/axiosClient";
import type { Agentscreate } from "../interfaces/Agentscreate";
import type { AgentDatacreate } from "../interfaces/AgentsDataCreate";
import type { AgentsResponse } from "../interfaces/AgentsResponse";
import type { AgentsUpdate } from "../interfaces/AgentsUpdate";
import type { SelectOption } from "../interfaces/SelectOption";
import type { AgentParams } from "../interfaces/Agent.params";
import type { AgentEntity } from "../interfaces/Agent.entity";
import {
    mapAgentResponseListToAgentEntities,
    mapAgentResponseToAgentEntity,
} from "../mappers/AgentsMapper";
import type { AgentCreate } from "../interfaces/AgentCreate";
import type { AgentShow } from "../interfaces/AgentShow";
// import type { AgentsResponse } from '../interfaces/Agent.response';

export const getAgentsByRegion = async (id: string) => {
    try {
        const response = await apiClient.post(`/agents/by-office`, id);
        return response.data;
    } catch (error) {
        console.error("Error al obtener agentes por región:", error);
        throw error;
    }
};

export const getAgentsByOffice = async (id: string) => {
    try {
        const response = await apiClient.post(`/agents/by-region`, id);
        return response.data;
    } catch (error) {
        console.error("Error al obtener agentes por oficina:", error);
        throw error;
    }
};

export const getAgentsByFilters = async (filters: Record<string, any>) => {
    try {
        const response = await apiClient.post(`/agents/filter`, filters);
        return response.data;
    } catch (error) {
        console.error("Error al obtener agentes con filtros:", error);
        throw error;
    }
};

export const fetchAgent = async (): Promise<AgentsResponse[]> => {
    const response = await apiClient.get("/agents");
    return response.data as AgentsResponse[]; // Especificas que los datos son un arreglo de `Agent`.
};

// Función para obtener los logs de auditoría de un agente específico
export const fetchAuditLogsByAgent = async (agentId: string) => {
    try {
        const response = await apiClient.get(`/agent/${agentId}/logs`);
        return response.data; // Asumiendo que la respuesta es un array de logs
    } catch (error) {
        console.error("Error al obtener los logs de auditoría:", error);
        throw new Error("No se pudieron obtener los logs de auditoría.");
    }
};

export const fetchAgentShow = async (id: string): Promise<AgentShow> => {
    if (!id) {
        throw new Error("El ID del agente no puede estar vacío.");
    }

    try {
        const response = await apiClient.get(`/agents/${id}`);
        return response.data as AgentShow;
    } catch (error: any) {
        if (error.response) {
            // Error del servidor (5xx) o cliente (4xx)
            console.error(
                `Error al obtener el agente con ID ${id}:`,
                error.response.data
            );
        } else if (error.request) {
            // No se recibió respuesta del servidor
            console.error(
                "No se recibió respuesta del servidor:",
                error.request
            );
        } else {
            // Error al configurar la solicitud
            console.error("Error al configurar la solicitud:", error.message);
        }
        throw new Error("No se pudo obtener la información del agente.");
    }
};

export const createAgent = async (
    agentcreate: AgentCreate
): Promise<AgentCreate> => {
    try {
        const response = await apiClient.post("/agents", agentcreate);
        return response.data as AgentCreate;
    } catch (error) {
        console.log("payload enviado ", agentcreate);
        console.error("Error al crear el agente:", error);
        throw new Error("No se pudo crear el agente.");
    }
};

export const fetchAgentOptions = async (): Promise<AgentDatacreate> => {
    try {
        const response = await apiClient.get("/agents/create");
        return response.data; // Devuelve los datos directamente de la API
    } catch (error: any) {
        console.error(
            "Error al obtener las opciones para el formulario:",
            error.message
        );

        if (error.response) {
            console.error("Error del servidor:", error.response.data);
        } else if (error.request) {
            console.error(
                "No se recibió respuesta del servidor:",
                error.request
            );
        } else {
            console.error("Error al configurar la solicitud:", error.message);
        }
        throw new Error("No se pudo obtener las opciones para el formulario.");
    }
};

export const updateAgent = async (
    id: string,
    agentData: Partial<AgentsUpdate>
): Promise<AgentsUpdate> => {
    if (!id) {
        throw new Error("El ID del agente es obligatorio para actualizar.");
    }

    // Obtener el user_id desde localStorage
    const userlogin = JSON.parse(localStorage.getItem("user") || "{}").id;

    // Preparar el objeto de configuración con headers
    const config = {
        headers: {
            "User-Login": userlogin, // Aquí estamos añadiendo el user_id en el header de la solicitud
        },
    };

    // **Console log para depurar los datos enviados en el body**
    console.log(
        "Enviando actualización de agente con ID:",
        id,
        "y user_login:",
        userlogin
    );
    console.log("Datos enviados en el body:", agentData);

    try {
        const response = await apiClient.put(
            `/agents/${id}`,
            agentData,
            config
        );
        // **Console log para verificar la respuesta del backend**
        console.log("Respuesta del backend:", response.data);

        return response.data as AgentsUpdate;
    } catch (error) {
        console.error(`Error al actualizar el agente con ID ${id}:`, error);
        throw new Error("No se pudo actualizar el agente.");
    }
};

export const deleteAgent = async (id: string): Promise<void> => {
    if (!id) {
        throw new Error("El ID del agente es obligatorio para eliminar.");
    }

    try {
        await apiClient.delete(`/agents/${id}`);
        console.log(`Agente con ID ${id} eliminado correctamente.`);
    } catch (error) {
        console.error(`Error al eliminar el agente con ID ${id}:`, error);
        throw new Error("No se pudo eliminar el agente.");
    }
};

export const fetchAgentByOffice = async (
    office_id: string
): Promise<SelectOption[]> => {
    if (!office_id) {
        throw new Error("El id de office no puede estar vacio");
    }

    try {
        const response = await apiClient.post(`/dashboard/get-agentes`, {
            oficina_id: office_id,
        });
        return response.data as SelectOption[];
    } catch (error: any) {
        if (error.request) {
            // No se recibió respuesta del servidor
            console.error(
                "No se recibió respuesta del servidor:",
                error.request
            );
        } else {
            // Error al configurar la solicitud
            console.error("Error al configurar la solicitud:", error.message);
        }
        throw new Error("No se pudo obtener la información de los agentes.");
    }
};

export const filterAgent = async (
    agentParams: AgentParams
): Promise<AgentEntity[]> => {
    try {
        console.log("Filtrando agentes con:", agentParams);
        const response = await apiClient.get(
            `/agents/search?office_id=${agentParams.office_id}`
        );
        return response.data;
    } catch (error) {
        console.log("Error al filtrar agentes:", error);
        throw new Error("No se pudo filtrar los agentes.");
    }
};
