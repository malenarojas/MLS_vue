<template>
    <AppLayout title="MarketAnalysis">
    <Toast />
      <div class="acm-container">
        <h1 class="acm-main-title">Análisis de mercado ACM</h1>
        <!-- Stepper -->
        <div class="stepper-container space-y-0 gap-0">
          <Stepper v-model:value="activeStep" class="stepper-wrapper">
            <StepList class="flex items-center">
                <template v-for="(step, index) in stepOrder" :key="step">
                    <Step asChild :value="step">
                    <div class="step-item">
                        <button
                        class="step-button"
                        :class="activeStep === step ? 'bg-blue-500 text-white' : 'bg-gray-300'"
                        @click="setStep(step)"
                        >
                        <span :class="stepClasses(step)">
                            <i v-if="step === 4" class="pi pi-info-circle"></i>
                            <i v-else-if="step === 2" class="pi pi-list"></i>
                            <i v-else-if="step === 3" class="pi pi-map-marker"></i>
                            <i v-else-if="step === 1" class="pi pi-money-bill"></i>
                        </span>
                        </button>
                        <span class="step-title" :class="activeStep === step ? 'text-blue-500' : 'text-gray-400'">
                        {{ step === 4 ? 'Características' : step === 2 ? 'Comisión' : step === 3 ? 'Ubicación' : 'Detalles' }}
                        </span>
                    </div>
                    </Step>

                    <!-- Línea entre pasos (excepto el último) -->
                    <div
                    v-if="index < stepOrder.length - 1"
                    class="h-1 flex-grow mx-2"
                    :class="stepOrder.indexOf(activeStep) > index ? 'bg-gradient-to-r from-blue-900 to-blue-500' : 'bg-gray-200'"
                    ></div>
                </template>
                </StepList>
         <StepPanels>
        <!-- Paso 4 (ahora el primero) -->
        <StepPanel :value="4" class="stepper-panel">
          <div class="step-content border border-gray-300 shadow-md rounded">
            <StepFour
              :filtros="filters"
              :options="options"
              @updateFilters="updateFilters"
              @anterior="goToPreviousStep"
                          @siguiente="goToNextStep"
              @buscar="buscarPropiedades"
            />
                      <!-- Contenedor relativo -->
                      <div class="relative flex justify-end gap-4 mt-4 custom-height">

                          <!-- Botón Buscar -->
                          <button
                              class="p-3 bg-blue-500 text-white rounded-full hover:bg-blue-600 shadow-md order-1"
                              @click="fetchListings"
                          >
                              <i class="pi pi-search"></i>
                          </button>

                          <!-- Botón Siguiente -->
                          <button
                              class="p-3 bg-green-500 text-white rounded- hover:bg-green-600 shadow-md order-2"
                              @click="goToNextStep"
                          >
                              <i class="pi pi-arrow-right"></i>
                          </button>
                      </div>

          </div>
        </StepPanel>

        <!-- Paso 2 -->
        <StepPanel :value="2" class="stepper-panel">
          <div class="step-content border border-gray-300 shadow-md rounded">
            <StepTwo
              :filters="filters"
              :options="options"
              @updateFilters="updateFilters"
              @anterior="goToPreviousStep"
              @siguiente="goToNextStep"
            />
                       <!-- Contenedor de botones -->
                      <div  class="relative flex justify-between items-center mt-4 custom-height">
                           <!-- Botón Atrás -->
                          <button
                              class="p-3 bg-gray-300 text-black rounded hover:bg-gray-400 shadow-md"
                              @click="goToPreviousStep"
                          >
                              <i class="pi pi-arrow-left"></i>
                          </button>

                           <!-- Contenedor para los botones de Buscar y Siguiente -->
                          <div class="flex gap-4">
                              <!-- Botón Buscar -->
                              <button
                                  class="p-3 bg-blue-500 text-white rounded-full hover:bg-blue-600 shadow-md"
                                  @click="fetchListings"
                              >
                                  <i class="pi pi-search"></i>
                              </button>

                              <!-- Botón Siguiente -->
                              <button
                                  class="p-3 bg-green-500 text-white rounded hover:bg-green-600 shadow-md"
                                  @click="goToNextStep"
                              >
                                  <i class="pi pi-arrow-right"></i>
                              </button>
                          </div>
                      </div>
          </div>
        </StepPanel>

        <!-- Paso 3 -->
        <StepPanel :value="3" class="stepper-panel">
          <div class="step-content border border-gray-300 shadow-md rounded">
            <StepThree
              :filtros="filters"
              :options="options"
              :provinces ="provinces"
              :cities ="cities"
              :zones = "zones"
              @updateFilters="updateFilters"
              @updateListings="updateListingsFromStepThree"
              @anterior="goToPreviousStep"
              @siguiente="goToNextStep"
            />
                       <!-- Contenedor de botones -->
                     <div  class="relative flex justify-between items-center mt-4 custom-height">
                           <!-- Botón Atrás -->
                          <button
                              class="p-3 bg-gray-300 text-black rounded- hover:bg-gray-400 shadow-md"
                              @click="goToPreviousStep"
                          >
                              <i class="pi pi-arrow-left"></i>
                          </button>

                           <!-- Contenedor para los botones de Buscar y Siguiente -->
                          <div class="flex gap-4">
                              <!-- Botón Buscar -->
                              <button
                                  class="p-3 bg-blue-500 text-white rounded-full hover:bg-blue-600 shadow-md"
                                  @click="fetchListings"
                              >
                                  <i class="pi pi-search"></i>
                              </button>

                              <!-- Botón Siguiente -->
                              <button
                                  class="p-3 bg-green-500 text-white rounded hover:bg-green-600 shadow-md"
                                  @click="goToNextStep"
                              >
                                  <i class="pi pi-arrow-right"></i>
                              </button>
                          </div>
                      </div>
          </div>
        </StepPanel>

        <!-- Paso 1 (ahora el último) -->
        <StepPanel :value="1" class="stepper-panel">
          <div class="step-content border border-gray-300 shadow-md rounded">
            <StepOne
              :filtros="filters"
              :options="options"
              @updateFilters="updateFilters"
                          @anterior="goToPreviousStep"
              @siguiente="goToNextStep"
            />

                   <!-- Contenedor de botones -->
                   <div  class="relative flex justify-between items-center mt-4 custom-height">
                           <!-- Botón Atrás -->
                          <button
                              class="p-3 bg-gray-300 text-black rounded hover:bg-gray-400 shadow-md"
                              @click="goToPreviousStep"
                          >
                              <i class="pi pi-arrow-left"></i>
                          </button>

                           <!-- Contenedor para los botones de Buscar y Siguiente -->
                          <div class="flex gap-4">
                              <!-- Botón Buscar -->
                              <button
                                  class="p-3 bg-blue-500 text-white rounded-full hover:bg-blue-600 shadow-md"
                                  @click="fetchListings"
                              >
                                  <i class="pi pi-search"></i>
                              </button>
                          </div>
                      </div>
                      </div>
        </StepPanel>
      </StepPanels>
          </Stepper>
        </div>
          <SummaryComponent :summaries="summaryData"  v-model:selectedCurrency="selectedCurrency" />

           <!-- Botón e InputSwitch -->
        <div class="flex justify-between items-center my-2">
        <div class="flex items-center space-x-4">
            <div class="flex justify-between items-center my-2 gap-2 flex-wrap">

            <!-- Dropdown de tipo de impresión -->
            <Dropdown
            v-model="selectedCase"
            :options="[
                { label: 'Ver ACM en folleto', value: '1' },
                { label: 'Ver ACM en paralelo', value: '2' }
            ]"
            optionLabel="label"
            optionValue="value"
            placeholder="Tipo de ACM"
            class="w-50 text-sm"
            />

            <!-- Botón imprimir -->
            <button
            class="h-9 w-9 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none shadow flex items-center justify-center"
            @click="selectedCase === '1' ? handlePrint1() : handlePrint2()"
            title="Imprimir"
            >
            <i class="pi pi-print text-lg"></i>
            </button>


            <!-- Dropdown de moneda -->
            <Dropdown
            v-model="selectedCurrency"
            :options="props.options.currencies"
            optionLabel="name"
            placeholder="Moneda"
            class="w-32 text-sm"
            >
            <template #value="slotProps">
                <span v-if="slotProps.value">
                {{ slotProps.value.symbol }} - {{ slotProps.value.name }}
                </span>
                <span v-else class="text-gray-400">Moneda</span>
            </template>
            <template #option="slotProps">
                <span>
                {{ slotProps.option.symbol }} - {{ slotProps.option.name }}
                </span>
            </template>
            </Dropdown>

        </div>
        </div>
        <div class="flex items-center space-x-2">
          <InputSwitch id="verMapa" v-model="showMap" />
          <span>{{ showMap ? "Ver Listado" : "Ver Mapa" }}</span>
        </div>
      </div>
      <div
              class="content-container mt-0"
              :class="{ 'map-view': showMap, 'list-view': !showMap }"
          >
              <div class="list-section mt-0">
                  <ListingComponent :listings="listingsData"  @summary-calculated="handleSummaryCalculated"  :total="totalMatches"  @update-data="handleSummaryCalculated"  v-model:selectedCurrency="selectedCurrency"
                    :itemsPerPage="5" />
              </div>
              <div v-if="showMap" class="map-section">
                  <Map @update-polygon="filters.latlngs = $event"  ref="mapComponent" @update-listings="updateListingsFromMap" :centerCoords="mapCenter" v-model:selectedCurrency="selectedCurrency" />
              </div>
      </div>

     </div>

      <!-- Modal para mostrar el PDF -->
     <div v-if="showPdfModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-[100]">
    <div class="bg-white rounded-lg shadow-xl w-full h-full flex flex-col">
      <!-- Encabezado del modal -->
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-xl font-semibold">Vista previa del PDF</h2>
        <button @click="showPdfModal = false" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Contenedor del PDF -->
      <div class="flex-1 overflow-hidden">
        <vue-pdf-app :pdf="pdfData" style="height: 100%; width: 100%;" />
      </div>
    </div>
     </div>
    </AppLayout>
</template>
<script setup>
import "@/assets/css/ACM/shared-styles.css";
import AppLayout from "@/Layouts/AppLayout.vue";
//import { useEventBridgeStore } from "@/Stores/eventBridge";
import { useCurrencyStore } from '@/Stores/currencyStore';
import { useEventBridgeStore } from '@/Stores/eventBridge';
import { useMarketAnalisisStore } from '@/Stores/marketAnalisis';
import axios from 'axios';
import { StepList, StepPanel } from "primevue";
import Dropdown from 'primevue/dropdown';
import InputSwitch from "primevue/inputswitch";
import Step from "primevue/step";
import Stepper from "primevue/stepper";
import { useToast } from "primevue/usetoast";
import { onMounted, ref, watch } from 'vue';
import VuePdfApp from "vue3-pdf-app";
import "vue3-pdf-app/dist/icons/main.css";
import ListingComponent from "./ListingComponent.vue";
import Map from "./map.vue";
import StepFour from "./steps/step_four.vue";
import StepOne from "./steps/step_one.vue";
import StepThree from "./steps/step_three.vue";
import StepTwo from "./steps/step_two.vue";
import SummaryComponent from "./SummaryComponent.vue";

const currencyStore = useCurrencyStore();
const marketAnalysisStore = useMarketAnalisisStore();

const mapComponent = ref(null);
//const { $apiClient } = useNuxtApp();
const toast = useToast();
const totalMatches = ref(0);

const activeStep = ref(1);
const showMap = ref(false);
const listingsData = ref([]);
const mapListings = ref([]);
const stepThreeListings = ref([]);
const combinedListings = ref([]);
const eventBridgeStore = useEventBridgeStore();
const summary = ref({});
const selectedCase = ref('1');
const selectedProperties = ref([]);
const showPdfModal = ref(false);
const pdfData = ref(null);
const closeModal = () => {
  showPdfModal.value = false;
  rutaPdf.value = null;
};

const props = defineProps({
  listings: Array,
  total_matches: Number,
  options: Object,
  step: String,
  provinces: Array,
  cities: Array,
  zones: Array,
});

console.log("currencyyyyyyyy", props.options.currencies);
console.log("localitation",  props.provinces, props.cities, props.zones);
const selectedCityId = ref(null);
const mapCenter = ref({ lat: -16.2902, lng: -63.5887, zoom: 6 });
onMounted(() => {
  const bridgeStore = useEventBridgeStore();

  bridgeStore.setClearMapCallback(() => {
    console.log('✅ Ejecutado desde el mapa (via Pinia)');
    combinedListings.value = [];

    toast.add({
      severity: "warn",
      summary: "Limpieza",
      detail: "Se han limpiado los listados del mapa y filtros.",
      life: 3000,
    });
  });
});

watch(selectedCityId, (cityId) => {
  const city = props.cities.find(c => c.id === cityId);
 console.log("este es la ciudad mandada con latitude y longitu", city);
  if (city && city.latitude && city.longitude) {
    mapCenter.value = {
      lat: parseFloat(city.latitude),
      lng: parseFloat(city.longitude),
      zoom: 13,
    };
  } else {
    mapCenter.value = {
      lat: -16.2902,
      lng: -63.5887,
      zoom: 6,
    };
  }
});

const filters = ref({
  status: null,
  start_date: null,
  end_date: null,
  minimum_price: null,
  maximum_price: null,
  market_segment: null,
  transaction_type: null,
  property_status: null,
  contract_type: null,
  state_id: null,
  province_id: null,
  city_id: null,
  zone_id: null,
  street_name: null,
  street_number: null,
  address: null,
  district: null,
  postal_code: null,
  property_type_id: null,
  market_status_id: null,
  category_id: null,
  min_rooms: null,
  max_rooms: null,
  min_bedrooms: null,
  max_bedrooms: null,
  min_toiletroom: null,
  max_toiletroom: null,
  min_bathrooms: null,
  max_bathrooms: null,
  min_parking: null,
  max_parking: null,
  min_total_sqm: null,
  max_total_sqm: null,
  min_year_built: null,
  max_year_built: null,
  min_floors: null,
  max_floors: null,
  latlngs: null,
  characteristics: null,
});
const summaryData = ref({
 "Días en el mercado": 0,
  "Precio captacion": 0,
  "Precio venta": 0,
  "Precio/M²": 0,
  "Proporción vendida a la lista": 0,
})

// Estado reactivo
const showModal = ref(false); // Controla la visibilidad del modal

function convertToArrayIfNeeded(status) {
    if (!status) {
        return null; // Si no hay valor, devuelve null
    }

    let statusArray;

    // Verificar si el estado es un string en formato JSON
    if (typeof status === "string") {
        try {
            statusArray = JSON.parse(status); // Intentar parsear la cadena JSON
        } catch (error) {
            console.error("Error al parsear el JSON:", error);
            return null;
        }
    } else if (Array.isArray(status)) {

        statusArray = status;
    } else if (status[Symbol.iterator]) {
        statusArray = Array.from(status);
    } else {
        return null;
    }

    // Eliminar duplicados del array
    const uniqueStatusArray = [...new Set(statusArray)];

    // Si el array tiene elementos, convertirlo a JSON
    return uniqueStatusArray.length > 0 ? JSON.stringify(uniqueStatusArray) : null;
}
function convertToArrayIfNeededCaracteristicas(selectedFeatures) {
    if (selectedFeatures && selectedFeatures[Symbol.iterator]) {
        const selectedFeaturesArray = Array.from(selectedFeatures); // Convierte el Proxy a un array normal
        const uniqueselectedFeaturesArray = [...new Set(selectedFeaturesArray )];

        // Si el array tiene elementos, convertirlo a una cadena JSON
        if (uniqueselectedFeaturesArray .length === 0) {
            return null; // Si está vacío, devolver null
        }

        // Devolver el array convertido a una cadena JSON
        return JSON.stringify(uniqueselectedFeaturesArray);
    }

    return null; // Si no es un array o no tiene elementos, devolver null
}

function prepareFilters() {
    filters.value.status = convertToArrayIfNeeded(filters.value.status);
	filters.value.selectedFeatures = convertToArrayIfNeededCaracteristicas(filters.value.selectedFeatures);
}
// Preparar filtros para el backend
const prepareFiltersForBackend = () => ({
  ...filters.value,
  start_date: formatDate(filters.value.start_date),
  end_date: formatDate(filters.value.end_date),

});

const formatDate = (date) => (date ? new Date(date).toISOString().split("T")[0] : null);

// Actualizar filtros
const updateFilters =  (updatedFields) => {Object.assign(filters.value, updatedFields);}
const emit = defineEmits(["update-listings", 'update:selectedCurrency']); // Registra el evento emitido por este componente
const isLoading = ref(false);
const fetchListings = async () => {
    summaryData.value = {
      "Días en el mercado": 0,
      "Precio captacion": 0,
      "Precio venta": 0,
      "Precio/M²": 0,
      "Proporción vendida a la lista": 0,
    };
    selectedProperties.value = [];

    // 🧼 Limpiar también el store global de Pinia
    marketAnalysisStore.setSummaryData({});
    marketAnalysisStore.setSelectedProperties([]);
  try {
    isLoading.value = true;
    const loadingToast = toast.add({
        severity: "info",
        summary: "Cargando...",
        detail: "Buscando propiedades, por favor espere.",
        life: 999999,
        closable: false,
    });

    prepareFilters();
    const preparedFilters = prepareFiltersForBackend();
    console.log("📤 Enviando filtros al backend:", preparedFilters);

    const response = await axios.post('/marketanalysis/search', preparedFilters, {

});
   console.log("📥 Respuesta completa:", response);



   totalMatches.value = response.data.total_matches;
    console.log("total maches", totalMatches.value);
    if (!response.data.listings) {
      console.warn("⚠️ No se encontraron listados.");
      return;
    }
    //const total_matches = response.data.listings.map((listing) => ({}));
    const backendListings = response.data.listings.map((listing) => ({
      id: listing.id,
      key: listing.key,
      title: listing.title,
      description: listing.description,
      fecha_de_carga: listing.fecha_de_carga,
      MLSID: listing.MLSID,
      status: listing.status,
      prices: listing.prices,
	  address: listing.address,
      multimedias: listing.multimedias,
      longitude: listing.location.longitude,
      latitude: listing.location.latitude,
      days_in_market: listing.days_in_market,
      usd_per_m2: listing.usd_per_m2,
      subtype_name: listing.subtype_name,
      year_built: listing.year_built,
    }));

    console.log("✅ Listados obtenidos desde el backend:", backendListings);
    isLoading.value = false;
    toast.removeAllGroups();
    stepThreeListings.value = backendListings;
    updateCombinedListings({  fromStep: true });
  } catch (error) {
    console.error("❌ Error al obtener listados:", error);
  }
};


const updateCombinedListings = (fromStep = false ) => {
  if (Array.isArray(stepThreeListings.value)) {
    combinedListings.value = [...stepThreeListings.value];
    console.log("📤 Dibujando puntos en el mapa desde los datos de los pasos.");
    mapComponent.value.updateMapPoints(combinedListings.value);

    toast.add({
      severity: "info",
      summary: "Datos desde la Búsqueda",
      detail: "Se actualizaron los datos basados en la búsqueda.",
      life: 5000,
    });

  } else {
    combinedListings.value = [];
    console.warn("⚠️ No hay datos para mostrar.");

    toast.add({
      severity: "warn",
      summary: "Sin Resultados",
      detail: "No hay datos disponibles para mostrar en el mapa.",
      life: 5000,
    });
  }
  listingsData.value = [...combinedListings.value];

  console.log("🔄 Listados combinados actualizados:", combinedListings.value);
};

const selectedCurrency = ref(props.options.currencies?.[0] || {})
/*const selectedCurrency = props.options.currencies?.[0] || {};
if (!currencyStore.selectedCurrency?.id && selectedCurrency?.id) {
  currencyStore.setCurrency(selectedCurrency);
  console.log("✅ Moneda por defecto seteada en el store:", selectedCurrency);
}*/
watch(
  () => [combinedListings.value, showModal.value],
  ([newListings, isVisible]) => {
    console.log("🔄 Cambio detectado en para el watch :", newListings, isVisible);

    listingsData.value = [...newListings];
    if (newListings && newListings.length > 0) {
      localStorage.setItem("lastCombinedListings", JSON.stringify(newListings));
      console.log("💾 Guardando en caché la última combinación de listados.");
    }
  },
  { deep: true, immediate: true }
);
watch(selectedCurrency, (newVal) => {
  console.log("▶ Se emite y guarda la monEDA:", newVal);
  emit('update:selectedCurrency', newVal);
  console.log("▶ Se guarda  moneda en el store:", newVal);
  currencyStore.setCurrency(newVal);
});
onMounted(() => {
  const cachedCombinedListings = localStorage.getItem("lastCombinedListings");

  if (cachedCombinedListings) {
    console.log("📤 Cargando últimos listados combinados desde caché...");
    combinedListings.value = JSON.parse(cachedCombinedListings);
    listingsData.value = [...combinedListings.value]; // Sincronizar con la vista
  }

  /*const cachedFilters = localStorage.getItem("lastFilters");
    if (cachedFilters) {
    console.log("📤 Cargando filtros desde localStorage...");
    filters.value = JSON.parse(cachedFilters);
    console.log("cambios de filters", filters.value);
    //fetchListings();
    }*/

})

function handleSummaryCalculated(data) {

	console.log('📥 Datos recibidos desde el hijo:', data);
  summaryData.value = data.summary || {};
  selectedProperties.value = data.selectedProperties || [];

  marketAnalysisStore.setSummaryData(data.summary);
  marketAnalysisStore.setSelectedProperties(data.selectedProperties);

  console.log("📊 Datos actualizados en el store:", {
    summary: marketAnalysisStore.summary,
    selectedProperties: marketAnalysisStore.selectedProperties,
  });
}

// Clases CSS dinámicas para los pasos
const stepClasses = (step) => {
  return [
    "rounded-full border-2 w-12 h-12 flex items-center justify-center",
    {
      "bg-primary text-primary-contrast border-primary": activeStep.value === step,
      "border-surface-200 dark:border-surface-700": activeStep.value !== step,
    },
  ];
};
// Orden personalizado de los pasos
const stepOrder = [4, 2, 3, 1];

// Validar que el paso exista en el orden personalizado
const isStepValid = (step) => stepOrder.includes(step);

const setStep = (step) => {
  if (isStepValid(step)) {
    activeStep.value = step;
  } else {
    console.warn(`⚠️ Paso inválido: ${step}`);
  }
};

// Avanzar al siguiente paso según el orden personalizado
const goToNextStep = () => {
  const currentIndex = stepOrder.indexOf(activeStep.value);
  if (currentIndex < stepOrder.length - 1) {
    activeStep.value = stepOrder[currentIndex + 1];
  } else {
    console.log("✅ Ya estás en el último paso.");
  }
};

// Retroceder al paso anterior según el orden personalizado
const goToPreviousStep = () => {
  const currentIndex = stepOrder.indexOf(activeStep.value);
  if (currentIndex > 0) {
    activeStep.value = stepOrder[currentIndex - 1];
  } else {
    console.log("✅ Ya estás en el primer paso.");
  }
};

// Inicializar el primer paso en el orden personalizado
activeStep.value = stepOrder[0];


const handlePrint2 = async () => {

	console.log("📥 Datos actuales para enviar al backend:", {
	  selectedProperties: marketAnalysisStore.selectedProperties,
	  summary: marketAnalysisStore.summary,
	  selectedCase: selectedCase.value,
      selectedCurrency: currencyStore.selectedCurrency,
	  });

      try {
        const response = await axios.post('/marketanalysis/imprimir/caso_two',
		{
		selectedProperties: marketAnalysisStore.selectedProperties,
        summary: marketAnalysisStore.summary,
        selectedCase: selectedCase.value,
        selectedCurrency: currencyStore.selectedCurrency,
        }, { responseType: 'blob' });

        if (!response.data) {
          console.error("Error: No se generó el PDF correctamente");
          return;
        }
        console.log("Tipo MIME del Blob:", response.data.type); // Debería ser "application/pdf"
        console.log("Tamaño del Blob:", response.data.size); // Debería ser mayor que 0

				 // Convertir Blob a Uint8Array
         const arrayBuffer = await response.data.arrayBuffer();
        pdfData.value = new Uint8Array(arrayBuffer);

        // Mostrar el modal con el PDF
        showPdfModal.value = true;
      } catch (error) {
        console.error("Error fetching PDF:", error);
      }
    // Abrir el PDF en una nueva pestaña del navegador
   // window.open(pdfUrl, '_blank'); // Esta línea abre el PDF en una nueva pestaña
};

const handlePrint1 = async () =>  {
			console.log(" Datos actuales para enviar al backend:", {
				selectedProperties: marketAnalysisStore.selectedProperties,
				summary: marketAnalysisStore.summary,
				selectedCase: selectedCase.value,
                selectedCurrency: currencyStore.selectedCurrency,
				//image: imageBase64,
			});
      try {
        // Llamada al backend para generar el PDF
        const response = await axios.post('/marketanalysis/imprimir/caso_one', {
		selectedProperties: marketAnalysisStore.selectedProperties,
        summary: marketAnalysisStore.summary,
        selectedCase: selectedCase.value,
        selectedCurrency: currencyStore.selectedCurrency,
        //image: imageBase64,
        }, { responseType: 'blob' });

        if (!response.data) {
          console.error("Error: No se generó el PDF correctamente");
          return;
        }
        const arrayBuffer = await response.data.arrayBuffer();
        pdfData.value = new Uint8Array(arrayBuffer);

        // Mostrar el modal con el PDF
        showPdfModal.value = true;

        // Crear una URL del blob para el PDF recibido
       // const pdfUrl = URL.createObjectURL(response.data);

        // O abrir el PDF en una nueva pestaña del navegador
        // window.open(pdfUrl, '_blank'); // Descomenta esto si prefieres abrirlo en una nueva pestaña

      } catch (error) {
        console.error("Error al generar o visualizar el PDF:", error);
      }
};

</script>
<style scoped>
.modal-container {
  display: flex;
  align-items: center;
  justify-content: center;
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 50;
}
.custom-height {
  height: 45px;
  align-items: center;
}

.flex {
  margin-bottom: 0.2rem;
}
.map-section,
.list-section {
  margin-top: 0.0rem;
  height: 100%;
  overflow-y: auto
}
.acm-container {
  width: 100%;
  margin: 0 auto;
  padding: 20px;
  background-color: #e3f2fd;
}

.stepper-wrapper {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}
.card {
  width: 100%;
  max-width: 100%;
}


.stepper-panel {

  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}


.p-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: flex-start;
}

.p-col-12 {
  flex: 1 1 100%;
}


@media (min-width: 768px) {
  .p-col-12.p-md-6 {
    flex: 1 1 calc(50% - 1rem);
  }

  .p-col-12.p-md-4 {
    flex: 1 1 calc(33.33% - 1rem);
  }
}

.stepper-container {
  background: linear-gradient(to right, #e3f2fd, #e3f2fd);
  border-radius: 10px;
  padding: 10px;
  margin-bottom: 20px;
}

.step-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.step-title {
	font-size: 14px;
  font-weight: bold;
  color: #000000;
}
.acm-main-title {
  text-align: center;
  font-size: 24px;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
}
.step-button {
  border: none;
  background: transparent;
  cursor: pointer;
}


.content-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Vista de listado completo */
.content-container.list-view .list-section {
  flex: 1;
  width: 100%;
}

.content-container.list-view .map-section {
height: 1000px;
  display: none;
}

/* Vista con mapa y listado lado a lado */
.content-container.map-view {
  flex-direction: row;
  height: 780px;
}

.content-container.map-view .list-section {
  flex: 1;
  width: 50%;
  height: 1000px;
}

.content-container.map-view .map-section {
  flex: 1;
  width: 50%;
  display: block;
}


</style>
