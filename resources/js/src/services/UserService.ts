import type { AcquisitionResponseApi } from "../interfaces/Users.response";
import { mapAcquisitionResponseToEntity } from "../mappers/UserMapper";
import type { ChangeUser } from "@/src/interfaces/UserChange";
import apiClient from "../constansts/axiosClient";
import type { UserResponse } from "../interfaces/UserResponse";

export const fetchAcquisition = async (): Promise<ChangeUser> => {
  const response = await apiClient.get<AcquisitionResponseApi>('/changeuser');
  return mapAcquisitionResponseToEntity(response.data.data);
}

export const loginAs = async (data:any): Promise<AcquisitionResponseApi> => {
  const response = await apiClient.post('/loginas', data);
  return response.data;

}

// Obtener todos los usuarios
export const fetchUsers = async (): Promise<UserResponse[]> => {
  try {
    const response = await apiClient.get<UserResponse[]>('/users');
    return response.data as UserResponse[];
  } catch (error) {
    console.error('Error al obtener los usuarios:', error);
    throw new Error('No se pudieron obtener los usuarios.');
  }
};

// Obtener un usuario por ID
export const fetchUserById = async (id: string): Promise<UserResponse> => {
  if (!id) {
    throw new Error('El ID del usuario no puede estar vacío.');
  }

  try {
    const response = await apiClient.get(`/users/${id}`);
    return response.data as UserResponse;
  } catch (error) {
    console.error(`Error al obtener el usuario con ID ${id}:`, error);
    throw new Error('No se pudo obtener la información del usuario.');
  }
};

// Crear un nuevo usuario
export const createUser = async (userData: Partial<UserResponse>): Promise<UserResponse> => {
  try {
    const response = await apiClient.post('/users', userData);
    return response.data as UserResponse;
  } catch (error) {
    console.error('Error al crear el usuario:', error);
    throw new Error('No se pudo crear el usuario.');
  }
};

// Actualizar un usuario existente
export const updateUser = async (id: string, userData: Partial<UserResponse>): Promise<UserResponse> => {
  if (!id) {
    throw new Error('El ID del usuario es obligatorio para actualizar.');
  }

  try {
    const response = await apiClient.put(`/users/${id}`, userData);
    return response.data as UserResponse;
  } catch (error) {
    console.error(`Error al actualizar el usuario con ID ${id}:`, error);
    throw new Error('No se pudo actualizar el usuario.');
  }
};

// Eliminar un usuario
export const deleteUser = async (id: string): Promise<void> => {
  if (!id) {
    throw new Error('El ID del usuario es obligatorio para eliminar.');
  }

  try {
    await apiClient.delete(`/users/${id}`);
    console.log(`Usuario con ID ${id} eliminado correctamente.`);
  } catch (error) {
    console.error(`Error al eliminar el usuario con ID ${id}:`, error);
    throw new Error('No se pudo eliminar el usuario.');
  }

};
