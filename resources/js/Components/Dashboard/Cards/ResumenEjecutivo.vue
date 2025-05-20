<template>
    <div class="p-4 rounded-lg border border-indigo-200 bg-indigo-50 mb-4">
      <div class="flex justify-between mb-2">
        <h2 class="font-semibold opacity-75 uppercase text-xl text-gray-700">Resumen Ejecutivo</h2>
        <button @click="openModal = 1" v-if="['Super Administrador', 'Administrador', 'Soporte', 'Broker'].includes(props.userRole)" >
						<i class="pi pi-window-maximize opacity-75"></i>
				</button>
      </div>
      <div class="flex justify-center mb-4">
        <div class="p-2 container max-w-5xl flex flex-wrap justify-center gap-8" v-if="filteredResumenData.length">
          <ResumenCard
          v-for="(item, index) in filteredResumenData"
            :key="index"
            :icon="filterIcons[index]"
            :title="filterTitles[index]"
            :valueA="item.valueA"
            :valueB="tipoReporte == 'mensual' ? item.valueB : null"
            :goal="item.goal"
          />
        </div>
        <div v-else>
            <div class="p-2 container max-w-5xl flex flex-wrap justify-center gap-8">
            <Skeleton 
              v-for="index in 6" 
              :key="index" 
              width="18rem" 
              height="10rem" 
              borderRadius="16px" 
            />
            </div>
        </div>
      </div>
    </div>
    <ResumenModal 
      v-model:openModal="openModal"
      v-if="openModal === 1"
      :months="monthsMapped"
      :canShowPayments="['Super Administrador', 'Administrador', 'Soporte'].includes(props.userRole)"
      :year="year"/>
  </template>
  
  <script setup>
  import { ref, toRefs, computed } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import ResumenCard from '@/Components/Dashboard/Cards/ResumenCard.vue';
  import ResumenModal from '@/Components/Dashboard/ResumenModal.vue';

  const props = defineProps({
      resumenData: Array,
      tipoReporte : String,
      months : Array,
      year : Number,
      userRole : String
    });

  let openModal = ref(0);

  let monthsMapped = computed(() => {
    return props.months.map((month) => {
      return month.code;
    });
  });

  const page = usePage();
  const role = ref(page.props.role);

  const { resumenData } = toRefs(props);
  const { tipoReporte } = toRefs(props);

  const icons = ref(
    [
      'pi-arrow-right-arrow-left',
      'pi-home',
      'pi-dollar',
      'pi-users',
      'pi-home'
    ]
  );

  const titles = ref([
    'Transacciones',
    'Volumen',
    'Comisiones',
    'Agentes',
    'Captaciones'
  ]);

  const filteredResumenData = computed(() => {
    return resumenData.value.filter((item, index) => index !== 3 || role.value !== 'Agente');
  });

  const filterIcons = computed(() => {
    return icons.value.filter((item, index) => index !== 3 || role.value !== 'Agente');
  });

  const filterTitles = computed(() => {
    return titles.value.filter((item, index) => index !== 3 || role.value !== 'Agente');
  });
  
  
 
  </script>