<template>

    <Card 
        :icon="'pi pi-money-bill'"
        :title="'Tipo de cambio'">

        <div>
            <div class="grid grid-cols-2 gap-4">
                <FloatLabel variant="on">
                    <InputNumber id="exchange_rate" v-model="new_exchange" class="w-full" 
                        inputId="locale-user"
						:minFractionDigits="0" 
						:maxFractionDigits="2"
                        min="0"/>
                        
                    <label for="exchange_rate">Nueva tasa de cambio</label>
                </FloatLabel>

                <Button label="Actualizar" icon="pi pi-check" :loading="loading" @click="updateExchangeRate"/>
            </div>

            <div class="mt-2">
                <p class="text-sm">La tasa de cambio actual es de: <strong>1 USD = {{ exchange }} BOB</strong></p>
            </div>
        </div>
    </Card>

    <Toast />
</template>
<script setup>

    import Card from '../Card.vue';
    import { ref } from 'vue';
    import apiClient from '@/src/constansts/axiosClient';
    import { Toast } from 'primevue';
    import { useToast } from "primevue";

    const props = defineProps({
        exchange: {
            type: String,
            required: true
        }
    });

    const exchange = ref(props.exchange);

    const toast = useToast();

    let new_exchange = ref(0);
    let loading = ref(false);

    const updateExchangeRate = async () => {
        loading.value = true;
        const response = await apiClient.post('/admin/exchange-rate', { exchange: new_exchange.value });
        if(response.status == 200) {
            exchange.value = new_exchange.value;
            new_exchange.value = 0;
            toast.add({severity:'success', summary: 'Tasa de cambio actualizada', detail: 'La tasa de cambio ha sido actualizada correctamente', life:3000});
        }else{
            toast.add({severity:'error', summary: 'Error', detail: 'Ha ocurrido un error al actualizar la tasa de cambio' , life:3000});
        }
        loading.value = false;
    }

</script>