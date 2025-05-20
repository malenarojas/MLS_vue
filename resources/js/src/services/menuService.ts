import apiClient from '../constansts/axiosClient';
import type { MenuItem } from '../interfaces/MenuItems';

export const fetchMenuItems = async (): Promise<any[]> => {
  const response = await apiClient.get('/dashboard');
  return response.data; 
};
