<template>
    <AppLayout title="Editar Oficina">
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
        <Toast />
        <OfficeHeader :office="props.office"  @update-office-logo="updateImage"  />

        <form @submit.prevent="updateOffice" class="bg-white shadow-xl rounded-lg p-4">
          <TabView v-model:activeIndex="activeTab" scrollable class="mb-4">
            <TabPanel v-for="tab in tabs" :key="tab.name">
              <template #header>
                <div class="flex items-center gap-1">
                  <i :class="`pi ${tab.icon}`"></i>
                  <span>{{ tab.label }}</span>
                </div>
              </template>
                <component   v-if="tab.component"
                    :is="tab.component" :form="form" :regions="options?.regions || []"
                    :state ="props.formData.state"
                    @updateSocialNetworksoffices="handleSocialNetworksUpdateoffices"
                    @updateOfficeAchievements="handleAchievementsUpdateoffices"
                    :achievement ="props.formData.achievement"
                    :provinces  = "props.provinces"
                    :cities ="props.cities"
                    :zones = "props.zones"
                    class="w-full"/>
            </TabPanel>
          </TabView>

          <div class="flex justify-end gap-4 mt-4">
            <Button
              label="Guardar"
              icon="pi pi-save"
              class="bg-blue-500 hover:bg-blue-600 text-white"
              :loading="form.processing"
              type="submit"
            />
            <Link href="/offices" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow">
              Atrás
            </Link>
          </div>
        </form>
      </div>
    </AppLayout>
  </template>

  <script setup>
  import OfficeHeader from '@/Components/Offices/OfficeHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import MarketingOfficeTab from "@/Pages/Offices/Tabs/MarketingOfficeTab.vue";
import OfficePersonalTab from "@/Pages/Offices/Tabs/PersonalOfficeTab.vue";
import UserOfficeTab from "@/Pages/Offices/Tabs/UserOfficeTab.vue";
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import TabPanel from 'primevue/tabpanel';
import TabView from 'primevue/tabview';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { computed, ref, watch } from 'vue';
import AchievementOfficeTab from './Tabs/AchievementOfficeTab.vue';
import ConfigurationOfficeTab from './Tabs/ConfigurationOfficeTab.vue';
import HistorialOfficeTab from './Tabs/HistorialOfficeTab.vue';

  //const { props } = usePage();
  //const options = ref(props.formData);
  //const office = ref(props.office || {});
  const toast = useToast();
  const props = defineProps({
  office: Object,
  formData: Object,
  provinces: Array,
  cities: Array,
  zones: Array,
  achievement: Array,
});
  console.log("datos de la officina ",props.office);
  console.log("datos de la state ",props.formData.state);
  console.log("datos de la state ",props.formData.achievement);
  console.log("📦 props.provinces desde edit:", props.provinces);
  console.log("datos de la city ",props.cities);
  console.log(
        `location actulizada`,props.provinces, props.cities, props.zones
  );
  watch(() => props.provinces, (nuevasProvincias) => {
  console.log("🟢 Provincias actualizadas desde props:", nuevasProvincias);
});
const hasOffice = computed(() => !!props.office && !!props.office.id);



  /*const form = useForm({
    name: office.value.name || '',
    code: office.value.code || '',
    address: office.value.address || '',
    city: office.value.city || '',
    zone: office.value.zone || '',
    lat: office.value.lat || '',
    lng: office.value.lng || '',
    active: office.value.active_office || false,
    is_satellite_office: office.value.is_satellite_office || false,
    is_commercial: office.value.is_commercial || false,
    is_collection: office.value.is_collection || false,
    region_id: office.value.region_id || null,
  });*/
  const form = useForm({
  // Información Básica
    id: props.office.id || null,
    name: props.office.name || '',
    code: props.office.code || '',
    office_id: props.office.office_id || '',
    office_intl_id: props.office.office_intl_id || '',
    active_office: props.office.active_office ?? 0,
    is_satellite_office: props.office.is_satellite_office ?? 0,
    is_commercial: props.office.is_commercial ?? 0,
    is_collection: props.office.is_collection ?? 0,
    hide_office_from_web: props.office.hide_office_from_web ?? 0,
    show_whatsapp: props.office.show_whatsapp ?? 0,
    first_updated_to_web: props.office.first_updated_to_web || 0,
    office_start_date: props.office.office_start_date || '',
    international_code: props.office.international_code || '',
    // Contacto
    phone: props.office.phone || '',
    email: props.office.email || '',
    cell_phone: props.office.cell_phone || '',
    image_url: props.office.image_url || '',
    image: props.office.image,
    image_data: '',

    // Ubicación
    city_id: props.office.city_id || '',
    state_id: props.office.state_id || '',
    province_id: props.office.province_id || '',
    number: props.office.number || '',
    address: props.office.address || '',
    address2: props.office.address2 || '',
    postal_code: props.office.postal_code || '',
    floor: props.office.floor || '',
    latitude: props.office.latitude  || '',
    longitude: props.office.longitude|| '',

    // Ubicación
    region_id: props.office.region_id || null,
    zone_id: props.office.zone_id || '',
    region: props.office.region || '',
    province: props.office.province || '',
    city: props.office.city || '',
    unit: props.office.unit || '',
    is_external: props.office.is_external|| 0,
    schedule_weekdays : props.office.schedule_weekdays ?? '',
    schedule_saturday : props.office.schedule_saturday ?? '',


    // Licencia
    first_year_licensed: props.office.first_year_licensed || '',
    expiration_date: props.office.expiration_date || '',
    license_number: props.office.license_number || '',
    license_department: props.office.license_department || '',
    socialNetworks: Array.isArray(props.office.social_networks) ? props.office.social_networks: [],
    achievementoffices: Array.isArray(props.office.achievementoffices) ? props.office.achievementoffices: [],
    agents: props.office.agents,

      // Marketing y Web
    marketing_slogan: props.office.marketing_slogan || '',
    website_description: props.office.website_description || '',
    closure: props.office.closure || '',
    short_link: props.office.short_link || '',
    office_website: props.office.office_website || '',

    // Bullet points
    bullet_point_one: props.office.bullet_point_one || '',
    bullet_point_two: props.office.bullet_point_two || '',
    bullet_point_three: props.office.bullet_point_three || '',
    bullet_point_four: props.office.bullet_point_four || '',


    // SEO
    meta_tag_keywords: props.office.meta_tag_keywords || '',
    meta_tag_description: props.office.meta_tag_description || '',

});
const handleSocialNetworksUpdateoffices = (socialNetworks) => {
			console.log("Redes sociales recibidas desde el hijo:", socialNetworks);

			// Actualizar la propiedad socialNetworks en agentDataupdate
			form.socialNetworks = socialNetworks.map((network) => ({
				id: network.id,
				name: network.name,
				state: network.state,
				url: network.url,
			}));

			console.log("Redes sociales actualizadas en officina:", form.socialNetworks);
		};
        const handleAchievementsUpdateoffices = (achievements) => {
            console.log("Achievement recibidas desde el hijo:", achievements);

			form.achievementoffices = achievements.map(achievement => ({
				id: achievement.id,
				achievement_id: achievement.achievement_id,
				achievement_date: achievement.achievement_date,
				enable_achievement: achievement.enable_achievement,
                name_achievements: achievement.name_achievements || "Sin Nombre",
                achievement_description: achievement.achievement_description || "Sin Descripción",
                image_url: achievement.image_url || null,// aún sin fecha

			}));
            console.log("achivements actualizadas en oficionas:", form.achievementoffices);
		};


  const activeTab = ref(0);

  const tabs = ref([
  {
    name: "OfficePersonalTab",
    label: "Oficina",
    icon: "pi pi-building",            // ícono de un edificio, para la oficina
    component: OfficePersonalTab,
  },
  {
    name: "MarketingOfficeTab",
    label: "Marketing",
    icon: "pi pi-bullhorn",            // ícono de megáfono
    component: MarketingOfficeTab,
  },
  {
    name: "AchievementOfficeTab",
    label: "Logros",
    icon: "pi pi-trophy",              // ícono de trofeo
    component: AchievementOfficeTab,
  },
  {
    name: "HistorialOfficeTab",
    label: "Historial",
    icon: "pi pi-history",             // ícono de “historial”
    component: HistorialOfficeTab,
  },
  {
    name: "UserOfficeTab",
    label: "Usuarios",
    icon: "pi pi-users",               // ícono de usuarios
    component: UserOfficeTab,
  },
  {
    name: "ConfigurationOfficeTab",
    label: "Configuración",
    icon: "pi pi-cog",                 // ícono de engranaje/configuración
    component: ConfigurationOfficeTab,
  },
]);



const updateOffice = () => {
  console.log("📤 Enviando datos para actualizar:", form.data());

  form.post(route('offices.update', form.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({
        severity: 'success',
        summary: 'Actualización exitosa',
        detail: 'La oficina fue actualizada correctamente',
        life: 3000
      });
    },
    onError: () => {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Ocurrió un problema al actualizar la oficina',
        life: 3000
      });
    }
  });
};
const updateImage = (payload) => {
	console.log("Imagen recibida desde el hijo:", payload);
	form.image_data = payload.image_data;
	form.image = payload.image_name;

	// Verificar que la imagen se está asignando correctamente
	console.log(" Nueva imagen asignada:", form.image_data);
	};
/*const updateOffice = async () => {
  try {
    console.log("📤 Enviando datos para actualizar (axios):", form.data());

    const response = await axios.put(`/offices/${props.office.id}`, form.data());

    console.log("✅ Respuesta del servidor:", response.data);
    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Oficina actualizada correctamente',
      life: 3000
    });

  } catch (error) {
    console.error("❌ Error al actualizar oficina:", error);

    // Si Laravel devuelve errores de validación
    if (error.response && error.response.status === 422) {
      form.setErrors(error.response.data.errors);
    }
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Ocurrió un error al actualizar la oficina',
        life: 4000
      });
  }
};*/

  </script>
