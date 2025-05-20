<template>
    <div class="listing-wrapper">
  <div class="listing-container mt-8 p-4 border border-gray-300 rounded-lg bg-white shadow-sm">
				<div class="flex justify-between items-center mb-4">
			<!-- Título de la lista -->
			<h2 class="text-lg font-semibold">Lista de Propiedades</h2>

			<!-- Total de propiedades -->
			<p class="text-sm text-gray-600">
				Total de coicidencias: {{ props.total }}
			</p>
			<p class="text-sm text-gray-600">
				Total de propiedades: {{ props.listings.length }}
			</p>
		</div>
    <!-- Checkbox para seleccionar todos -->
    <div v-if="props.listings.length" class="flex items-center mb-4">
      <Checkbox
        v-model="selectAll"
        binary
        @change="toggleSelectAll"
        class="mr-2"
      />
      <label class="text-sm text-gray-700">Seleccionar Todos</label>
    </div>

    <!-- Tabla normal (sin VirtualScroller) -->
    <div class="overflow-auto border border-gray-300 rounded" style="max-height: 630px;">
      <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
          <tr class="bg-gray-200 text-gray-700 text-sm">
            <th class="p-1 border"></th>
            <th class="p-2 border w-64 text-center">Propiedad</th>
            <th class="p-2 border">Tipo Propiedad</th>
            <th class="p-2 border">Estatus</th>
            <th class="p-2 border">Año Construcción</th>
            <th class="p-2 border">Precio/M²</th>
            <th class="p-2 border">Fecha de listado</th>
            <th class="p-2 border">Días en el mercado</th>
            <th class="p-2 border">Prospectos</th>
            <th class="p-2 border">Comp. potencia</th>
          </tr>
        </thead>

        <tbody>
            <tr
            v-for="(item, index) in combinedListings"
            :key="item.id"
            :id="'row-' + item.id"
            class="text-gray-700 text-sm hover:bg-gray-100"
            >

            <td class="p-2 border">
              <Checkbox
                v-model="selectedProperties"
                :value="item"
                @change="emitSelection"
              />
            </td>
            <td class="p-2 border w-64">
  <img
    :src="item.multimedias && item.multimedias.length > 0
          ? getFullImageUrl(item.multimedias[0].link)
          : defaultImage"
    alt="Imagen de propiedad"
    class="w-full h-36 object-cover rounded"
    style="width: 230px; height: 160px"
  />

  <!-- Precio -->
  <div class="text-sm text-gray-700 mt-2">
    <span v-if="item.prices">
      {{ formatPrice(selectCorrectAmount(item.prices), getCurrencyCode()) }}
    </span>
    <span v-else>Sin precio</span>
  </div>
  <a
  :href="`/marketanalysis/show?listing=${item.key}`"
  class="text-sm font-semibold text-blue-800 hover:underline truncate"
  :title="item.MLSID"
>
  {{ item.MLSID || 'N/A' }}
</a>
</td>
            <td class="p-2 border">{{ item.subtype_name || "N/A" }}</td>
            <td class="p-2 border">{{ item.status || "N/A" }}</td>
            <td class="p-2 border">{{ item.year_built || "N/A" }}</td>
            <td class="p-2 border">
              {{ item.usd_per_m2 || "N/A" }}
            </td>
            <td class="p-2 border">{{ item.fecha_de_carga || "N/A" }}</td>
            <td class="p-2 border">{{ item.days_in_market || "N/A" }}</td>
            <td class="p-2 border">0</td>
            <td class="p-2 border">0</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</template>

<script setup>
import defaultImage from '@/assets/img/default.gif';
import { useEventBridgeStore } from '@/Stores/eventBridge';
import { storeToRefs } from 'pinia';
import Checkbox from 'primevue/checkbox';
import { computed, defineEmits, defineProps, ref, watch } from 'vue';


const props = defineProps({
  listings: {
    type: Array,
    default: () => []
  },
  selectedCurrency: {
    type: Array,
    default: () => []
  },
  total: Number,
})
const selectAll = ref(false)
const selectedProperties = ref([])
const eventBridge = useEventBridgeStore()
const { visibleListings } = storeToRefs(eventBridge)
watch(
  () => props.listings,
  (newVal) => {
    console.log('📦 Listado actualizado de precios:', newVal)
  },
  { immediate: true, deep: true }
)
watch(
  () => props.selectedCurrency,
  (newCurrency) => {
    console.log('💱 Moneda seleccionada cambió a:', newCurrency)
    selectedProperties.value = []
    //emitSelectionAndSummary()
  },
  { immediate: true }
)
watch(visibleListings, (newListings) => {
  console.log('👀 Listados visibles desde el mapa actualizados:', newListings)
})
const combinedListings = computed(() => {
  const visibles = visibleListings.value.map(v => v.id)
  const visiblesPrimero = props.listings.filter(l => visibles.includes(l.id))
  const noVisibles = props.listings.filter(l => !visibles.includes(l.id))

  return [...visiblesPrimero, ...noVisibles]
})


const getFullImageUrl = (relativeUrl) => {
  return relativeUrl || ""; // Si es null o undefined, retorna un string vacío
};




// Emit al padre
const emit = defineEmits(['update-selected','summary-calculated', 'update-data'])

function emitSelection() {
  emit('update-selected', selectedProperties.value)
}

function emitSelectionAndSummary() {
  const total = selectedProperties.value.length;
  let sumDays = 0;
  let sumUsdPerM2 = 0;
  let sumusdsold = 0;

	let sumusdcatchment = 0;
	let countSoldPrices = 0;
	let countCatchmentPrices = 0;


  selectedProperties.value.forEach((props) => {
    console.log("sumo dias de mercado ");
    sumDays += props.days_in_market || 0;
    sumUsdPerM2 += props.usd_per_m2 || 0;
        console.log("agente status existe ",props.status_id);
      if (props.status === 'Venta Aceptada/Vendida' || props.status === 'Alquilado') {
        console.log("propiedad vendida sumar");
        sumusdsold += selectCorrectAmount(props.prices) || 0
        console.log("cambios de suma vendida", sumusdsold);
        countSoldPrices += props.prices ? 1 : 0;
      } else {
        console.log("propiedad precio captacion sumar");
        sumusdcatchment += selectCorrectAmount(props.prices) || 0
        console.log("cambios de suma de captacion", sumusdcatchment);
        countCatchmentPrices += props.prices ? 1 : 0;
      }

  });

  // Calcular promedios
  const avgDays = total > 0 ? Math.round(sumDays / total) : 0;
  const avgUsd = total > 0 ? parseFloat((sumUsdPerM2 / total).toFixed(2)) : 0.0;

  const avgUsdSold = countSoldPrices > 0 ? parseFloat((sumusdsold / countSoldPrices).toFixed(2)) : 0.0;
  const avgUsdCatchment = countCatchmentPrices > 0 ? parseFloat((sumusdcatchment / countCatchmentPrices).toFixed(2)) : 0.0;


	const dataToEmit = {
    selectedProperties: selectedProperties.value,
    summary: {
      'Días en el mercado': avgDays,
      'Precio/M²': avgUsd,
			'Precio captacion' : avgUsdCatchment,
			'Precio venta' : avgUsdSold,

    },
  };

  console.log('📤 Datos emitidos desde el componente hijo:', dataToEmit);
  emit('update-data', dataToEmit);
}

watch(
  selectedProperties,
	emitSelectionAndSummary,
  { deep: true }
);



// Computed: ¿todas seleccionadas?
const isAllSelected = computed(() => {
  return (
    selectedProperties.value.length === props.listings.length &&
    props.listings.length > 0
  )
})

function toggleSelectAll() {
  if (isAllSelected.value) {
    selectedProperties.value = []
  } else {
    // seleccionar todos
    selectedProperties.value = [...props.listings]
  }
  emitSelectionAndSummary();
}

function selectCorrectAmount(price) {
  const currencySymbol = props.selectedCurrency?.symbol || 'Bs'

  if (currencySymbol === 'Bs' || currencySymbol === 'BOB') {
    return price.price_in_bolivianos ?? price.amount
  }

  if (currencySymbol === '$' || currencySymbol === 'USD') {
    return price.price_in_dollars ?? price.amount
  }

  return price.amount
}

function formatPrice(value, currencyCode = 'BOB') {
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: currencyCode
  }).format(value)
}
function getCurrencyCode() {
  const symbol = props.selectedCurrency?.symbol

  if (symbol === 'Bs' || symbol === 'BOB') return 'BOB'
  if (symbol === '$' || symbol === 'USD') return 'USD'

  return 'BOB' // ✅ Siempre por defecto a bolivianos
}




/*watch(
  () => props.listings,
  (newListings) => {
    if (!Array.isArray(newListings) || newListings.length === 0) {
      console.log("❌ No hay listings disponibles.");
      return;
    }

    newListings.forEach((listings, index) => {
      if (listings.multimedias && listings.multimedias.length > 0) {
        console.log(`✅ Listing ${index + 1} tiene imágenes:`, listings.multimedias);
        console.log(`📸 URL de la imagen:`, getFullImageUrl(listings.multimedias[0].link));
      } else {
        console.log(`❌ Listing ${index + 1} no tiene imágenes.`);
        console.log(`❌ Listing  no tiene imágenes.`, listings.multimedias);
      }
    });
  },
  { deep: true, immediate: true }
);*/
</script>


<style scoped>
/* Para que la tabla sea realmente responsiva y se adapte:
   - Usa table-layout: auto si quieres que se expanda al contenido
   - Usa overflow-auto en el contenedor si quieres scroll horizontal
*/
.listing-wrapper {
  height: 100%;            /* 🔄 hereda el alto desde el padre (.list-section) */
  display: flex;
  flex-direction: column;
}

.table-auto {
  table-layout: auto; /* o 'fixed' si prefieres anchos fijos */
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  white-space: nowrap; /* Evita saltos de línea. Quita si quieres que el texto se parta */
}
/* Ejemplo: selector por nth-child si la imagen es la 2da columna */
table th:nth-child(2),
table td:nth-child(2) {
  width: 300px; /* Ajusta a gusto */
}

/* O con una clase custom */
.img-col {
  width: 300px;
}
.highlighted-row {
  background-color: #e0f2fe !important; /* Azul clarito */
  border-left: 4px solid #3b82f6; /* Azul Tailwind */
  transition: all 0.3s ease;
}


</style>


