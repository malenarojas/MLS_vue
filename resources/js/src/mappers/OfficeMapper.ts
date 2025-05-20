import type { Office } from "../interfaces/Office";
import type { OfficeResponse } from "../interfaces/Office.response";

export const mapOfficeResponseToEntity = (response: OfficeResponse): Office => {
    return {
      id: response.id,
      name: response.name,
      officeId: response.office_id,
      regionId: response.region_id,
      country: response.country,
      city: response.city,
    };
  }
  
  export const mapOfficeResponseListToEntities = (
    response: OfficeResponse[]
  ): Office[] => {
    return response.map(mapOfficeResponseToEntity);
  }