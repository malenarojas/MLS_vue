<script setup lang="ts">
import type { FormSubmitEvent } from "@primevue/forms";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { createUserResolver } from "@/src/validators/Users/ChangeUserValidation";
import { useCreate } from "@/Composables/Users/useCreate";
import { useUserStoreLogin } from "@/Stores/userStoreLogin";
import { fetchOffices } from "@/src/services/OfficeService";
import type { OfficeResponse } from "@/src/interfaces/Office.response";
import type { SelectOption } from "@/src/interfaces/SelectOption";
import { fetchAgent, fetchAgentByOffice } from "@/src/services/AgentService";
import { usePage, router } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import apiClient from "@/src/constansts/axiosClient";
import { useListingFilterStore } from "@/Stores/useListingFilterStore";
import { route } from "vendor/tightenco/ziggy/src/js";

const page = usePage();
const listingFilter = useListingFilterStore();

const role = page.props.role;
const user = page.props.user;

onMounted(async () => {
    if (role == "Broker") {
        office_selected.value = user.agent?.office?.id ?? null;
        agents.value = await fetchAgentByOffice(user.agent?.office?.id ?? null);
    } else {
        console.log("Fetching offices for non-broker role");
        offices.value = await fetchOffices();
    }
    //selAgent.value = { "name": "Eva Yanira Ortiz Aguilera", "id": 128 };
});

const store = useUserStoreLogin();
// const { isLoading, isSuccess } = useCreate();
const selAgent = ref<SelectOption>();
const resolver = zodResolver(createUserResolver);
const offices = ref<OfficeResponse[]>([]);
const agents = ref<SelectOption[]>([]);
const office_selected = ref<string>();

// OJO 👀 ⚠ Revisar ⚠ @jimmy

// const onFormSubmit = ({ valid, values }: FormSubmitEvent) => {
// 	console.log(values);
// 	if (valid) {

// 		storeAuth.loginAs(values.agent_id.user_id)
// 		store.hideLoginModal()
// 	}
// };

const changeOffice = async () => {
    if (office_selected.value) {
        agents.value = await fetchAgentByOffice(office_selected.value);
        console.log(agents.value);
    } else {
        console.error("No se ha seleccionado una oficina");
    }
};

const onLoginSubmit = async () => {
    if (selAgent.value) {
        console.log("Agente seleccionado:");
        console.log(selAgent.value);

        //storeAuth.loginAs(selAgent.value.user_id);
        //store.hideLoginModal();

        const response = await apiClient.post("/login-as", {
            user_id: selAgent.value.user_id,
        });

        // console.log(response.data);

        if (response.status == 200) {
            const cleanUrl = window.location.pathname;
            console.log(`URL ${cleanUrl}`);

            router.visit(cleanUrl, {
                method: "get",
                replace: true,
                preserveScroll: true,
            });

            listingFilter.clearFilters();
        }
    }
};
</script>

<template>
    <Form :resolver @submit="onFormSubmit" class="grid grid-cols-2 gap-4">
        <!-- <pre>{{ offices }}</pre> -->
        <FormField
            v-slot="$field"
            name="office_id"
            class="flex flex-col gap-1"
            v-if="role != 'Broker'"
        >
            <label for="office_id">Oficina</label>
            <Select
                filter
                size="small"
                :options="offices"
                @value-change="changeOffice"
                v-model="office_selected"
                optionLabel="name"
                optionValue="id"
                placeholder="Selecciona oficina"
                class="w-full"
                fluid
            />

            <Message
                v-if="$field?.invalid"
                severity="error"
                size="small"
                variant="simple"
                >{{ $field.error.message }}
            </Message>
        </FormField>

        <FormField v-slot="$field" name="agent_id" class="flex flex-col gap-1">
            <label for="agent_id">Agente</label>

            <!-- <pre>{{ agents }}</pre> -->

            <Select
                filter
                v-model="selAgent"
                size="small"
                :options="agents"
                placeholder="Selecciona agente"
                optionLabel="name"
                class="w-full"
            >
                <template #option="slotProps">
                    <span>{{ slotProps.option.name }}</span>
                </template>
                <template #value="slotProps">
                    <span>{{
                        slotProps.value
                            ? slotProps.value.name
                            : slotProps.placeholder
                    }}</span>
                </template>
            </Select>

            <Message
                v-if="$field?.invalid"
                severity="error"
                size="small"
                variant="simple"
                >{{ $field.error.message }}
            </Message>
        </FormField>

        <div class="flex justify-end mt-2 col-span-2">
            <Button
                size="small"
                label="Cancelar"
                type="button"
                severity="danger"
                class="mx-3"
                @click="store.hideLoginModal;"
                :disabled="!selAgent"
            />
            <Button
                size="small"
                label="Aceptar"
                severity="info"
                @click="onLoginSubmit()"
            />
        </div>
    </Form>
</template>
