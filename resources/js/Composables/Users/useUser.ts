// composables/useUsers.ts

import { useQuery } from '@tanstack/vue-query';
import { useUserStore } from '@/stores/Users';
import { fetchUsers } from '@/src/services/UserService';
import { mapUserResponseListToEntities } from '@/src/mappers/UserMapper';
import { watchEffect } from 'vue';
import type { UserResponse } from '~/src/interfaces/UserResponse';

export const useUsers = () => {
  const store = useUserStore();

  const { data: users, isLoading, isError, error } = useQuery<UserResponse[]>({
    queryKey: ['users'],
    queryFn: fetchUsers,
  });

  watchEffect(() => {
    if (users?.value) {
      store.setUsers( users.value ?? []);
    }
  });

  return {
    users,
    isLoading,
    isError,
    error,
  };
};
