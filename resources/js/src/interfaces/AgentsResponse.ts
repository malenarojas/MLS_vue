import type { UserCreate } from "./UserCreate";

export interface AgentsResponse {
    id:                      string;
    agent_internal_id:       string;
    office_id:               number;
    region_id:               number;
    user_id:                 number;
    date_joined:             string;
    date_termination:        string;
    agent_status_id:         number;
    studies:                 string;
    additional_education:    string;
    image_name:             string;
    previous_occupation:     string;
    license_type:            string;
    license_department:      string;
	  year_obtained_license:   string;
    expiration_date_license: string;
    license_number:          number;
    address: string;
    landline_phone: string;
    marketing_slogan:        string;
    website_descripction:    string;
    countries_interested:    string;
    meta_tag_description:    string;
    bullet_point_one:        string;
    bullet_point_two:        string;
    bullet_point_three:      string;
    meta_tag_keywords:       string;
    deactivation_date:       string;
    commission_percentage:   number;
		rejectionReason:string;
    qualifications_id:       null;
    social_networks_id:      null;
    created_at:              Date;
    updated_at:              Date;
		//user:any
		user: UserCreate
}
