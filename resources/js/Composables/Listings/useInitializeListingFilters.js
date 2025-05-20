import { useListingFilterStore } from "@/Stores/useListingFilterStore";

export function useInitializeListingFilters(props) {
    const filtersStore = useListingFilterStore();

    const user = props.auth?.user;
    const filters = props.filters ?? {};
    const permissions = props.permissions ?? [];

    const isAdmin = permissions.includes("listing.show_offices");
    const canSeeAgents = permissions.includes("listing.show_agents");

    const initializeFilters = () => {
        if (filtersStore.formFilters.status_id === null)
            filtersStore.formFilters.status_id = 2;

        if (!isAdmin) filtersStore.formFilters.office_id = null;

        if (!canSeeAgents) filtersStore.formFilters.agent_id = null;

        // if (!isAdmin) {
        //     filtersStore.formFilters.office_id = filters.office_id ?? null;
        // }

        // if (filtersStore.formFilters.agent_id === null) {
        //     if (!canSeeAgents) {
        //         filtersStore.formFilters.agent_id = user?.agent?.id ?? null;
        //         // filtersStore.formFilters.agent_id = filters.agent_id ?? null;
        //     } else {
        //     }
        // }
    };

    return { initializeFilters, isAdmin, canSeeAgents };
}
