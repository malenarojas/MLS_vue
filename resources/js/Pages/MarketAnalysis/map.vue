<script setup>
import defaultImage from '@/assets/img/pointer_1.png';
import { useLeafletMapmarket } from '@/Composables/useLeafletMapanalysisMarket';
import { useMarketAnalisisStore } from '@/Stores/marketAnalisis';
import { useEventBridgeStore } from '@/Stores/eventBridge'
import { useToast } from "primevue/usetoast";
import { onMounted, onUnmounted, ref ,watch, watchEffect} from 'vue';
const toast = useToast();
const mapElement = ref(null);
const { initializeMap, addMarker, destroyMap, clearMarkers, clearAllListings, getMap } = useLeafletMapmarket();
const marketAnalysisStore = useMarketAnalisisStore();
const listings = ref([]);
const eventBridge = useEventBridgeStore();
const emit = defineEmits(['update-listings','clear-map-listings', 'update-polygon']);
const localListings = ref([]);

const handleClearAll = () => {
    clearAllListings();
    listings.value = [];
    emit('update-listings', []);
    emit('clear-map-listings');

    toast.add({
        severity: "info",
        summary: "Mapa limpio",
        detail: "Se han eliminado todas las selecciones y marcadores.",
        life: 4000,
    });

    console.log("🧹 Mapa y listados limpiados exitosamente.");
};

const props = defineProps({
  updatedListings: {
    type: Array,
    default: () => [],
  },
  centerCoords: {
    type: Object,
    default: () => ({
      lat: -16.2902,
      lng: -63.5887,
      zoom: 6
    }),
  },
  selectedCurrency: {
    type: Object,
    default: () => ({
      symbol: 'Bs',
      code: 'BOB'
    })
  }
});
const generateCirclePolygon = (center, radius, numPoints = 32) => {
  const latlngs = [];
  const angleStep = (2 * Math.PI) / numPoints;

  for (let i = 0; i < numPoints; i++) {
    const angle = i * angleStep;
    const latOffset = (radius / 111320) * Math.cos(angle);
    const lngOffset = (radius / (111320 * Math.cos((center.lat * Math.PI) / 180))) * Math.sin(angle);

    latlngs.push({ lat: center.lat + latOffset, lng: center.lng + lngOffset });
  }

  return latlngs;
};

function selectCorrectAmount(price) {
  const currencySymbol = props.selectedCurrency?.symbol || 'Bs'

  if (currencySymbol === 'Bs' || currencySymbol === 'BOB') {
    return price?.price_in_bolivianos ?? price?.amount
  }

  if (currencySymbol === '$' || currencySymbol === 'USD') {
    return price?.price_in_dollars ?? price?.amount
  }

  return price?.amount
}

function getCurrencyCode() {
  const symbol = props.selectedCurrency?.symbol
  if (symbol === 'Bs' || symbol === 'BOB') return 'BOB'
  if (symbol === '$' || symbol === 'USD') return 'USD'
  return 'BOB'
}

function formatPrice(value, currencyCode = 'BOB') {
  return new Intl.NumberFormat('es-BO', {
    style: 'currency',
    currency: currencyCode
  }).format(value)
}


const onShapeChange = async (data) => {
  if (!data) {
    console.error('❌ No se detectaron datos para enviar al backend.');
    return;
  }

  let payload = {};

  if (data.type === 'polygon') {
    payload = {
      type: 'polygon',
      latlngs: data.latlngs[0].map(({ lat, lng }) => ({ lat, lng })),
    };
    emit('update-polygon', payload.latlngs);

  } else if (data.type === 'circle') {
    const circleLatLngs = generateCirclePolygon(data.center, data.radius, 32);
    payload = {
      type: 'polygon',
      latlngs: circleLatLngs,
    };
    emit('update-polygon', payload.latlngs);

  } else {
    console.warn('⚠️ Tipo de forma no soportado:', data.type);
    return;
  }

  console.log('📤 Enviando datos al backend:', payload);

};
const updateMapPoints = (newListings) => {
  if (!Array.isArray(newListings)) {
    console.warn('⚠️ No hay listados válidos para actualizar en el mapa.');
    return;
  }

  console.log('📍 Actualizando puntos en el mapa:', newListings);
  localListings.value = [...newListings]
  clearAllListings();

  newListings.forEach((listing) => {
    let lat = null;
    let lng = null;

    if (listing.location && listing.location.latitude && listing.location.longitude) {
      lat = parseFloat(listing.location.latitude);
      lng = parseFloat(listing.location.longitude);
    }
    else if (listing.latitude && listing.longitude) {
      lat = parseFloat(listing.latitude);
      lng = parseFloat(listing.longitude);
    }

    if (lat && lng) {
        const priceObj = listing.prices
        const amount = selectCorrectAmount(priceObj)
        const currency = getCurrencyCode()
        const price = priceObj ? formatPrice(amount, currency) : 'Sin precio'

      addMarker(lat, lng, '', defaultImage, price, 'row-' + listing.id);
    } else {
      console.warn(`⚠️ Datos inválidos para marcador:`, listing);
    }
  });
};
watch(
  () => props.selectedCurrency,
  () => {
    console.log("💱 Moneda cambiada → redibujando marcadores con localListings")
    updateMapPoints(localListings.value) // 🔁 redibuja con nueva moneda
  },
  { immediate: true }
);
let moveListenerAttached = false;

watchEffect(() => {
  const map = getMap();
  const hasPoints = localListings.value.length > 0;

  if (map && hasPoints && !moveListenerAttached) {
    moveListenerAttached = true;

    console.log("✅ Registrando 'moveend' en el mapa (con puntos)");
    map.on('moveend', () => {
      const bounds = map.getBounds();

      const visible = localListings.value.filter((listing) => {
        const lat = listing.latitude || listing?.location?.latitude;
        const lng = listing.longitude || listing?.location?.longitude;
        return lat && lng && bounds.contains([parseFloat(lat), parseFloat(lng)]);
      });

      if (visible.length > 0) {
        console.log("📍 Puntos visibles tras zoom/move:", visible.map(v => v.id));
        emit('update-visible-listings', visible);
        eventBridge.updateVisibleListings(visible);
      } else {
        console.log("😶 No hay puntos visibles tras el movimiento.");
      }
    });
  }

  if (!hasPoints && moveListenerAttached && map) {
    console.log("🧹 Quitando 'moveend' porque no hay puntos");
    map.off('moveend');
    moveListenerAttached = false;
  }
});


defineExpose({
  updateMapPoints,
  handleClearAll
});

// Vigilar cambios en `listings` y actualizar el mapa automáticamente
/*watch(listings, (newListings) => {
  console.log('🔄 Detectando cambios en `listings para el mapa`:', newListings);
  updateMapPoints(newListings);
});*/

onMounted(() => {
    initializeMap(mapElement.value, {
    center: [props.centerCoords.lat, props.centerCoords.lng],
    zoom: props.centerCoords.zoom
  }, onShapeChange);
});

onUnmounted(() => {
  destroyMap();
  emit('update-visible-listings', []); // Limpia la tabla si usa este evento
  eventBridge.updateVisibleListings([]);
  emit('update-polygon', null);

  console.log("🧹 Mapa destruido, listados visibles y polígono reseteados");
});
</script>


<template>
  <div class="map-container">
    <!-- Mapa principal -->
    <div ref="mapElement" class="map z-50" ></div>

    <!-- Botón para abrir el modal
    <button class="modal-button" @click="openModal">Ver mapa completo</button> -->

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <!-- Botón para cerrar el modal -->
        <button class="close-button" @click="closeModal">X</button>
        <div ref="modalMapElement" class="modal-map"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.map-container {
  margin-top: 2rem;
}

.map {
  width: 100%;
  height: 780px; /* Ajusta la altura según tus necesidades */
  border: 1px solid #ddd;
  border-radius: 4px;
}

.buttons {
  margin-bottom: 1rem;
  display: flex;
  gap: 1rem;
}

button {
  padding: 0.5rem 1rem;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}
.custom-tooltip {
  background-color: white;
  color: #333;
  font-size: 12px;
  font-weight: bold;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 4px 8px;
}

</style>
