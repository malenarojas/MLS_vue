import type { Speciality } from "./SpecialityResponse";

export interface Area {
	id: number;
	name: string;
	description: string;
	created_at: string;
	updated_at: string;
	is_base: number;
	specialities: Speciality[];
}
  