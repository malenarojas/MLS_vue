import type { TypePropertyResponse } from "./TypeProperty.response";

export interface SubtypePropertyResponse {
    id:               number;
    name:             string;
    type_property_id: number;
    created_at:       null;
    updated_at:       null;
    type_property:    TypePropertyResponse;
}
