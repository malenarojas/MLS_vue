export interface ApiResponse<T> {
    status: string; 
    message: string; 
    data: T; // Tipo genérico que representa los datos específicos
  }