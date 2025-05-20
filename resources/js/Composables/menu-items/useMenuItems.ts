import { useQuery } from "@tanstack/vue-query";
import { fetchMenuItems } from '@/src/services/menuService';

export const useMenuItems = () => {
  const {data : menuItems, isLoading, isError} = useQuery({
    queryKey: ['menu-items'],
    queryFn: fetchMenuItems,
    
  });

  return {menuItems, isLoading, isError};
};
