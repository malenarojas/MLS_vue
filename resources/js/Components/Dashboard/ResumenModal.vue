<template>
    
    <Dialog v-model:visible="openModal" modal :style="{ width: '80rem' }">
        <h2 class="text-2xl font-bold">Resumen de Oficinas</h2>
        <div class="flex justify-end mb-4">
            <div class="flex gap-2">
                <div class="relative">
                    <span class="absolute -top-3 bg-white text-sm left-3 px-0.5">Comparar datos con:</span>
                    <div class="flex gap-2 items-center border rounded-lg border-slate-300 py-2 px-3">
                        
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="comparativeType" @value-change="handleFiltersChange()" inputId="lastYear" name="lastYear" value="year" />
                            <label for="lastYear">Año anterior</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="comparativeType" @value-change="handleFiltersChange()" inputId="goal" name="goal" value="goal" />
                            <label for="goal">Meta asignada</label>
                        </div>
                    </div>
                </div>

                <Select v-model="inDollars" @value-change="handleFiltersChange" :options="currencys" optionLabel="name" optionValue="id" checkmark :highlightOnSelect="false" class="w-full md:w-auto ml-auto" />

                <Select 
                    optionLabel="name"
                    :options="[
                        { id: 1, min : 0, max : 2*365, name: '0 - 2 años' },
                        { id: 2, min : 2*365 + 1, max : 5*365, name: '2 - 5 años' },
                        { id: 3, min : 5*365 + 1, max : 8*365, name: '5 - 8 años' },
                        { id: 4, min : 8*365 + 1, name: 'Mas de 8 años' }
                    ]"
                    v-model="selectedAge"
                    placeholder="Seleccionar rango de Edad"
                    @value-change="handleFiltersChange"
                />

                <Button @click="exportData()">
                    <i class="fa-solid fa-file-excel"></i><span class="ml-1">Exportar</span>
                </Button>
            </div>
        </div>
        
        <div class="h-[424.5px] flex justify-center items-center" v-if="officesData.length === 0">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        </div>
        
        <DataTable :value="sortedByPayment" v-else 
            :style="{ fontSize: '0.875rem' }"
            :scrollable="true" 
            ref="dt"
            :sortField="sortField"
            :sortOrder="sortOrder"
            @sort="onSort"
            :scrollHeight="`400px`">
            <Column header="#" sorteable>
                <template #body="slotProps">
                    {{ slotProps.data.payment_rank }}
                </template>
            </Column>
            <Column field="name" header="Oficina" sortable/>
            <Column field="current_transactions" header="Transacciones" sortable>
            <template #body="slotProps">
                <div class="flex justify-between items-center gap-2">
                <PercentageArrow 
                    :valueA="slotProps.data.current_transactions"
                    :valueB="slotProps.data.comparation_transactions"
                    :comparativeType="comparativeType"
                    />
                {{ slotProps.data.current_transactions }}
                </div>
            </template>
            </Column>
            <Column field="current_transaction_volume" header="Volumen" sortable>
            <template #body="slotProps">
                <div class="flex justify-between items-center gap-2">
                <PercentageArrow 
                    :valueA="slotProps.data.current_transaction_volume"
                    :valueB="slotProps.data.comparation_transaction_volume"
                    :comparativeType="comparativeType"
                    />
                    
                {{ amount_format(slotProps.data.current_transaction_volume) }}
                </div>
            </template>
            </Column>
            <Column field="current_agents" header="Agentes Activos" sortable>
            <template #body="slotProps">
                <div class="flex justify-between items-center gap-2">
                <PercentageArrow 
                    :valueA="slotProps.data.current_agents"
                    :valueB="slotProps.data.comparation_agents"
                    :comparativeType="comparativeType"
                    />
                {{ slotProps.data.current_agents }}
                </div>
            </template>
            </Column>
            <Column field="current_payment_amount" header="Comisiones" 
                sortable>
            <template #body="slotProps">
                <div class="flex justify-between items-center gap-2" v-if="props.canShowPayments">
                    <PercentageArrow 
                        :valueA="slotProps.data.current_payment_amount"
                        :valueB="slotProps.data.comparation_payment_amount"
                        :comparativeType="comparativeType"
                        />
                    {{ amount_format(slotProps.data.current_payment_amount) }}
                </div>
                <div v-else>
                    -
                </div>
            </template>
            </Column>
            <Column field="current_active_listings" header="Captaciones Activas" sortable>
            <template #body="slotProps">
                <div class="flex justify-between items-center gap-2">
                <PercentageArrow 
                    :valueA="slotProps.data.current_active_listings"
                    :valueB="slotProps.data.comparation_active_listings"
                    :comparativeType="comparativeType"
                    />
                {{ slotProps.data.current_active_listings }}
                </div>
            </template>
            </Column>
            <Column header="Tiempo en mercado" field="age" sortable>
            <template #body="slotProps">
                <div class="flex justify-end">
                {{ yearMonthFormat(slotProps.data.age) }}
                </div>
            </template>
            </Column>
        </DataTable>
    </Dialog>
</template>

<script setup>
import { ref, defineModel, onMounted, computed } from 'vue';
import { Dialog, DataTable } from 'primevue';
import apiClient from '@/src/constansts/axiosClient';
import PercentageArrow from './PercentageArrow.vue';

const openModal = defineModel('openModal', 0);

let officesData = ref([]);

const dt = ref(null);

let selectedAge = ref(null);

const props = defineProps({
    months: Array,
    year: Number,
    canShowPayments : Boolean
});

const currencys = ref([
	{ 'name': 'Bs.', id: 0 },
	{ 'name': 'USD.', id: 1 },
])

const inDollars = ref(0);

let comparativeType = ref('year');

const sortField = ref(null)
const sortOrder = ref(null) 

function onSort(e) {
  sortField.value = e.sortField;
  sortOrder.value = e.sortOrder;
    console.log(sortOrder.value);
}

async function fetchData()
{
    const response = await apiClient.get('/dashboard/executive-resume/details', {
        params : {
            'months' : props.months,
            'year' : props.year,
            'comparativeType' : comparativeType.value,
            'inDollars' : inDollars.value,
        }
    })
    if(response.status == 200)
    {
        officesData.value = response.data;
    }
    else{
        console.error('Error fetching data:', error);
    }
};


const sortedByPayment = computed(() => {
  return [...officesData.value]
    .sort((a, b) =>  {
        // if(b.current_payment_amount == a.current_payment_amount)
        // {
        //     return b.id - a.id;
        // }
        return b.current_payment_amount - a.current_payment_amount
    })
    .map((item, index) => ({
      ...item,
      payment_rank: index + 1
    }))
})


function paymentSort(a, b) {
    const aValue = a?.current_payment_amount ?? 0
    const bValue = b?.current_payment_amount ?? 0

    console.log('aValue', aValue);
    console.log('bValue', bValue);
    console.log("\n");

    if (aValue == bValue) {
      return (a?.payment_rank ?? 0) - (b?.payment_rank ?? 0)
    }

    if (sortOrder.value == 1){
        return aValue > bValue ? -1 : 1
    }else {
        return aValue < bValue ? -1 : 1
    }
}

function exportData () {
    apiClient.get('/dashboard/executive-resume/details/export', {
        params : {
            'months' : props.months,
            'year' : props.year,
            'comparativeType' : comparativeType.value,
            'inDollars' : inDollars.value,
        },
        responseType: 'blob'
    }).then((response) => {
			
			const url = window.URL.createObjectURL(new Blob([response.data]));

			const link = document.createElement('a');
			link.href = url;
			link.setAttribute('download','test.xlsx');

			document.body.appendChild(link);
			link.click();
			link.remove();

		}).catch((error) => {
			console.error('Error descargando el archivo:', error);
		});
}

const amount_format = (value) => {
    if (value === null || value === undefined) {
        return '';
    }
    return new Intl.NumberFormat('es-US', {
        style: 'currency',
        currency: inDollars.value ? 'USD' : 'BOB'
    }).format(value);
};

const yearMonthFormat = (value) => {
    if (value === null || value === undefined) {
        return '';
    }
    const years = value / 365;
    const months = (value % 365) / 30;
    return `${Math.floor(years)} años ${Math.floor(months)} meses`;
};

async function handleFiltersChange() 
{
    officesData.value = [];

    await fetchData();

    if (selectedAge.value) {
        officesData.value = officesData.value.filter((item) => {
            return item.age < (selectedAge.value.max??99999) && item.age >= selectedAge.value.min;
        });
    } 
}

const sortOrderAsc = ref(false) 

onMounted(() => {
    fetchData();
});
</script>