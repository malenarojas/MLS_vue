import type { SpecialityPivot } from "./SpecialityPivotResponse";

export interface Speciality {
	id: number;
	name: string;
	parent_id: number | null;
	created_at?: string;    // Estas fechas pueden no estar siempre presentes en todas las especialidades
	updated_at?: string;
	pivot?: SpecialityPivot; // Presente cuando la especialidad está vinculada directamente a un área
	children?: Speciality[]; // Sub-especialidades anidadas
  }
