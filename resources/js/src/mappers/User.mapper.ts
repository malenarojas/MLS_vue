import type { UserResponse } from "../interfaces/UserResponse";
import type { UserFilter } from "../interfaces/UsersFilter";

export const mapUserResponseToEntity = (response: UserResponse): UserFilter => ({
    id: response.id,
    username: response.username,
    ci:response.ci,
    email: response.email,
    firstName: response.first_name,
    lastName: response.last_name,
});