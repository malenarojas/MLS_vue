<template>
<AppLayout title="Create Agents">
	<Toast />
  <div class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <div class="flex items-center gap-x-4 mb-6">
    <Link href="/agents" class="bg-blue-500 text-white text-xs px-3 py-1 rounded-md hover:bg-blue-600 transition flex items-center">
    ⬅ Volver atrás
   </Link>


    <h1 class="text-2xl font-bold text-blue-800">Crear Nuevo Agente</h1>
	</div>

    <form @submit.prevent="saveAgent" class="bg-white shadow-xl rounded-lg p-4">
      <div class="mb-4 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
          <li
            v-for="tab in tabs"
            :key="tab.value"
            class="me-2"
            role="presentation"
          >
            <button
              :class="{
                'inline-block p-4 border-b-2 rounded-t-lg': true,
                'text-blue-600 border-blue-600': activeTab === tab.value,
                'text-gray-500 hover:text-gray-600 hover:border-gray-300': activeTab !== tab.value,
              }"
              @click="activeTab = tab.value"
              type="button"
              role="tab"
            >
              <div class="flex items-center gap-1">
                <i :class="`pi ${tab.icon}`"></i>
                <span>{{ tab.label }}</span>
              </div>
            </button>
          </li>
        </ul>
      </div>

      <div>
        <div
          v-for="tab in tabs"
          :key="tab.value"
          :class="{
            'p-4 rounded-lg bg-gray-50': true,
            'hidden': activeTab !== tab.value,
          }"
          role="tabpanel"
        >
          <component
            :is="tab.component"
            :options="options"
            :user="user"
            :role = "role"
            :agentData="agentData"
            :validationErrors="validationErrors"
            @update-image="updateImage"
            @updateAreaSpecialitiesUser="updateAreaSpecialitiesUser"
            @updateAchievements="updateAchievements"
          />
        </div>
      </div>
      <div class="relative mt-4 flex justify-end">

			<button
				ref="saveButton"
				@click="saveAgent"
				:disabled="isPending"
				class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
			>
				{{ isPending ? 'Guardando...' : 'Guardar Agente' }}
			</button>


		  </div>

    </form>
  </div>
</AppLayout>
</template>
<script setup>
import { useCreateAgent } from '@/Composables/Agents/userCreateAgent'; // Ajusta la ruta
import AppLayout from "@/Layouts/AppLayout.vue";
import MarketingTab from "@/Pages/Agents//Tabs/MarketingTab.vue";
import AchievementTab from "@/Pages/Agents/Tabs/AchievementTab.vue";
import ConfigurationTab from "@/Pages/Agents/Tabs/ConfigurationTab.vue";
import PersonalTab from "@/Pages/Agents/Tabs/PersonalTab.vue";
import PhotoTab from "@/Pages/Agents/Tabs/PhotoTab.vue";
import SpecializationTab from "@/Pages/Agents/Tabs/SpecializationTab.vue";

import { Link, router, usePage } from '@inertiajs/vue3';
import axios from "axios";
import { useToast } from 'primevue/usetoast';
import { onMounted, reactive, ref, watch } from "vue";
import * as yup from "yup";
import DocumentationTab from './Tabs/DocumentationTab.vue';
const toast = useToast(); // Inicializa el Toast
const { props } = usePage();

const options = ref(props.formData);
const user = ref(props.user|| {});
const role = ref(props.role|| {});
console.log("estos son los datos del usuario",user.value);

const saveButton = ref(null);
const isSubmitting = ref(false);

const agentDataSchema = yup.object({
    region_id: yup
      .number()
      .transform(value => (isNaN(value) ? undefined : value))
      .typeError("La región es requerida")
      .min(1, "La región es requerida")
      .required("La región es requerida"),
    office_id: yup
      .number()
      .transform(value => (isNaN(value) ? undefined : value))
      .typeError("La oficina es requerida")
      .min(1, "La oficina es requerida")
      .required("La oficina es requerida"),
    agent_status_id: yup
      .number()
      .transform(value => (isNaN(value) ? undefined : value))
      .typeError("El estado del agente es requerido")
      .required("El estado del agente es requerido"),
    role_id: yup
      .number()
      .transform(value => (isNaN(value) ? undefined : value))
      .typeError("El rol es requerido")
      .required("El rol es requerido"),
  user: yup.object({
    phone_number: yup.string().required("el numero de telefono es requerido"),
    birthdate: yup.date().required("la fecha de nacimiento es requerido"),
    username: yup.string().required("el nombre de usuario es requerido"),
    first_name: yup.string().required("El nombre es requerido"),
    email: yup.string().email("Debe ser un correo válido").required("El correo es requerido"),
    gender: yup.string().required("el genero es requerido"),
    ci: yup.string().required("El CI es requerido"),
    password: yup.string().required("La contraseña es requerida"),
  }),
});
const showError = ref(false);

const { mutate, isPending, isError, isSuccess, error } = useCreateAgent();

const validationErrors = ref({});

// Configuración de tabs
const tabs = ref([
  { name: "personal", label: "Personal", value: "0", icon: "pi-user", component: PersonalTab },
  { name: "marketing", label: "Marketing", value: "1", icon: "pi-chart-line", component: MarketingTab },
  { name: "specialization", label: "Especialización", value: "2", icon: "pi-briefcase", component: SpecializationTab },
  { name: "photo", label: "Foto", value: "4", icon: "pi-camera", component: PhotoTab },
  { name: "documentation", label: "Documentation", value: "5", icon: "pi-file", component: DocumentationTab },
  //{ name: "Configuracion", label: "Configuracion", value: "6", icon: "pi-cog", component: ConfigurationTab },
]);
if (role.value?.toLowerCase() === 'administrador') {
      tabs.value.push({
        name: "ConfigurationTab",
        label: "Configuración",
        value: "6",
        icon: "pi-cog",
        component: ConfigurationTab,
      });
      }


// Tab activo
const activeTab = ref("0");

// Datos iniciales del formulario
const agentData = reactive({
  agent_internal_id: "",
  office_id: 0,
  region_id: 0,
  date_joined: new Date(),
  date_termination: new Date(),
  agent_status_id: 0,
  studies: "",
  additional_education: "",
  previous_occupation: "",
  license_type: "",
  license_department: "",
  year_obtained_license: "",
  expiration_date_license: new Date(),
  license_number: "",
  address: "",
  landline_phone: "",
  commission_percentage: 0,
  marketing_slogan: "",
  image_name: "",
  image_agent: "",
  website_description: "",
  countries_interested: "",
  meta_tag_description: "",
  meta_tag_keywords: "",
  bullet_point_one: "",
  bullet_point_two: "",
  bullet_point_three: "",
  nro_internacional_remax: 0,
  id_business_agent: 0,
  url_alterno: null,
  role_id: null,
  user: {
    id: 0,
    first_name: "",
    middle_name: "",
    last_name: "",
    name_to_show: "",
    ci: "",
    gender: "",
    phone_number: "",
    email: "",
    url: "",
    birthdate: null,
    remax_start_date: new Date(),
    password: "",
    username: "",
    user_type_id: null,
    remax_title_id: null,
    remax_title_to_show_id: null,
    team_status_id: null,
    customer_preference_id: null,
    state_url: 0,

  },
  social_networks: [
    { name: "facebook", url: "", state: 0 },
    { name: "twitter", url: "", state: 0 },
    { name: "youtube", url: "", state: 0 },
    { name: "linkedin", url: "", state: 0 },
    { name: "instagram", url: "", state: 0 },
    { name: "whatsapp", url: "", state: 0 },
  ],
  area_specialities_user: [],
  achievement_user: [],
  image_url: "",
  documents: [],
  permissions: [],
});

const validateAgentData = async () => {
  try {
    validationErrors.value = {};

    await agentDataSchema.validate(agentData, { abortEarly: false });
    return true;
  } catch (err) {
    if (err instanceof yup.ValidationError) {
      err.inner.forEach((validationError) => {
        if (validationError.path) {
          validationErrors.value[validationError.path] = validationError.message;

        }
      });
    }
    return false;
  }
};

// Función para validar y llenar `validationErrors`
const validateForm = async () => {
  try {
    await agentDataSchema.validate(props.agentData, { abortEarly: false });
    validationErrors.value = {}; // Sin errores
  } catch (err) {
    if (err.inner) {
      validationErrors.value = err.inner.reduce((acc, error) => {
        acc[error.path] = error.message;
        return acc;
      }, {});
    }
  }
};
onMounted(() => {
  //validateForm(); // Valida y llena `validationErrors`
  validateAgentData();

  const formatDate = (date) => {
    if (date instanceof Date) {
      return date.toISOString().split('T')[0]; // 'YYYY-MM-DD'
    }
    return date;
  };

  agentData.date_joined = formatDate(agentData.date_joined);
  agentData.date_termination = formatDate(agentData.date_termination);
  agentData.expiration_date_license = formatDate(agentData.expiration_date_license);
  agentData.user.birthdate = formatDate(agentData.user.birthdate);
  agentData.user.remax_start_date = formatDate(agentData.user.remax_start_date)
});

watch(
  () => agentData.value,
  () => {
    validateData();
  },
  { deep: true }
);


const saveAgent = async () => {
  if (isSubmitting.value) return; // Evita múltiples envíos
  isSubmitting.value = true; // Bloquea la función para evitar doble envío

  const isValid = await validateAgentData();
  if (!isValid) {
    const errorMessages = Object.values(validationErrors.value)
    .map((message) => {
      if (typeof message === 'string') return `• ${message}`;
      if (typeof message === 'object') return Object.values(message).map(m => `• ${m}`).join("\n");
      return `• ${String(message)}`;
    })
    .join("\n");


    toast.add({
      severity: "error",
      summary: "Errores en el formulario",
      detail: errorMessages,
      life: 5000,
    });

    isSubmitting.value = false;
    return;
  }

  try {
    console.log("Datos enviados antes de conversión a FormData:", agentData);
    const formData = new FormData();
    for (const key in agentData) {
      if (agentData[key] !== null && agentData[key] !== undefined) {
        if (typeof agentData[key] === "object" && !(agentData[key] instanceof File)) {
          continue;
        } else {
          formData.append(key, agentData[key]);
        }
      }
    }
     const formatDate = (date) => {
    if (!date) return "";
    const d = new Date(date);
    if (isNaN(d)) return "";
    return d.toISOString().split("T")[0];
    };

    if (agentData.user && typeof agentData.user === "object") {
    for (const key in agentData.user) {
        if (agentData.user[key] !== null && agentData.user[key] !== undefined) {
        if (["birthdate", "remax_start_date"].includes(key)) {
            formData.append(`user[${key}]`, formatDate(agentData.user[key]));
        } else {
            formData.append(`user[${key}]`, agentData.user[key]);
        }
        }
    }
    }


    if (Array.isArray(agentData.social_networks)) {
      agentData.social_networks.forEach((network, index) => {
        formData.append(`social_networks[${index}][name]`, network.name);
        formData.append(`social_networks[${index}][url]`, network.url);
        formData.append(`social_networks[${index}][state]`, network.state);
      });
    }

        if (Array.isArray(agentData.area_specialities_user)) {
    agentData.area_specialities_user.forEach((item, index) => {
        formData.append(`area_specialities_user[${index}][area_speciality_id]`, item.area_speciality_id);
        formData.append(`area_specialities_user[${index}][user_id]`, item.user_id ?? '');
    });
    }

    // 👉 Enviar permisos seleccionados como array de strings
    if (Array.isArray(agentData.permissions)) {
    agentData.permissions.forEach((permission, index) => {
        formData.append(`permissions[${index}]`, permission);
    });
    }


    if (Array.isArray(agentData.achievement_user)) {
    agentData.achievement_user.forEach((item, index) => {
        formData.append(`achievement_user[${index}][id]`, item.id ?? '');
        formData.append(`achievement_user[${index}][achievement_date]`, item.achievement_date ?? '');
        formData.append(`achievement_user[${index}][enable_achievement]`, item.enable_achievement ?? 0);
        formData.append(`achievement_user[${index}][user_id]`, item.user_id ?? '');
    });
    }


    if (agentData.documents && agentData.documents.length > 0) {
        agentData.documents.forEach((doc, index) => {
            formData.append(`archivo[]`, doc.file); // ✅ Laravel espera esto
            formData.append(`descriptions[]`, doc.description || "");
            formData.append(`types[]`, doc.type);
        });
    }


    for (let pair of formData.entries()) {
      console.log(`📝 FormData -> ${pair[0]}:`, pair[1]);
    }
    console.log("asi esta maandando from data ", formData);
    const response = await axios.post(route("agents.store"), formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    console.log("Respuesta del servidor:", response.data);

    toast.add({
      severity: "success",
      summary: "¡Éxito!",
      detail: "El agente fue creado con éxito.",
      life: 3000,
    });
    setTimeout(() => {
    router.visit(route("agents.index")); // 👈 Redirige usando Inertia
    }, 1000);

  } catch (error) {
    console.error("Error al guardar el agente:", error);

    let errorMessage = "Hubo un problema al crear el agente.";

    if (error.response && error.response.data) {
      const errors = error.response.data.errors;
      if (errors) {
        errorMessage = Object.values(errors).flat().join("\n"); // Mostrar errores de Laravel
      }
    }

    // Mostrar mensaje de error
    toast.add({
      severity: "error",
      summary: "Error",
      detail: errorMessage,
      life: 5000,
    });
  } finally {
    isSubmitting.value = false;
  }
};



// Actualizar imagen
const updateImage = (payload) => {
  agentData.image_agent = payload.image_data;
  agentData.image_name = payload.image_name;
};

// Actualizar especialidades
const updateAreaSpecialitiesUser = (areaSpecialities) => {
  agentData.area_specialities_user = areaSpecialities;
};

// Actualizar logros
const updateAchievements = (achievements) => {
  agentData.achievement_user = achievements;
  console.log("Logros actualizados:", agentData.achievement_user);
};
</script>

<style scoped>
/* Animación para el mensaje flotante */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.actions {
  margin-top: 1rem;
  text-align: right;
}
.actions button {
  padding: 0.5rem 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

/* Estilo para permitir scroll horizontal */
.tablist {
  overflow-x: auto; /* Habilita el scroll horizontal */
  display: flex; /* Asegura que los tabs estén en una fila */
  white-space: nowrap; /* Evita el salto de línea */
  -webkit-overflow-scrolling: touch; /* Mejora el scroll en dispositivos móviles */
}

.tablist::-webkit-scrollbar {
  height: 8px; /* Estilo del scrollbar horizontal */
}

.tablist::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}

.tablist::-webkit-scrollbar-track {
  background: transparent;
}
@media (max-width: 768px) {
  .tablist {
    gap: 0.5rem; /* Ajusta el espacio entre tabs en pantallas pequeñas */
  }
}
</style>
