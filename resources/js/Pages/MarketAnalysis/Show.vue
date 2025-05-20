<template>
    <AppLayout title="Agents Show">
  <div class=" w-full bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <!-- Botón para volver -->
    <Link href="/marketanalysis" class="bg-blue-600 text-white px-4 py-2 rounded-lg mb-6 hover:bg-blue-700 transition">
    ⬅ Volver a la Lista
    </Link>
    <!-- Contenedor principal -->
    <div v-if="listing" class="bg-white shadow-xl rounded-lg p-6">
      <!-- 🔹 Título y Ubicación -->
      <!-- Contenedor con flex y responsive -->
      <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-4 p-4 bg-white rounded shadow">

      <!-- Columna / item: MLSID -->
      <div>
        <h3 class="text-xl font-semibold text-blue-800">MLS: {{ listing?.MLSID || 'No tiene MLSID' }}</h3>
      </div>

      <!-- Columna / item: Ubicación -->
      <div class="text-xl font-semibold text-blue-800">
        <span class="font-medium">Ubicación:</span>
        {{ listing.location?.city?.name || 'Sin ciudad'}}, {{ listing.location?.zone?.name || 'Sin zona' }}
      </div>

      <!-- Columna / item: Estado -->
      <div class="text-sm font-medium text-blue-700">
        Estado: {{ listing.status_listing?.name || 'Sin estado'}}
      </div>

      <!-- Columna / item: Precio -->
      <div class="text-lg font-bold text-green-700">
        💰
        {{ selectCorrectAmount(listing.prices)
      ? formatPrice(selectCorrectAmount(listing.prices), getCurrencyCode())
      : 'Sin precio definido' }}
      </div>

      </div>

       <!-- 🔹 Contenedor del Carrusel y el Mapa en la misma fila -->
        <div class="flex flex-col md:flex-row items-center mb-6 gap-4">
        <!-- 🖼 Carrusel de imágenes (75% en pantallas grandes) -->
        <div class="relative w-full md:w-3/4 ">
        <swiper
            :style="{
            '--swiper-navigation-color': '#2563eb',
            '--swiper-pagination-color': '#fff',
            }"
            :loop="true"
            :spaceBetween="10"
            :navigation="true"
            :pagination="{ clickable: true }"
            :thumbs="{ swiper: thumbsSwiper || null }"
            :modules="modules"
            class="mySwiper2 rounded-lg shadow-lg"
        >
            <swiper-slide v-for="(image, index) in mainImages" :key="index">
            <img :src="getFullImageUrl(image.link)"
                alt="Imagen de propiedad"
                class="w-full max-h-[500px] object-contain rounded-lg"/>
            </swiper-slide>
        </swiper>

        <!-- 🔹 Miniaturas del carrusel -->
        <swiper
            @swiper="setThumbsSwiper"
            :loop="true"
            :spaceBetween="10"
            :slidesPerView="4"
            :freeMode="true"
            :watchSlidesProgress="true"
            :modules="modules"
            class="mySwiper mt-2"
        >
            <swiper-slide v-for="(image, index) in mainImages" :key="'thumb-' + index">
            <img :src="getFullImageUrl(image.link)"
                alt="Miniatura"
                class="w-full max-h-[100px] object-cover rounded-md"/>
            </swiper-slide>
        </swiper>
        </div>
        <!--  Mapa de Ubicación (25% en pantallas grandes) -->
        <div class="relative w-full md:w-1/4 max-h-[800px] h-full">
          <div ref="mapElement" class="h-80 bg-gray-200 rounded-lg shadow-md z-50"></div>
        </div>
    </div>
      <!-- 🔹 Contenedor con DOS COLUMNAS -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 📌 Columna Izquierda: Detalles de la Propiedad -->
        <div class="md:col-span-2 bg-gray-50 p-6 rounded-lg shadow-md border border-blue-100">
          <h4 class="text-xl font-semibold text-blue-800 mb-4">Detalles de la Propiedad</h4>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <p><span class="text-gray-600">🛏 Habitaciones:</span> {{ listing.listing_information?.number_bedrooms || "N/A" }}</p>
            <p><span class="text-gray-600">🛁 Baños:</span> {{ listing.listing_information?.number_bathrooms || "N/A" }}</p>
            <p><span class="text-gray-600">📅 Fecha de Captación:</span> {{ formatDate(listing.date_of_listing) }}</p>
            <p><span class="text-gray-600">📆 Fin de Contrato:</span> {{ formatDate(listing.contract_end_date) }}</p>
            <p><span class="text-gray-600">📊 Días en el Mercado:</span> {{ listing.days_in_market }}</p>
            <p><span class="text-gray-600">📄 Tipo de Contrato:</span> {{ listing.contract_type?.name || "No especificado" }}</p>
            <p><span class="text-gray-600">🏡 Tipo de Propiedad:</span> {{ listing.listing_information?.subtype_property?.name || "No especificado" }}</p>
          </div>

          <div class="grid grid-cols-1 gap-4 text-sm">
            <p><span class="text-gray-600"> Description del  sitio:</span> {{ listing.marketing_description|| "N/A" }}</p>
          </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 text-center w-full max-w-sm mx-auto">

<!-- Imagen del agente -->
<img
  :src="listing.agents[0]?.image_url || defaultAgentImage"
  alt="Agente"
  class="w-48 h-48 h-auto object-cover rounded-2xl shadow-lg border-4 border-blue-300 mx-auto mt-0"
/>


<!-- Nombre -->
<h2 class="text-lg font-semibold text-gray-800 mt-4">
  {{ listing.agents[0]?.user?.name_to_show || 'Agente Desconocido' }}
</h2>

<!-- Rol / título -->
<p class="text-sm text-gray-500">Agente Asociado</p>

<!-- Oficina -->
<p class="text-sm text-gray-700 mt-1">
  {{ listing.agents[0]?.office?.name || "RE/MAX No Disponible" }}
</p>

<!-- Listings -->
<p class="text-sm text-blue-600 font-medium mt-1">
  Listings {{ listing.agents[0]?.listing_count || 0 }}
</p>

<!-- Contactos rápidos
<div class="flex justify-center gap-4 mt-4">
  <a
    :href="'tel:' + listing.agents[0]?.user?.phone_number"
    target="_blank"
    class="text-blue-600 hover:text-blue-800 text-xl"
    title="Llamar"
  >
    <i class="pi pi-phone"></i>
  </a>
  <a
    :href="'mailto:' + listing.agents[0]?.user?.email"
    target="_blank"
    class="text-red-500 hover:text-red-700 text-xl"
    title="Correo"
  >
    <i class="pi pi-envelope"></i>
  </a>
  <a
    v-if="listing.agents[0]?.user?.phone_number"
    :href="`https://wa.me/${listing.agents[0]?.user?.phone_number.replace('+', '')}`"
    target="_blank"
    class="text-green-500 hover:text-green-700 text-xl"
    title="WhatsApp"
  >
    <i class="pi pi-whatsapp"></i>
  </a>
</div>-->

<!-- Teléfono y correo -->
<div class="mt-4 text-sm text-gray-600">
  <div class="flex items-center justify-center gap-2 mb-2">
    <i class="pi pi-phone text-red-500"></i>
    <span>{{ listing.agents[0]?.user?.phone_number || 'Sin teléfono' }}</span>
  </div>
  <div class="flex items-center justify-center gap-2">
    <i class="pi pi-envelope text-gray-500"></i>
    <span>{{ listing.agents[0]?.user?.email || 'Sin correo' }}</span>
  </div>
</div>

<!-- Botones -->
<div class="mt-6 flex flex-col gap-2">
  <button class="border border-blue-500 text-blue-600 py-1 rounded-lg hover:bg-blue-50 transition">Ver mi perfil</button>
  <!--<button class="border border-blue-500 text-blue-600 py-1 rounded-lg hover:bg-blue-50 transition">Reservar una visita</button>-->
</div>

<!-- Footer Oficina -->
<div class="mt-6 border-t pt-4 text-sm text-gray-500">
  <p class="font-semibold text-gray-700">{{ listing.agents[0]?.office?.name || 'RE/MAX' }}</p>
  <p>Av. Montaño N° 996 - Planta Baja</p>
  <p>La Paz</p>
</div>

</div>


      </div>
			 <!-- 🔹 Características -->
             <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            <h4 class="text-xl font-semibold text-blue-800 mb-4">Características</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div
                v-for="feature in listing.all_features.filter(f =>
                    listing.features.some(selected => selected.id === f.id)
                )"
                :key="feature.id"
                class="flex items-center space-x-2"
                >
                <span class="text-green-500 text-lg">✅</span>
                <p class="text-gray-700">{{ feature.name }}</p>
                </div>
            </div>
            </div>

    </div>

    <!-- Mensaje de carga -->
    <div v-else class="text-center text-gray-500 text-sm mt-6">Cargando detalles...</div>
  </div>
</AppLayout>
</template>

<script setup>
//import { useFetch, useRoute } from "#app";
import defaultImage from '@/assets/img/default.gif';
import { useLeafletMap } from '@/Composables/useLeafletMap';
import AppLayout from "@/Layouts/AppLayout.vue";
import { useCurrencyStore } from '@/Stores/currencyStore';
import { Link } from '@inertiajs/vue3';
import "leaflet/dist/leaflet.css";
import { onMounted, ref } from "vue";
import "vue3-carousel/dist/carousel.css";
import { watchEffect } from 'vue';
import "swiper/css";
import "swiper/css/free-mode";
import "swiper/css/navigation";
import "swiper/css/thumbs";
import { Swiper, SwiperSlide } from "swiper/vue";


const currencyStore = useCurrencyStore();
watchEffect(() => {
  const currency = currencyStore.selectedCurrency;
  console.log("💱 Moneda actual detectada (reactiva):", currency);
});



import { FreeMode, Navigation, Pagination, Thumbs } from "swiper/modules";

const modules = [FreeMode, Navigation, Thumbs, Pagination];
// Recibir datos desde Laravel
const props = defineProps({
  listing: Object
});

console.log("datos del listings ", props.listing);

const mapElement = ref(null);
const { initializeMap, addMarker } = useLeafletMap();
//const route = useRoute();
//const mlsId = route.query.mls_id;
//const baseUrl = "http://localhost:8000";
// Almacenar imágenes principales, miniaturas y extras
const mainImages = ref([]);
const miniThumbnails = ref([]);
const extraImages = ref([]);
const thumbsSwiper = ref(null);
const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper;
};
//const modules = [Navigation, Thumbs];


// Verificar si hay imágenes, si no, usar una por defecto
if (props.listing?.multimedias?.length > 0) {
  mainImages.value = props.listing.multimedias  || [];
  miniThumbnails.value = props.listing.multimedias  || [];
  extraImages.value = props.listing.multimedias.slice(3);
} else {
  mainImages.value = [{ link: defaultImage }];
}

const getFullImageUrl = (relativeUrl) => {
  return relativeUrl || ""; // Si es null o undefined, retorna un string vacío
};
onMounted(() => {
    const currency = currencyStore.selectedCurrency;
    console.log("💱 Moneda actual detectada (reactiva):", currency);
});


// 📌 Formatear fecha
const formatDate = (date) => {
  return new Date(date).toLocaleDateString("es-BO", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });
};
function selectCorrectAmount(prices) {
  // prices es el objeto con { amount, price_in_dollars, price_in_bolivianos, etc. }
  // currencyStore.selectedCurrency contiene { symbol: 'Bs' } o { symbol: '$' }
  const symbol = currencyStore.selectedCurrency?.symbol;

  if (symbol === 'Bs' || symbol === 'BOB') {
    return prices.price_in_bolivianos ?? prices.amount;
  }
  if (symbol === '$' || symbol === 'USD') {
    return prices.price_in_dollars ?? prices.amount;
  }
  // Si no coincide, devolvemos .amount por defecto
  return prices.amount;
}

function getCurrencyCode() {
  const symbol = currencyStore.selectedCurrency?.symbol;
  if (symbol === 'Bs' || symbol === 'BOB') return 'BOB';
  if (symbol === '$' || symbol === 'USD') return 'USD';
  return 'BOB'; // por defecto Bolivianos
}

// Igual que antes
function formatPrice(value, currencyCode = 'BOB') {
  return new Intl.NumberFormat("es-BO", {
    style: "currency",
    currency: currencyCode,
  }).format(value);
}


onMounted(() => {
      if (mapElement.value) {
        initializeMap(mapElement.value, {
          center: [props.listing.location.latitude, props.listing.location.longitude],
          zoom: 13,
        });
        addMarker(props.listing.location.latitude, props.listing.location.longitude);
      }
    });
</script>

<style scoped>
/* Estilos adicionales si es necesario */
.carousel__prev,
.carousel__next {
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  padding: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: background-color 0.3s ease;
}

.carousel__prev:hover,
.carousel__next:hover {
  background-color: rgba(255, 255, 255, 1);
}
</style>
