import type { AchievementResponse } from "./AchievementResponse";
import type { Area } from "./AreaResponse";
import type { GoalResponse } from "./GoalResponse";
import type { OfficeResponse } from "./Office.response";
import type { RegionResponse } from "./RegionsRespone";
import type { RemaxTitleResponse } from "./RemaxTitleResponse";
import type { remaxtitletoshow } from "./RemaxTitleToShowResponse";
import type { TeamStatusResponse } from "./TeamStatusResponse";
import type { UserTypeResponse } from "./UserTypeResponse";

export interface FormOptions {
	regions:  RegionResponse[];
	offices:  OfficeResponse[];
	teamStatuses: TeamStatusResponse[];
	goals: GoalResponse[];
	usertype: UserTypeResponse[];
	remaxtitle: RemaxTitleResponse[];
	remaxtitletoshow: remaxtitletoshow[];
	areas: Area[];
	Achievement: AchievementResponse[];
}
