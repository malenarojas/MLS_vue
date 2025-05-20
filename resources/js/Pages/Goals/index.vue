<template>
    <AppLayout>
        <div class="p-2 flex flex-col">

            <h1 class="title"> <i class="pi pi-chart-line mr-1" />Metas mensuales</h1>

            <div class="mt-4 flex justify-end  gap-4 bg-indigo-50 border-indigo-200 border p-2">

                
                <Select v-model="selectedOffice" optionLabel="name" filter :options="offices" placeholder="Oficina"
                    class="mb-auto" option-value="code" @value-change="fetchGoals()"
                    v-if="['Administrador', 'Super Administrador'].includes(role)" 
                    />

                <Select v-model="month" :aria-label="Mes" placeholder="Mes" option-label="name" 
                    optionLabel="name" optionValue="code" @value-change="fetchGoals()" :options="[
                    { code: 1, name: 'Enero' },
                    { code: 2, name: 'Febrero' },
                    { code: 3, name: 'Marzo' },
                    { code: 4, name: 'Abril' },
                    { code: 5, name: 'Mayo' },
                    { code: 6, name: 'Junio' },
                    { code: 7, name: 'Julio' },
                    { code: 8, name: 'Agosto' },
                    { code: 9, name: 'Septiembre' },
                    { code: 10, name: 'Octubre' },
                    { code: 11, name: 'Noviembre' },
                    { code: 12, name: 'Diciembre' }
                ]" />
                <Select v-model="year" :aria-label="Año" optionValue="code" optionLabel="name" :options="[
                    { code: 2026, name: '2026' },
                    { code: 2025, name: '2025' }
                ]" placeholder="Año" @value-change="fetchGoals()" />
                
            </div>
            <div class="mt-2 overflow-x-auto">
                <DataTable v-model:editingRows="editingRows" :value="goals" editMode="row" dataKey="compoundKey" 
                @row-edit-save="onRowEditSave" scrollHeight="500px" :scrollable="true" size="small"
                class="w-full table-auto text-sm"
                    :pt="{
                        table: { style: 'min-width: 50rem' },
                        column: {
                            bodycell: ({ state }) => ({
                                style: state['d_editing'] 
                                    ? 'padding-top: 0.75rem; padding-bottom: 0.75rem; white-space: nowrap; padding-left: 1.5rem;' 
                                    : 'padding-left: 1.5rem; white-space: nowrap;'
                            }),
                            headercell: { style: 'padding-left: 1.5rem' }
                        }
                    }"
                >
                
                    <Column field="office_name" header="Oficina" style="width: 20%; z-index: 1;" v-if="!selectedOffice" :frozen="true">
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" fluid class="text-sm" />
                        </template>
                    </Column>
                    
                    <Column field="agent_name" header="Agente" style="width: 20%; z-index: 1;" v-else :frozen="true">
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" fluid class="text-sm" />
                        </template>
                    </Column>
                    <Column :rowEditor="true" bodyStyle="text-align:center" style="z-index: 1;" :frozen="true"></Column>

                    <Column field="payment_amount" style="width: 20%">
                        <template #header>
                            <i class="pi pi-money-bill"></i> Pagos por Comisiones
                        </template>
                        <template #body="{ data, field }">
                            <div style="text-align: right;" class="text-sm">
                                {{ formatCurrency(data[field]) }}
                            </div>
                        </template>
                        <template #editor="{ data, field }">
                            <InputNumber v-model="data[field]" mode="currency" :currency="inDollars ? 'USD': 'BOB'" locale="en-US" fluid class="text-sm" />
                        </template>
                    </Column>
                    <Column field="production_by_agent" v-if="!selectedOffice">
                        <template #header>
                            <i class="pi pi-user"></i> Producción por Agente
                        </template>
                        <template #body="{ data, field }">
                            <div style="text-align: right;" class="text-sm">
                                {{ formatCurrency(data[field]) }}
                            </div>
                        </template>
                        <template #editor="{ data, field }">
                            <InputNumber v-model="data[field]" mode="currency" :currency="inDollars ? 'USD': 'BOB'" locale="en-US" fluid class="text-sm" />
                        </template>
                    </Column>
                    <Column field="time_in_market">
                        <template #header>
                            <i class="pi pi-home"></i><i class="pi pi-clock !text-[8px] -translate-x-2 translate-y-1"></i> <span class="-translate-x-2">Tiempo promedio en el mercado</span>
                        </template>
                        <template #body="{ data, field }">
                            <span class="text-sm">
                                {{ data[field] ? data[field] + ' Días': '' }}
                            </span>
                        </template>
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" fluid class="text-sm" />
                        </template>
                    </Column>
                    <Column field="transaction_volume" style="width: 20%">
                        <template #header>
                            <i class="pi pi-arrow-right-arrow-left"></i> Volumen de Transacciones
                        </template>
                        <template #body="{ data, field }">
                            <div style="text-align: right;" class="text-sm">
                                {{ formatCurrency(data[field]) }}
                            </div>
                        </template>
                        <template #editor="{ data, field }">
                            <InputNumber v-model="data[field]" mode="currency" :currency="inDollars ? 'USD': 'BOB'" locale="en-US" fluid class="text-sm" />
                        </template>
                    </Column>

                    <Column field="transactions" style="width: 20%">
                        <template #header>
                            <i class="pi pi-arrow-right-arrow-left"></i> Transacciones
                        </template>
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" fluid class="text-sm" />
                        </template>
                    </Column>
                    <Column field="new_listings" style="width: 20%">
                        <template #header>
                            <i class="pi pi-home"></i> Captaciones Activas
                        </template>
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" fluid class="text-sm" />
                        </template>
                    </Column>
                    
                    <Column field="new_agents" style="width: 20%" v-if="!selectedOffice">
                        <template #header>
                            <i class="pi pi-user"></i> Agentes Activos
                        </template>
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" fluid class="text-sm" />
                        </template>
                    </Column>

                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { usePage } from '@inertiajs/vue3';
    import { onMounted, ref } from 'vue';
    import apiClient from '@/src/constansts/axiosClient';
    import { split } from 'lodash';
    import { useToast } from 'primevue';
    import { useIsLoadingStore } from '@/Stores/isLoadingCharts';

	let offices = ref([]);
	let selectedOffice = ref(null);
    let inDollars = ref(0);
    const page = usePage();
    const role = page.props.role;
    const user = page.props.user;
    let month = ref();
    let year = ref();
    const toast = useToast();
    const isLoadingStore = useIsLoadingStore();

    const editingRows = ref([]);
    let goals = ref([]);

    async function fetchOffices() {
        const response = await apiClient.post('/dashboard/get-oficinas-por-ubicacion');
        offices.value = response.data.map(office => {
            return {
                code: office.id,
                name: office.name
            }
	})};

    const fetchGoals = async () => {
        isLoadingStore.setLoading(true);
        
        const response = await apiClient.get('/goals/get-goals', {
            params: {
                month: month.value,
                year: year.value,
                inDollars: inDollars.value,
                office_id: selectedOffice.value
            }
        });

        isLoadingStore.setLoading(false);

        if(response.status == 200){
            goals.value = response.data;

            goals.value = goals.value.map(goal => ({
                ...goal,
                compoundKey: `${goal.agent_id}-${goal.office_id}`
            }));
        }else{
            toast.add({ severity: 'error', summary: 'Error al obtener las metas', life: 3000 });
            console.log(response);
        }
    }

    const onRowEditSave = async (event) => {
        let { newData, index } = event;

        goals.value[index] = newData;

        newData = {
            ...newData,
            month: month.value,
            year: year.value,
            office_id: split(newData.compoundKey, '-')[1],
            agent_id: split(newData.compoundKey, '-')[0] == 'undefined' ? null : split(newData.compoundKey, '-')[0],
        };

        console.log(newData);

        const response = await apiClient.put(`/goals/update-goal`, newData);

        if(response.status == 200) {
            toast.add({ severity: 'success', summary: 'Meta actualizada', life: 3000 });
        }else{
            toast.add({ severity: 'error', summary: 'Error al actualizar la meta', life: 3000 });
        }
    };

    const formatCurrency = (value) => {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: inDollars.value ? 'USD' : 'BOB' }).format(value);
    }


    onMounted(async () => {

        month.value = new Date().getMonth() + 1;
        year.value = new Date().getFullYear();

        if(role == 'Broker'){
            offices.value = [{ code: user.agent.office.id, name: user.agent.office.name }];
            selectedOffice.value = user.agent.office.id;
        }else{
            await fetchOffices();
        }
        await fetchGoals();
    });

</script>

<style>
    thead tr {
        z-index: 1000 !important;
        position: relative;
    }
    thead tr th {
        z-index: 1000 !important;
    }

    tbody tr {
        z-index: 0 !important;
        position: relative;
    }
</style>