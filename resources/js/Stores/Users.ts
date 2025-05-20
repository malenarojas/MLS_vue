// stores/users.store.ts

import { defineStore } from 'pinia';
import type { UserResponse } from '@/src/interfaces/UserResponse';
import { fetchUsers, fetchUserById, createUser, updateUser, deleteUser } from '@/src/services/UserService';
import type { UserFilter } from '@/src/interfaces/UsersFilter';

export const useUserStore = defineStore('users', {
  state: () => ({
    users: [] as UserResponse[], // Lista de usuarios
    filters: {
			id: '',
      username: '', // Filtro por nombre
      email: '', // Filtro por correo electrónico
      ci: '', // Filtro por ci
    },
    filteredUsers: [] as UserFilter[], // Lista de usuarios filtrados
  }),

  actions: {
    // Establecer la lista de usuarios
    setUsers(users: UserResponse[]) {
      this.users = users;
      this.applyFilters(); // Aplicar filtros después de establecer los usuarios
    },

    // Obtener todos los usuarios desde la API
    async fetchUsers() {
      try {
        const data = await fetchUsers(); // Llama al servicio para obtener los usuarios
        this.setUsers(data);
      } catch (error) {
        console.error('Error al obtener usuarios:', error);
        throw new Error('No se pudieron cargar los usuarios');
      }
    },

    // Obtener un usuario por ID
    async fetchUserById(id: string) {
      try {
        const user = await fetchUserById(id);
        return user; // Retorna el usuario obtenido
      } catch (error) {
        console.error(`Error al obtener el usuario con ID ${id}:`, error);
        throw new Error('No se pudo cargar el usuario.');
      }
    },

    // Crear un nuevo usuario
    async createUser(userData: Partial<UserResponse>) {
      try {
        const newUser = await createUser(userData); // Llama al servicio para crear el usuario
        this.users.push(newUser); // Añadir el nuevo usuario al estado
        this.applyFilters(); // Aplicar filtros después de añadir el nuevo usuario
        return newUser; // Retornar el usuario creado
      } catch (error) {
        console.error('Error al crear usuario:', error);
        throw new Error('No se pudo crear el usuario');
      }
    },

    // Actualizar un usuario por ID
    async updateUserById(id: string, userData: Partial<UserResponse>) {
      try {
        const updatedUser = await updateUser(id, userData); // Llama al servicio para actualizar el usuario
        const index = this.users.findIndex(user => user.id === id);
        if (index !== -1) {
          this.users[index] = updatedUser; // Actualiza el usuario en el estado
          this.applyFilters(); // Aplicar filtros después de actualizar el usuario
        }
        return updatedUser;
      } catch (error) {
        console.error(`Error al actualizar el usuario con ID ${id}:`, error);
        throw new Error('No se pudo actualizar el usuario.');
      }
    },

    // Eliminar un usuario por ID
    async deleteUserById(id: string) {
      try {
        await deleteUser(id); // Llama al servicio para eliminar el usuario
        this.users = this.users.filter(user => user.id !== id); // Elimina el usuario del estado
        this.applyFilters(); // Aplicar filtros después de eliminar el usuario
      } catch (error) {
        console.error(`Error al eliminar el usuario con ID ${id}:`, error);
        throw new Error('No se pudo eliminar el usuario.');
      }
    },

    // Aplicar filtros a la lista de usuarios
    applyFilters() {
      this.filteredUsers = this.users.filter(user => {
        return (
					(!this.filters.id|| user.id.includes(this.filters.id)) &&
          (!this.filters.username || user.username.includes(this.filters.username)) &&
          (!this.filters.email || user.email.includes(this.filters.email)) &&
          (!this.filters.ci || user.ci === this.filters.ci)
        );
      });
    },
  },
});
