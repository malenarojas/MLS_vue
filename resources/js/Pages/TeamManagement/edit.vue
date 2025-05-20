<template>
    <AppLayout title="Editar Equipo">
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
        <Toast />

        <!-- 🧠 Encabezado elegante con ícono y nombre del equipo -->
        <!-- 🧠 Encabezado en una sola fila -->
        <div class="flex items-center gap-3 mb-6">
        <i class="pi pi-users text-blue-700 text-3xl"></i>

        <h1 class="text-2xl font-bold text-blue-800 leading-tight flex items-center gap-2">
            Editar Equipo
            <span class="text-base font-semibold text-blue-600">({{ form.name || 'Sin nombre' }})</span>
        </h1>
        </div>


        <!-- Formulario -->
        <form @submit.prevent="updateOffice" class="bg-white shadow-xl rounded-lg p-4">
          <TabView v-model:activeIndex="activeTab" scrollable class="mb-4">
            <TabPanel v-for="tab in tabs" :key="tab.name">
              <template #header>
                <div class="flex items-center gap-1">
                  <i :class="`pi ${tab.icon}`"></i>
                  <span>{{ tab.label }}</span>
                </div>
              </template>
              <div class="p-4 bg-gray-50 rounded-lg">
                <component :is="tab.component" :form="form" @updateSocialNetworks="handleSocialNetworksUpdate" :agents="props.agents":regions="options?.regions || []" />
              </div>
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
            <Link href="/teammanagement" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow">
              Atrás
            </Link>
          </div>
        </form>
      </div>
    </AppLayout>
  </template>


  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
import TeamPersonalTab from "@/Pages/TeamManagement/Tabs/TeamPersonal.vue";
import TeamUserTab from "@/Pages/TeamManagement/Tabs/TeamUsers.vue";
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import TabPanel from 'primevue/tabpanel';
import TabView from 'primevue/tabview';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';

  const { props } = usePage();
  const team = ref(props.team);
  const toast = useToast();
  console.log("datos del team traido ", props.team);
  console.log("datos de los agentes de esas oficcionas ", props.agents);

  //const office = ref(props.office || {});
  const form = useForm({
  name: props.team.name,
  phone_number: props.team.phone_number,
  email: props.team.email,
  logo_url: props.team.logo_url || '',
  logo_file:  '',
  image_file: '',
  image_url: props.team.image_url|| '',
  members: props.team.members,
  shortlink: props.team.shortlink,
  leader_id: props.team.leader_id,
  enable_chat: props.team.enable_chat,
  whatsapp_number:props.team.whatsapp_number,
  spanish_description: props.team.spanish_description,
  english_description: props.team.english_description,
  portuguese_description: props.team.portuguese_description,
  motto: props.team.motto,
  description: props.team.description,
  office_id: props.team.office_id,
  member_count: props.team.member_count,
  is_active: props.team.is_active,
  leader: props.team.leader,
  socialNetworks: Array.isArray(props.team.social_networks) ? props.team.social_networks  : [],
});
const handleSocialNetworksUpdate = (socialNetworks) => {
			console.log("Redes sociales recibidas desde el hijo:", socialNetworks);
			// Actualizar la propiedad socialNetworks en agentDataupdate
			form.socialNetworks = socialNetworks.map((network) => ({
				id: network.id,
				name: network.name,
				state: network.state,
				url: network.url,
			}));

			console.log("Redes sociales actualizadas en el edit:",form.socialNetworks);
		};

		/*const updateImage = (payload) => {
			console.log("Imagen recibida desde el hijo:", payload);
			props.form.image = payload.image_data;
			props.form.image = payload.image_name;

			// Verificar que la imagen se está asignando correctamente
			console.log(" Nueva imagen asignada:", props.form.image);
		};*/


  const activeTab = ref(0);

  const tabs = ref([
    {
      name: "TeamPersonalTab",
      label: "Team Personal",
      icon: "pi-home",
      component: TeamPersonalTab,
    },
    {
      name: "TeamUserTab",
      label: "Team Users",
      icon: "pi-users",
      component: TeamUserTab,
    },
  ]);

  const updateOffice = () => {
  console.log("📤 Enviando datos para actualizar:", form.data());
  const data = new FormData();
   // 📸 Agregar archivos si están presentes
   if (form.logo_file) {
    data.append('logo', form.logo_file);
  }

  if (form.image_file) {
    data.append('image', form.image_file);
  }


  form.post(route('teammanagement.update', props.team.id), {
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
  </script>
