<template>
    <div class=" w-full grid grid-cols-5 gap-6">
      <!-- Grupo: Información Básica -->
      <div class="col-span-5 my-0">
        <div class="section-title">Información Básica</div>
        <div class="grid grid-cols-5 gap-4">
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Nombre Oficina</label>
            <InputText v-model="form.name" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">ID Oficina Internacional</label>
            <InputText v-model="form.international_code" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">ID Oficina Empresarial</label>
            <InputText v-model="form.office_id" class="w-full" />
          </div>

          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Office Start Date</label>
            <DatePicker v-model="form.office_start_date" class="w-full" showIcon />
          </div>
          <div class="flex items-center gap-2 pt-6">
            <Checkbox v-model="activeCheckbox" :binary="true" />
            <label class="text-gray-700">Active</label>
          </div>


           <!-- Horario de Atención (ocupa 2 columnas) -->
            <div class="col-span-4">
                <label class="block text-gray-700 text-sm font-medium mb-1">Horario de Atención</label>
                <div class="grid grid-cols-2 gap-2">
                <InputText
                    v-model="form.schedule_weekdays"
                    class="w-full"
                    placeholder="Lunes a Viernes (Ej: 08:30 - 18:00)"
                />
                <InputText
                    v-model="form.schedule_saturday"
                    class="w-full"
                    placeholder="Sábado (Ej: 09:00 - 13:00)"
                />
                </div>

            </div>
            <div class="flex items-center gap-2 pt-6">
            <Checkbox v-model="activeCheckboxIsExternal" :binary="true" input-id="is_external" />
            <label for="is_external" class="text-gray-700">Externa</label>
            </div>
        </div>

      </div>

      <!-- Grupo: Contacto -->
      <div class="col-span-5 my-0">
        <div class="section-title">Contacto</div>
        <div class="grid grid-cols-5 gap-4">
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Teléfono</label>
            <InputText v-model="form.phone" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Correo Electrónico</label>
            <InputText v-model="form.email" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Celular</label>
            <InputText v-model="form.cell_phone" class="w-full" />
          </div>
          <div class="flex items-center gap-2 pt-6">
            <Checkbox v-model="activeCheckboxshowwasap" :binary="true" />
            <label class="text-gray-700">Mostrar WhatsApp en el sitio web</label>
          </div>
          <div class="flex items-center gap-2 pt-6">
            <Checkbox v-model="activeCheckboxshowoffice" :binary="true" />
            <label class="text-gray-700">Ocultar Office del sitio web público</label>
          </div>
        </div>
      </div>

      <!-- Grupo: Dirección -->
      <div class="col-span-5 my-0">
        <div class="section-title">Dirección</div>
        <div class="grid grid-cols-5 gap-4">
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Región</label>
            <Dropdown
                id="region"
                v-model="form.state_id"
                :options="props.state"
                optionLabel="name"
                optionValue="id"
                placeholder="Seleccione una región"
                @change="updateLocation"
                filter
                class="w-full mt-1 text-sm"
                />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Provincia</label>
            <Dropdown
            id="provincia"
            v-model="form.province_id"
            :options="props.provinces"
            optionLabel="name"
            optionValue="id"
            placeholder="Seleccione una provincia"
            @change="updateLocation"
            filter
            class="w-full mt-1 text-sm"
            />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Ciudad</label>
            <Dropdown
                id="ciudad"
                v-model="form.city_id"
                :options="props.cities"
                optionLabel="name"
                optionValue="id"
                placeholder="Seleccione una ciudad"
                @change="updateLocation"
                filter
                class="w-full mt-1 text-sm"
                />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Zona</label>
            <Dropdown
                id="zona"
                v-model="form.zone_id"
                :options="props.zones"
                optionLabel="name"
                optionValue="id"
                placeholder="Seleccione una zona"
                @change="updateLocation"
                filter
                class="w-full mt-1 text-sm"
                />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Número</label>
            <InputText v-model="form.number" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Dirección 1</label>
            <InputText v-model="form.address" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Dirección 2</label>
            <InputText v-model="form.address2" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Código Postal</label>
            <InputText v-model="form.postal_code" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Piso</label>
            <InputText v-model="form.floor" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Depto / Unidad</label>
            <InputText v-model="form.unit" class="w-full" />
          </div>
        </div>

        <!-- Mapa y Coordenadas -->
        <div class="col-span-5 mt-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-1">Latitud</label>
              <InputText v-model="form.latitude" class="w-full" />
            </div>
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-1">Longitud</label>
              <InputText v-model="form.longitude" class="w-full" />
            </div>
          </div>
          <div ref="mapElement" class="h-64 w-full mt-4 border border-gray-300 rounded  z-50"></div>
        </div>
      </div>

      <!-- Grupo: Licencia -->
      <div class="col-span-5 my-0">
        <div class="section-title">Detalles de la Licencia</div>
        <div class="grid grid-cols-4 gap-4">
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">
              Año que obtuvo Licencia
            </label>
            <DatePicker v-model="form.first_year_licensed" class="w-full" showIcon/>
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Fecha de Caducidad</label>
            <DatePicker v-model="form.expiration_date" class="w-full" showIcon/>
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Número de Licencia</label>
            <InputText v-model="form.license_number" class="w-full" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Departamento</label>
            <InputText v-model="form.license_department" class="w-full" />
          </div>
        </div>
      </div>
    </div>
  </template>

<script setup>
import { router } from '@inertiajs/vue3';
import { computed, defineProps, onMounted, ref, watch } from 'vue';

import { useLeafletMapOffice } from "@/Composables/useLeafletMapOffice";
import Checkbox from 'primevue/checkbox';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';

const { onMarkerEvent , addMarker, initializeMap, moveMarker} = useLeafletMapOffice();
const props = defineProps({
  form: Object,
  regions: Array,
  state: Array,
  provinces: Array,
  cities: Array,
  zones: Array,
});
console.log("datos de la state ",props.state);
const updateLocation = () => {
    console.log(
        `location actulizada`,props.provinces, props.cities, props.zones
    );
    router.visit(route("offices.edit"), {
        method: "get",
        data: {
            id: props.form.id,
            state_id:  props.form.state_id,
            province_id: props.form.province_id,
            city_id: props.form.city_id,
        },
        preserveState: true,
        preserveScroll: true,
        replace: true,
        preserveUrl: true,
    });
};


const activeCheckbox = computed({
  get: () => !!props.form.active_office,
  set: (value) => {
    props.form.active_office = value ? 1 : 0;
  },
});
const activeCheckboxshowwasap = computed({
  get: () => !!props.form.show_whatsapp,
  set: (value) => {
    props.form.show_whatsapp = value ? 1 : 0;
  },
});
const activeCheckboxshowoffice = computed({
  get: () => !!props.form.hide_office_from_web,
  set: (value) => {
    props.form.hide_office_from_web = value ? 1 : 0;
  },
});
const activeCheckboxIsExternal = computed({
  get: () => !!props.form.is_external,
  set: (value) => {
    props.form.is_external = value ? 1 : 0;
  },
});


// Comentario en español: Manejamos el mapa
const mapElement = ref(null);

// Desestructura las funciones que necesitas
/*onMarkerEvent('dragend', (event) => {
  const { lat, lng } = event.target.getLatLng();
  props.form.latitude = lat.toFixed(8);
  props.form.longitude = lng.toFixed(8);
});*/

onMounted(() => {
  const latitude = parseFloat(props.form.latitude) || -16.2902;
  const longitude = parseFloat(props.form.longitude) || -63.5887;

  initializeMap(mapElement.value, {
    center: [latitude, longitude],
    zoom: 15
  });

  addMarker(latitude, longitude);

  // ⬇️ Escuchar el evento DESPUÉS de crear el marcador
  onMarkerEvent('dragend', (event) => {
    const { lat, lng } = event.target.getLatLng();
    console.log("📍 Coordenadas arrastradas:", lat, lng);
    props.form.latitude = lat.toFixed(8);
    props.form.longitude = lng.toFixed(8);
  });
});
onMounted(() => {
  const missingProvince = props.form.province_id && !props.provinces.find(p => p.id === form.province_id);
  const missingCity = props.form.city_id && !props.cities.find(c => c.id === form.city_id);
  const missingZone = props.form.zone_id && !props.zones.find(z => z.id === form.zone_id);

  if (missingProvince || missingCity || missingZone) {
    console.log("⛔️ Datos faltantes en provincias, ciudades o zonas. Forzando recarga...");
    updateLocation();
  }
});



// Comentario en español: Observar si lat o lng cambian para mover el marcador
watch(() => props.form.latitude, (newVal) => {
  const newLat = parseFloat(newVal);
  const newLng = parseFloat(props.form.longitude);

  if (!isNaN(newLat) && !isNaN(newLng)) {
    moveMarker(newLat, newLng);
  }
});

watch(() => props.form.longitude, (newVal) => {
  const newLat = parseFloat(props.form.latitude);
  const newLng = parseFloat(newVal);

  if (!isNaN(newLat) && !isNaN(newLng)) {
    moveMarker(newLat, newLng);
  }
});


</script>

  <style scoped>
  .section-title {
    /* Mismo estilo que agentes: línea azul al ancho, etc. */
    font-size: 1.1rem;
    font-weight: bold;
    color: #080808;
    margin-bottom: 8px; /* Espacio justo debajo del título */
    border-bottom: 2px solid #007bff; /* Línea inferior azul */
    padding-bottom: 4px;
  }
  </style>
