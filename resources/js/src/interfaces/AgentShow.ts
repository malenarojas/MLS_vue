
import type { AchievementUser } from "./AchievementUser";
import type { AreaSpecilitiesUser } from "./AreaSpecilitiesUser";
import type { SocialNetwork } from "./SocialNetwork";
import type { UserCreate } from "./UserCreate";
export interface AgentShow{
		id:                      string;
		agent_internal_id:       string;
		office_id:               number;
		region_id:               number;
		date_joined:             Date;
		date_termination:        Date;
		agent_status_id:         number;
		studies:                 string;
		image_name:              string;
		image_agent:             string;
		additional_education:    string;
		previous_occupation:     string;
		license_type:            string;
		license_department:      string;
		year_obtained_license:   string;
		expiration_date_license: Date;
		license_number:          string;
	    address: string;
		landline_phone: string;
		commission_percentage:   number;
		marketing_slogan: string;
		meta_tag_keywords: string;
		website_descripction: string;
		countries_interested: string;
		meta_tag_description: string;
		bullet_point_one: string,
		bullet_point_two: string,
		bullet_point_three: string,
		user: UserCreate;
		social_networks: SocialNetwork[];
		area_specialities_user: AreaSpecilitiesUser[],
		achievement_user: AchievementUser[],
		region_name: string,
		office_name: string,
}
