export interface AchievementUser {
	achievement_id: number; // ID del logro relacionado
	enable_achievement: string; // Si va a ser un booleano (0 o 1),
	achievement_date: string; // Fecha del logro (formato ISO 8601 recomendado)
  }
  