// services/officeService.ts
import apiClient from "../constansts/axiosClient";
import type { OfficeResponse } from "../interfaces/Office.response";

// Obtener todas las oficinas
export const fetchOffices = async (): Promise<OfficeResponse[]> => {
    try {
        const response = await apiClient.get("/offices/all");
        return response.data as OfficeResponse[];
    } catch (error) {
        console.error("Error al obtener las oficinas:", error);
        throw new Error("No se pudieron obtener las oficinas.");
    }
};

// Obtener una oficina por ID
export const fetchOfficeById = async (id: number): Promise<OfficeResponse> => {
    if (!id) {
        throw new Error("El ID de la oficina es obligatorio.");
    }
    try {
        const response = await apiClient.get(`/offices/${id}`);
        return response.data as OfficeResponse;
    } catch (error) {
        console.error(`Error al obtener la oficina con ID ${id}:`, error);
        throw new Error("No se pudo obtener la información de la oficina.");
    }
};

// Crear una nueva oficina
export const createOffice = async (
    officeData: Partial<OfficeResponse>
): Promise<OfficeResponse> => {
    try {
        const response = await apiClient.post("/offices", officeData);
        return response.data as OfficeResponse;
    } catch (error) {
        console.error("Error al crear la oficina:", error);
        throw new Error("No se pudo crear la oficina.");
    }
};

// Actualizar una oficina existente
export const updateOffice = async (
    id: number,
    officeData: Partial<OfficeResponse>
): Promise<OfficeResponse> => {
    if (!id) {
        throw new Error("El ID de la oficina es obligatorio para actualizar.");
    }
    try {
        const response = await apiClient.put(`/offices/${id}`, officeData);
        return response.data as OfficeResponse;
    } catch (error) {
        console.error(`Error al actualizar la oficina con ID ${id}:`, error);
        throw new Error("No se pudo actualizar la oficina.");
    }
};

// Eliminar una oficina
export const deleteOffice = async (id: number): Promise<void> => {
    if (!id) {
        throw new Error("El ID de la oficina es obligatorio para eliminar.");
    }
    try {
        await apiClient.delete(`/offices/${id}`);
        console.log(`Oficina con ID ${id} eliminada correctamente.`);
    } catch (error) {
        console.error(`Error al eliminar la oficina con ID ${id}:`, error);
        throw new Error("No se pudo eliminar la oficina.");
    }
};
