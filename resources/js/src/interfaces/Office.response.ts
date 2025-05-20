export interface OfficeResponse {
    id:                   number;
    region_id:            number;
    office_id:            number;
    image:                null;
    name:                 string;
    city:                 string;
    province:             string;
    country:              string;
    latitude:             string;
    longitude:            string;
    first_updated_to_web: Date;
    access_ilist_net:     number;
    succeed_certified:    number;
    is_regional_office:   number;
    is_satellite_office:  number;
    first_year_licensed:  null;
    is_commercial:        number;
    is_collection:        number;
    date_time_stamp:      Date;
    active_office:        number;
    office_iconect_id:    string;
    office_intl_id:       null;
    macro_office:         null;
    office_type:          string;
    created_at:           Date;
    updated_at:           Date;
}
