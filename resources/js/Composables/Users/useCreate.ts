import { useQuery } from "@tanstack/vue-query";
import { fetchAcquisition } from "@/src/services/UserService";
import { useUserStoreLogin } from "@/Stores/userStoreLogin";
import type { ChangeUser } from "@/src/interfaces/UserChange";
import { watchEffect } from "vue";

export const useCreate = () => {
    const { setAcquisition } = useUserStoreLogin();
  
    const { data: userchange, isLoading, isError, error, isSuccess } = useQuery<ChangeUser, Error>({
        queryKey: ['userchange'],
        queryFn: fetchAcquisition,
    });
  
    watchEffect(() => {
        if (userchange?.value) {
          setAcquisition(userchange.value);
        }
    });
  
    return {
        isSuccess,
        isLoading,
        isError,
        error,
    };
  }