<template>
<AppLayout title="Editar Agents">
  <div class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <Toast />
    <AgentHeader :agentDataupdate="agentDataupdate"  @update-image="updateImage" />

    <form @submit.prevent="updateAgent" class="bg-white shadow-xl rounded-lg p-4">
        <TabView v-model:activeIndex="activeTab" scrollable class="mb-4">
        <TabPanel
            v-for="tab in tabs"
            :key="tab.name"
        >
            <!-- 👇 Solo una vez el header -->
            <template #header>
            <div class="flex items-center gap-1">
                <i :class="`pi ${tab.icon}`"></i>
                <span>{{ tab.label }}</span>
            </div>
            </template>

            <!-- 👇 Contenido del tab -->
            <div class="p-4 bg-gray-50 rounded-lg">
            <component
                :is="tab.component"
                :options="options"
                :user="user"
                :role = "role"
                :logs = "logs"
                :permissions ="permissions"
                :agentDataupdate="agentDataupdate"
                :validationErrors="validationErrors"
                @update-image="updateImage"
                @updateAchievements="handleAchievementsUpdate"
                @updateAreaSpecialitiesUser="updateAreaSpecialitiesUser"
                @updateSocialNetworks="handleSocialNetworksUpdate"
            />
            </div>
        </TabPanel>
        </TabView>
      <div class="actions mt-4 flex gap-4 justify-end">
        <button
          @click="updateAgent"
          :disabled="isLoading"
          class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
        >
          {{ isLoading ? 'Guardando...' : 'Actualizar Agente' }}
        </button>
				 <!-- Botón para volver atrás -->
        <p v-if="isError" class="text-red-600 mt-1 text-sm">Error: {{ error?.message }}</p>
        <p v-if="isSuccess" class="text-green-600 mt-1 text-sm">¡Agente actualizado con éxito!</p>
		<Link href="/agents"
		class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transition-all transform hover:scale-105 text-sm"
		>
		Atrás
		</Link>
      </div>
    </form>
  </div>
  </AppLayout>
</template>

<script setup>
import AgentHeader from '@/Components/AgentHeader.vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import AchievementTabEdit from "@/Pages/Agents/TabsEdit/AchievementTabEdit.vue";
import ConfigurationTabEdit from "@/Pages/Agents/TabsEdit/ConfigurationTabEdit.vue";
import DocumentationTabEdit from "@/Pages/Agents/TabsEdit/DocumentationTabEdit.vue";
import HistorialTabEdit from "@/Pages/Agents/TabsEdit/HistorialTabEdit.vue";
import MarketingTabEdit from "@/Pages/Agents/TabsEdit/MarketingTabEdit.vue";
import PersonalTabEdit from "@/Pages/Agents/TabsEdit/PersonalTabEdit.vue";
import SkillsTabEdit from "@/Pages/Agents/TabsEdit/SkillsTabEdit.vue";
import SpecializationTabEdit from "@/Pages/Agents/TabsEdit/SpecializationTabEdit.vue";
import TabPanel from 'primevue/tabpanel';
import TabView from 'primevue/tabview';

import { Link, router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import { onMounted, reactive, ref, toRaw } from "vue";
import * as yup from "yup";
const toast = useToast();

const activeTab = ref(0);
const validationErrors = reactive({});
const { props } = usePage();

const options = ref(props.formData);
const user = ref(props.user|| {});
const role = ref(props.role|| {});
const logs = ref(props.logs|| {});
const permissions = ref(props.permissions|| {});
console.log("estos son los logs ",logs.value);

console.log("este es el rol del usuario",role.value);
console.log("estos son los datos del usuario",user.value);
console.log("estos son los datos del usuario",permissions.value);
console.log("estos son los achievement",props.formData.Achievements);

const agentDataSchema = yup.object({
  user: yup.object({
    first_name: yup.string().required("El nombre es requerido"),
    email: yup.string().email("Debe ser un correo válido").required("El correo es requerido"),
  }),
})

//const agentId = route.query.agent_id;


const agentDataupdate = reactive({
		id: "",
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
		website_descripction: "",
		countries_interested: "",
		meta_tag_description: "",
		meta_tag_keywords: "",
		bullet_point_one: "",
		bullet_point_two: "",
		bullet_point_three: "",
		rejectionReason:"",
    url_alterno: "",
		user: {
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
		  user_type_id: 0,
		  remax_title_id: 0,
		  remax_title_to_show_id: 0,
		  team_status_id: 0,
		  customer_preference_id: 0,
          roles: 0,
          state_url: 0,
      ///permissions: [],
		},
		socialNetworks:[],
		areaSpecialityUser: [],
		achievementuser: [],
		image_url:"",
		documents: [],
        permissions: [],
	  });
   // Manejar la actualización de las redes sociales
		const handleSocialNetworksUpdate = (socialNetworks) => {
			console.log("Redes sociales recibidas desde el hijo:", socialNetworks);

			// Actualizar la propiedad socialNetworks en agentDataupdate
			agentDataupdate.socialNetworks = socialNetworks.map((network) => ({
				id: network.id,
				name: network.name,
				state: network.state,
				url: network.url,
			}));

			console.log("Redes sociales actualizadas en agentDataupdate:", agentDataupdate.socialNetworks);
		};

		const updateImage = (payload) => {
			console.log("Imagen recibida desde el hijo:", payload);
			agentDataupdate.image_agent = payload.image_data;
			agentDataupdate.image_name = payload.image_name;

			// Verificar que la imagen se está asignando correctamente
			console.log(" Nueva imagen asignada:", agentDataupdate.image_agent);
		};

		const handleAchievementsUpdate = (achievements) => {
			agentDataupdate.achievementuser = achievements.map(achievement => ({
				// Mapear los datos recibidos a la estructura que necesitas, asumiendo que recibes los campos necesarios
				id: achievement.id,
				achievement_id: achievement.achievement_id,
				achievement_date: achievement.achievement_date,
				enable_achievement: achievement.enable_achievement,
				user_id: agentDataupdate.user.id,  // Asumiendo que hay un ID de usuario asociado
			}));
		};
		const updateAreaSpecialitiesUser = (specialities) => {
			console.log("Especialidades recibidas para actualizar:", specialities);
			agentDataupdate.areaSpecialityUser = specialities.map(speciality => ({
				area_speciality_id: speciality.area_speciality_id,
				user_id: agentDataupdate.user.id  // Asegúrate de que el ID del usuario está correctamente asignado
			}));
		};
		const originalData = reactive({});
		onMounted(() => {
		try {
			if (!props.agent?.original) {
			console.error(" Error: `props.agent.original` es null o undefined");
			return;
			}

			console.log("🔹 Datos del agente cargados desde el show:", props.agent.original);

			const cleanAgent = JSON.parse(JSON.stringify(toRaw(props.agent.original)));
			console.log("🔹 Datos del agente cargados:", cleanAgent);
            console.log("🔹 Datos de la comision cargada:", cleanAgent.commission_percentage);
			// ✅ Asignar los datos correctamente
			//originalData.value = cleanAgent; // Para `ref()`
			Object.assign(agentDataupdate, cleanAgent); // Para `reactive()`
            console.log("comision asignada:", agentDataupdate.commission_percentage);
            console.log("✅ Datos asignados correctamente:", agentDataupdate);
            console.log("🧪 Valor actual real:", agentDataupdate.commission_percentage); // <- esto te muestra el valor real actualizado

            setTimeout(() => {
            console.log("⏱ Después de 100ms:", agentDataupdate.commission_percentage); // <- esto también te va a mostrar 0.5 si está bien asignado
            }, 100);

            Object.assign(originalData, cleanAgent);

		} catch (err) {
			console.error(" Error al cargar el agente:", err);
			alert("No se pudo cargar el agente.");
		}
		});
	const getAgentStatusName = (id) => {
    if (!options.value || !options.value.agent_status) {
      console.log(" agent_statuses no está definido");
      return `ID desconocido (${id})`;
    }

    const status = options.value.agent_status.find((status) => status.id === id);
		 console.log(" Estado encontrado:", status);
    return status ? status.name : `Estado desconocido (${id})`;
    };
		const getAgentRegionName = (id) => {
    if (!props.f || !options.value.regions) {
      console.warn("⚠️ agent_statuses no está definido aún.");
      return `ID desconocido (${id})`;
    }
    const region = options.value.regions.find((region) => region.id === id);
		 console.log("Region  encontrado:", region);
    return region ? region.name : `Estado desconocido (${id})`;
    };
		const getAgentOfficeName = (id) => {
    if (!options.value || !options.value.offices) {
      console.warn("⚠️ agent_statuses no está definido aún.");
      return `ID desconocido (${id})`;
    }
    const office = options.value.offices.find((office) => office.id === id);
		 console.log("office encontrado:", office);
    return office ? office.name : `Estado desconocido (${id})`;
    };

		const getAgentEstatusdelEquipoName = (id) => {
    if (!options.value  || !options.value.teamStatuses) {
      console.warn(" no está definido aún.");
      return `ID desconocido (${id})`;
    }
    const teamstatus = options.value.teamStatuses.find((teamstatus) => teamstatus.id === id);
		 console.log("team status  encontrado:", teamstatus);
    return teamstatus ? teamstatus.name : `Estado desconocido (${id})`;
    };
		const getAgenttituloactualremaxName = (id) => {
    if (!options.value || !options.value.remax_titles) {
      console.warn("remax title no está definido aún.");
      return `ID desconocido (${id})`;
    }
    const remaxtitle = options.value.remax_titles.find((remaxtitle) => remaxtitle.id === id);
		 console.log("remax title  encontrado:", remaxtitle);
    return remaxtitle ? remaxtitle.name : `Estado desconocido (${id})`;
    };
		const getAgenttituloamostrarenremaxName = (id) => {
    if (!options.value || !options.value.remax_titles_to_show) {
      console.warn("remax title no está definido aún.");
      return `ID desconocido (${id})`;
    }
    const remaxtitletoshow = options.value.remax_titles_to_show.find((remaxtitletoshow) => remaxtitletoshow.id === id);
		 console.log("remax title  encontrado:", remaxtitletoshow);
    return remaxtitletoshow ? remaxtitletoshow.name : `Estado desconocido (${id})`;
    };
		const getAgenttitulocustomerpreferenceName = (id) => {
    if (!options.value || !options.value.customer_preference) {
      console.warn("remax title no está definido aún.");
      return `ID desconocido (${id})`;
    }
    const customer_preference = options.value.customer_preference.find((customer_preference) => customer_preference.id === id);
		 console.log("remax title  encontrado:", customer_preference);
    return customer_preference ?customer_preference.name : `Estado desconocido (${id})`;
    };

		const detectChanges = () => {
			const changes = [];

			const toStringValue = (value) => {
				if (value === null || value === undefined) {
					return '';
				}
				if (Array.isArray(value) || typeof value === 'object') {
					return JSON.stringify(value);
				}
				return String(value);
			};
			const getLaPazTimestamp = () => {
				return new Date().toLocaleString("es-BO", { timeZone: "America/La_Paz" });
			};
			const processField = (field, originalValue, currentValue) => {
				if (Array.isArray(currentValue) && currentValue.length === 0) {
					console.log(`Omitido campo "${field}" porque el nuevo valor es un array vacío.`);
					return; // Omite este campo si el nuevo valor es un array vacío
				}
				let processedOldValue = toStringValue(originalValue);
				let processedNewValue = toStringValue(currentValue);
					if (field === 'agent_status_id') {
					processedOldValue = getAgentStatusName(originalValue);
					processedNewValue = getAgentStatusName(currentValue);
        }
				if (field === 'region_id') {
					processedOldValue = getAgentRegionName(originalValue);
					processedNewValue = getAgentRegionName(currentValue);
        }
				if (field === 'office_id') {
					processedOldValue = getAgentOfficeName(originalValue);
					processedNewValue = getAgentOfficeName(currentValue);
        }
				if (field === 'user.team_status_id') {
					processedOldValue = getAgentEstatusdelEquipoName(originalValue);
					processedNewValue = getAgentEstatusdelEquipoName(currentValue);
        }
				if (field === 'user.remax_title_id') {
					processedOldValue = getAgenttituloactualremaxName(originalValue);
					processedNewValue = getAgenttituloactualremaxName(currentValue);
        }
				if (field === 'user.remax_title_to_show_id') {
					processedOldValue = getAgenttituloamostrarenremaxName(originalValue);
					processedNewValue = getAgenttituloamostrarenremaxName(currentValue);
        }
				if (field === 'user.customer_preference_id') {
					processedOldValue = getAgenttitulocustomerpreferenceName(originalValue);
					processedNewValue = getAgenttitulocustomerpreferenceName(currentValue);
        }
				if (processedNewValue !== processedOldValue) {
					changes.push({
						field,
						oldValue: processedOldValue,
						newValue: processedNewValue,
						timestamp: getLaPazTimestamp(),
					});
				}
			};

			Object.keys(agentDataupdate).forEach(field => {
				if (field === 'user' && typeof agentDataupdate[field] === 'object') {
					Object.keys(agentDataupdate.user).forEach(subField => {
						const originalValue = originalData.user[subField];
						const currentValue = agentDataupdate.user[subField];
						processField(`user.${subField}`, originalValue, currentValue);
					});
				} else {
					const originalValue = originalData[field];
					const currentValue = agentDataupdate[field];
					processField(field, originalValue, currentValue);
				}
			});

			return changes;
		};

		const validateAgentData = async () => {
			try {
				Object.keys(validationErrors).forEach((key) => delete validationErrors[key]);
				await agentDataSchema.validate(agentDataupdate, { abortEarly: false });
				return true;
			} catch (err) {
				if (err instanceof yup.ValidationError) {
					err.inner.forEach((validationError) => {
						if (validationError.path) {
							validationErrors[validationError.path] = validationError.message;
						}
					});
				}
				return false;
			}
		};
		// Tabs configurados dinámicamente
		const tabs = ref([
			{ name: "PersonalTabEdit", label: "Personal", value: "0", icon: "pi-user", component: PersonalTabEdit },
			{ name: "MarketingTabEdit", label: "Marketing", value: "1", icon: "pi-chart-line", component: MarketingTabEdit },
			{ name: "SpecializationTabEdit", label: "Especialización", value: "2", icon: "pi-briefcase", component: SpecializationTabEdit },
			{ name: "AchievementTabEdit", label: "Logros", value: "3", icon: "pi-globe", component: AchievementTabEdit },
			{ name: "SkillsTabEdit", label: "Aptitudes", value: "5", icon: "pi-check-circle", component: SkillsTabEdit },
			{ name: "HistorialTabEdit", label: "Historial", value: "6", icon: "pi-check-circle", component: HistorialTabEdit },
      { name: "DocumentationTabEdit", label: "Documentation", value: "7", icon: "pi-file", component: DocumentationTabEdit },
     // { name: "ConfigurationTabEdit", label: "Configuracion", value: "8", icon: "pi-cog", component: ConfigurationTabEdit },
		]);

    if (role.value?.toLowerCase() === 'administrador') {
      tabs.value.push({
        name: "ConfigurationTabEdit",
        label: "Configuración",
        value: "8",
        icon: "pi-cog",
        component: ConfigurationTabEdit,
      });
      }
		const changeLogs = reactive([]); // Array para registrar los logs de cambios
		const roleId = ref(null);
        const goBack = () => {
        setTimeout(() => {
            router.visit(route("agents.index")); // 👈 Redirige usando Inertia a la lista de agentes
        }, 1000); // Espera 1 segundo
        };


const isSubmitting = ref(false);
const passwordError = ref("");

const validatePassword = () => {
  if (!agentDataupdate.user.password || agentDataupdate.user.password.trim() === "") {
    return true; // ✅ Si el usuario no ingresó contraseña, no validamos nada.
  }

  if (agentDataupdate.user.password.length < 8) {
    passwordError.value = "La contraseña debe tener al menos 8 caracteres.";

    // Mostrar mensaje en `toast`
    toast.add({
      severity: "warn",
      summary: "Contraseña Inválida",
      detail: "La contraseña debe tener al menos 8 caracteres.",
      life: 3000,
    });

    return false; // ❌ Detener la ejecución si no cumple la regla
  }

  passwordError.value = ""; // ✅ Borrar el error si la contraseña es válida
  return true;
};
const formatDate = (date) => {
            if (date instanceof Date) {
            return date.toISOString().split('T')[0]; // 'YYYY-MM-DD'
            }
            return date;
};

const updateAgent = async () => {
  if (isSubmitting.value) {
    console.warn("La actualización ya está en proceso. Ignorando nueva solicitud.");
    return;
  }
  isSubmitting.value = true; // Bloquear múltiples solicitudes

  const isValid = await validateAgentData();
  if (!isValid) {
    toast.add({
      severity: "error",
      summary: "Errores en el formulario",
      detail: Object.values(validationErrors).join("\n"),
      life: 5000,
    });
    isSubmitting.value = false; // Desbloquear en caso de error
    return;
  }


  if (!validatePassword()) {
    isSubmitting.value = false;
    return;
  }

  const changeLogs = detectChanges(); // Obtener logs de cambios

  console.log("Datos enviados:", {
    id: agentDataupdate.id,
    data: agentDataupdate,
    logs: changeLogs,
  });

  try {

    if (role.value?.toLowerCase() === "broker") {
    agentDataupdate.agent_status_id = 4;
    console.log("🔐 Estado forzado a 4 por rol broker");
    }
	console.log("Datos enviados antes de conversión a FormData:", agentDataupdate);

    // Crear FormData
    const formData = new FormData();
    formData.append("_method", "PUT"); // Laravel requiere esto para `PUT`

    // Asegurarse de incluir las fechas formateadas
    const dateFields = [
    "date_joined",
    "date_termination",
    "expiration_date_license",
    ];

    for (const key in agentDataupdate) {
    if (agentDataupdate[key] !== null && agentDataupdate[key] !== undefined) {
        if (dateFields.includes(key)) {
        formData.append(key, formatDate(agentDataupdate[key]));
        } else if (
        typeof agentDataupdate[key] !== "object" ||
        agentDataupdate[key] instanceof File
        ) {
        formData.append(key, agentDataupdate[key]);
        }
    }
    }
    const userDateFields = ["birthdate", "remax_start_date"];

     // 1. Agregar datos del usuario si existen (excepto roles)
    if (agentDataupdate.user && typeof agentDataupdate.user === "object") {
    for (const key in agentDataupdate.user) {
        if (
        agentDataupdate.user[key] !== null &&
        agentDataupdate.user[key] !== undefined &&
        key !== "roles"
        ) {
        if (userDateFields.includes(key)) {
            formData.append(`user[${key}]`, formatDate(agentDataupdate.user[key]));
        } else {
            formData.append(`user[${key}]`, agentDataupdate.user[key]);
        }
        }
    }

    // 2. Agregar roles como parte de user[roles][i]
    if (Array.isArray(agentDataupdate.user.roles)) {
        agentDataupdate.user.roles.forEach((role, index) => {
        if (role?.id) {
            formData.append(`user[roles][${index}][id]`, role.id );
            formData.append(`user[roles][${index}][name]`, role.name ?? '');
        }
        });
    }
    }


    // Agregar redes sociales
    if (Array.isArray(agentDataupdate.socialNetworks)) {
      agentDataupdate.socialNetworks.forEach((network, index) => {
        formData.append(`socialNetworks[${index}][id]`, network.id ?? '');
        formData.append(`socialNetworks[${index}][name]`, network.name);
        formData.append(`socialNetworks[${index}][url]`, network.url);
        formData.append(`socialNetworks[${index}][state]`, network.state);
      });
    }

    // Agregar especialidades del usuario
    if (Array.isArray(agentDataupdate.areaSpecialityUser)) {
    agentDataupdate.areaSpecialityUser.forEach((speciality, index) => {
        formData.append(`areaSpecialityUser[${index}][area_speciality_id]`, speciality.area_speciality_id);
        formData.append(`areaSpecialityUser[${index}][user_id]`, speciality.user_id);
    });
    }


    if (Array.isArray(agentDataupdate.achievementuser)) {
    agentDataupdate.achievementuser.forEach((achievement, index) => {
        formData.append(`achievementuser[${index}][id]`, achievement.id ?? '');
        formData.append(`achievementuser[${index}][achievement_id]`, achievement.achievement_id);
        formData.append(`achievementuser[${index}][achievement_date]`, achievement.achievement_date ?? '');
        formData.append(`achievementuser[${index}][enable_achievement]`, achievement.enable_achievement);
        formData.append(`achievementuser[${index}][user_id]`, achievement.user_id);
    });
    }

    agentDataupdate.permissions.forEach((permission, index) => {
    formData.append(`permissions[${index}]`, permission);
    });


    // Agregar documentos
    if (agentDataupdate.documents && agentDataupdate.documents.length > 0) {
      agentDataupdate.documents.forEach((doc, index) => {
        formData.append(`archivo[]`, doc.file);
        formData.append(`descriptions[]`, doc.description || "");
        formData.append(`types[]`, doc.type);
      });
    }

    // Mostrar datos en consola antes de enviarlos
    for (let pair of formData.entries()) {
      console.log(`📝 FormData -> ${pair[0]}:`, pair[1]);
    }


	if (Array.isArray(changeLogs)) {
  changeLogs.forEach((log, index) => {
    formData.append(`logs[${index}][field]`, log.field || '');
    formData.append(`logs[${index}][oldValue]`, log.oldValue || '');
    formData.append(`logs[${index}][newValue]`, log.newValue || '');
    formData.append(`logs[${index}][timestamp]`, log.timestamp || '');
  });
}

	// También Laravel requiere esto para simular PUT en FormData
	formData.append("_method", "PUT");

	// Enviar todo
	const response = await axios.post(route("agents.update", agentDataupdate.id), formData, {
	headers: {
		"Content-Type": "multipart/form-data",
	},
	});

    console.log("Actualización exitosa:", response.data);

    toast.add({
      severity: "success",
      summary: "Éxito",
      detail: "Agente actualizado correctamente.",
      life: 3000,
    });

    setTimeout(() => {
      window.location.reload();
    }, 1000);
  } catch (error) {
    console.error("Error al actualizar el agente:", error);

    let errorMessage = "Hubo un problema al actualizar el agente.";
    if (error.response && error.response.data.errors) {
      errorMessage = Object.values(error.response.data.errors).flat().join("\n");
    }

    toast.add({
      severity: "error",
      summary: "Error",
      detail: errorMessage,
      life: 5000,
    });
  } finally {
    isSubmitting.value = false; // Desbloquear después del proceso
  }
};
</script>
<style scoped>
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

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
