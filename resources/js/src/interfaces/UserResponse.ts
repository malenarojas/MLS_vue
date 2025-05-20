export interface UserResponse {
	id: string,
  first_name: string;
  middle_name: string;
  last_name: string;
  name_to_show: string;
  ci: string;
  gender: string;
  phone_number: string;
  email: string;
  url: string;
  remax_start_date: string; 
  password: string;
  username: string;
  user_type_id: number | null;
  remax_title_id: number | null;
  remax_title_to_show_id: number | null;
  team_status_id: number | null;
  customer_preference_id: number | null;
}