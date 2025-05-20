import { z } from "zod";

export const createUserResolver = z.object({
    office_id: z.number(),
    agent_id: z.object(
			{
				user_id:z.number()
			}
		)
 
});

export type FormCreateUser= z.infer<typeof createUserResolver>;