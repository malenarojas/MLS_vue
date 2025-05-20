<template  >
	<div v-if="agentDataupdate">
  <!-- Información Personal -->
	<div class="section-title">Información Personal</div>
    <div class="form-group">
      <div class="field">
        <label  for="first_name"class="block">Nombre</label>
        <input type="text" v-model= "agentDataupdate.user.first_name" id="firt_name" placeholder="Ingrese su nombre" />
      </div>
      <div class="field">
        <label for="last_name" >Apellido</label>
        <input v-model= "agentDataupdate.user.last_name" id="last_name" type="text" placeholder="Ingrese su apellido" />
      </div>
			<div class="field">
        <label for="name_to_show">Nombre a enseñar</label>
        <input v-model= "nameToShow" id="name_to_show"  type="text"  placeholder="Nombre a enseñar" readonly  />
      </div>
			<div class="field">
        <label for="ci">Cédula de Identidad</label>
        <input v-model= "agentDataupdate.user.ci" id="ci"  type="text"  placeholder="13174557Scz"/>
      </div>
      <div class="field">
        <label for="gender">Sexo</label>
        <select v-model="agentDataupdate.user.gender" id="gender">
          <option v-for="(gender, index) in genders" :key="index" :value="gender">
            {{ gender }}
          </option>
        </select>
      </div>
		<div class="field">
        <label for="birthdate">fecha de Nacimiento</label>
        <DatePicker v-model= "agentDataupdate.user.birthdate" id="birthdate"  showIcon placeholder="fecha nacimiento " />
        </div>
		<div class="field">
        <label for="username">Nombre de Usuario</label>
        <input v-model= "agentDataupdate.user.username" id="username" type="text"  placeholder="120001agent" />
      </div>
	  <div class="field">
				<label for="password">Contraseña</label>
				<div class="password-wrapper">
					<input
						v-model="agentDataupdate.user.password"
						id="password"
						:type="showPassword ? 'text' : 'password'"
						placeholder="Ingrese contraseña"/>
					<button type="button" @click="togglePasswordVisibility" class="toggle-password">
						{{ showPassword ? '👁️' : '🙈' }}
					</button>
				</div>
	  </div>
    </div>

	<!-- Información de Contacto -->
  <div class="section-title">Información de Contacto</div>
  <div class="form-group">
    <div class="field">
      <label for="address">Dirección</label>
      <input type="text" v-model="agentDataupdate.address"
      id="address" placeholder="Ingrese su dirección" />
    </div>
    <div class="field">
        <label for="landline_phone">Telefono fijo</label>
        <input type="text"v-model ="agentDataupdate.landline_phone" id ="landline_phone" placeholder="Ingrese teléfono fijo" />
      </div>
    <!-- <div class="field">
      <label>Redes Sociales</label>
      <input type="text" value="@andre_aviles" placeholder="Ingrese redes sociales" />
    </div>-->
		<div class="field">
        <label for ="email">Correo Electrónico</label>
        <input v-model = "agentDataupdate.user.email" id ="email" type="email" vnformalue="mail@gmail.com"  :readonly="!hasPermission('agent.edit.email')"  placeholder="Ingrese correo electrónico" />
      </div>
      <div class="field">
        <label for ="phone_number">Celular</label>
        <input type="text" v-model = "agentDataupdate.user.phone_number" id ="phone_number"  :readonly="!hasPermission('agent.edit.email')"  placeholder="Ingrese número de celular" />
      </div>
  </div>
  <!-- Información del Agente -->
	<div class="section-title" >Información del agente</div>
    <hr />
    <div class="form-group">
	  <div class="field">
		<label for="countries_interested:">Idioma Preferido</label>
			<select v-model="agentDataupdate.countries_interested" id="preferred-language">
				<option disabled value="">No seleccionado</option>
					<option v-for="(language, index) in languages" :key="index" :value="language">
						{{ language }}
					</option>
					</select>
				</div>
      <div class="field">
        <label >Nº RE/MAX Internacional</label>
        <input type="text" value="7626916" placeholder="Ingrese número" />
      </div>
	  <div class="field">
        <label for="remax_start_date" >Fecha de Inicio en Remax</label>
        <DatePicker v-model= "agentDataupdate.user.remax_start_date" id="remax_start_date" showIcon
         placeholder="Fecha inicio remax" />
      </div>
      <div class="field">
        <label>ID del Agente Empresarial</label>
        <input type="text" value="7626916" placeholder="Ingrese ID" />
      </div>
	  <div class="field">
		<label for="region_id">Region</label>
		<Select
			v-model="agentDataupdate.region_id"
			:options="props.options?.regions || []"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione una región"
			class="w-full md:w-56"
			>
			<template #option="slotProps">
				<!-- slotProps.option es el objeto de la región -->
				<div>
				{{ slotProps.option.name }}
				</div>
			</template>
			</Select>
	  </div>
	  <div class="field">
		<label for="office_id">Oficina</label>
		<Select
		v-model="agentDataupdate.office_id"
		:options="filteredOffices"
		optionLabel="name"
    optionValue="office_id"
		placeholder="Seleccione una oficina"
		class="w-full md:w-56"
		>
		<template #option="slotProps">
			<!-- Mostrar id, name y city -->
			<div>
			 {{ slotProps.option.name }} ({{ slotProps.option.city }})
			</div>
		</template>
		</Select>
	  </div>
	    <!-- Estatus del Equipo -->
		<div class="field">
		<label for="team_status_id">Estatus del Equipo</label>
		<Select
			v-model="agentDataupdate.user.team_status_id"
			:options="props.options?.team_statuses"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione un estatus"
			class="w-full md:w-56"
		>
			<template #option="slotProps">
			<div>{{ slotProps.option.name }}</div>
			</template>
		</Select>
		</div>

		<div class="field">
		<label for="remax_title_id">Título Actual en Remax</label>
		<Select
			v-model="agentDataupdate.user.remax_title_id"
			:options="props.options?.remax_titles"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione un título"
			class="w-full md:w-56"
		>
			<template #option="slotProps">
			<div>{{ slotProps.option.name }}</div>
			</template>
		</Select>
		</div>

		<div class="field">
		<label for="remax_title_to_show_id">Título a Enseñar</label>
		<Select
			v-model="agentDataupdate.user.remax_title_to_show_id"
			:options="props.options?.remax_titles_to_show"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione un título"
			class="w-full md:w-56"
		>
			<template #option="slotProps">
			<div>{{ slotProps.option.name }}</div>
			</template>
		</Select>
		</div>
        <div class="field">
		<label for="role_id">Rol del usuario</label>
		<Select
			v-model="selectedRoleId"
			:options="filteredRoles"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione un estado"
			class="w-full md:w-56"
		>
			<template #option="slotProps">
				<div
				>
					{{ slotProps.option.name }}
				</div>
			</template>
		</Select>
	</div>


	  <div class="field">
		<label for="agent_status_id">Estado del Agente</label>
		<Select
			v-model="agentDataupdate.agent_status_id"
			:options="filteredAgentStatus"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione un estado"
			class="w-full md:w-56"
		>
			<template #option="slotProps">
				<div
				>
					{{ slotProps.option.name }}
				</div>
			</template>
		</Select>
	</div>
		<div v-if="agentDataupdate.agent_status_id === 5" class="field">
      <label for="rejection_reason">Motivo del Rechazo</label>
      <input
        type="text"
        id="rejection_reason"
        v-model="props.agentDataupdate.rejectionReason"
        placeholder="Por qué se está rechazando"
        class="w-full md:w-56"
      />
    </div>
    </div>
	   <!-- Habilitar Chat -->
	<div class="section-title" >Habilitar Chat</div>
		<div class="chat-group" >
			<!-- Telegram -->
			<div class="chat-item">
				<i class="fab fa-telegram chat-icon"></i>
				<div class="chat-content">
					<label for="telegram-input">Telegram</label>
					<input id="telegram-input" type="text" placeholder="Usuario de Telegram" />
				</div>
				<input type="checkbox" class="chat-checkbox" />
			</div>
			<!-- Viber -->
			<div class="chat-item">
				<i class="fab fa-viber chat-icon"></i>
				<div class="chat-content">
					<label for="viber-input">Viber</label>
					<input id="viber-input" type="text" placeholder="Usuario de Viber" />
				</div>
				<input type="checkbox" class="chat-checkbox" />
			</div>
			<!-- Facebook Messenger -->
			<div class="chat-item">
				<i class="fab fa-facebook-messenger chat-icon"></i>
				<div class="chat-content">
					<label for="messenger-input">Facebook Messenger</label>
					<input id="messenger-input" type="text" placeholder="Usuario de Facebook Messenger" />
				</div>
				<input type="checkbox" class="chat-checkbox" />
			</div>
			<!-- WhatsApp -->
			<div class="chat-item">
				<i class="fab fa-whatsapp chat-icon"></i>
				<div class="chat-content">
					<label for="whatsapp-input">WhatsApp</label>
					<input id="whatsapp-input" type="text" placeholder="Número de WhatsApp" />
				</div>
				<input type="checkbox" class="chat-checkbox" />
			</div>
		</div>
  <!-- Información de Carrera -->
  <div class="section-title">Información de Carrera</div>
  <div class="form-group"  >
    <div class="field">
      <label for="date_joined"  >Fecha de Inicio</label>
			<DatePicker
			v-model="agentDataupdate.date_joined"
			id="date_joined"
			showIcon
			placeholder="Ingrese fecha de inicio"
		/>
    </div>
		<div class="field">
      <label for="date_termination">Fecha de Fin</label>
      <DatePicker  v-model= "agentDataupdate.date_termination" id="date_termination" showIcon placeholder="Ingrese fecha de inicio" />
    </div>
    <div class="field">
      <label for = "license_type">Tipo de Licencia</label>
      <input v-model = "agentDataupdate.license_type" id="license_type"type="text"  placeholder="Ingrese tipo de licencia" />
    </div>
    <div class="field">
      <label for ="license_number">Número de Licencia</label>
      <input v-model = "agentDataupdate.license_number" id ="license_number"type="text" placeholder="Ingrese número de licencia" />
    </div>
    <div class="field">
      <label for = "year_obtained_license">Año que Obtuvo Licencia</label>
      <input v-model = "agentDataupdate.year_obtained_license" id= "year_obtanied"type="text" wplaceholder="Ingrese año" />
    </div>
		<div class="field">
      <label for = "license_department"  >Dpto que obtubo la licencia</label>
      <input v-model = "agentDataupdate.license_department" id = "license_department" type="Text"  placeholder="departamento donde obtubo la licencia" />
    </div>
		<div class="field">
      <label for="expiration_date_license"  >fecha de caducidad</label>
      <DatePicker  v-model = "agentDataupdate.expiration_date_license" id="expiration_date_license" showIcon placeholder="Ingrese año" />
    </div>
    <div class="field">
      <label>Años de Experiencia</label>
      <input type="text" value="5" placeholder="Ingrese años de experiencia" />
    </div>
    <div class="field">
      <label for = "previus_occupation">Ocupacion previa a RE/MAX</label>
      <input  v-model= "agentDataupdate.previous_occupation"  id="previous_occupation"type="text"  placeholder="Ingrese último cargo" />
    </div>
    <div class="field">
      <label>Áreas de Especialización</label>
      <input type="text" value="Venta de propiedades" placeholder="Ingrese áreas de especialización" />
    </div>
		<div class="field">
      <label >Estudios</label>
      <input v-model = "agentDataupdate.studies" id= "studies"type="text" placeholder="Ingrese áreas de especialización" />
    </div>
		<div class="field">
      <label for="addtional_education" >Educacion Adicional</label>
      <input v-model = "agentDataupdate.additional_education" id ="additional_education"    type="text" placeholder="Ingrese áreas de especialización" />
    </div>


    <div class="field">
  <label for="commission_percentage" class="mb-1 font-semibold text-gray-700">
    Comisión de Porcentaje
  </label>
    <InputNumber
      v-model="props.agentDataupdate.commission_percentage"
      inputId="percent"
      suffix="%"
      :useGrouping="false"
    />
</div>
	<div class="field">
  <label for="custom_preference_id">Preferencia Personalizada</label>
  <Select
    id="custom_preference_id"
    v-model="agentDataupdate.user.customer_preference_id"
    :options="props.options?.customer_preference"
    optionLabel="name"
    optionValue="id"
    placeholder="Seleccione una preferencia"
    class="w-full md:w-56"
  >
    <template #option="slotProps">
      <div>
        {{ slotProps.option.name }}
      </div>
    </template>
    </Select>
    </div>

  </div>
</div>
</template>


<script setup>
import "@/assets/css/Agents/Tabpersonal.css";
//import { useAgentStore } from "@/stores/Agents"; // Importa el store
import { computed, defineProps, onMounted, ref, watch } from "vue";

// **Props con defineProps**
const props = defineProps({
  agentDataupdate: {
    type: Object,
    required: true,
  },
  options:{
    type: Object,
    required: true,
  },
  user:{
    type: Object,
    required: true,
  },
  role: {
    type: Object,
    required: true,
  },
  permissions:
  {
    type: Object,
    required: true,
  },
});
console.log("rollllllllll", props.agentDataupdate.user.roles);
console.log("datos de usuario", props.user);
const roleId = ref(null); // Rol actual


// **Estados reactivos**
const loading = ref(false);
const error = ref(null);
const showPassword = ref(false);

const roleName = ref(props.role|| {});

//const router = useRouter();
const userOfficeId = ref(null);

const filteredRoles = computed(() => {
  const allRoles = props.options.all_roles || [];
  const currentUserRole = roleName.value.trim().toLowerCase();

  if (currentUserRole === 'administrador') {
    return allRoles; // Muestra todos los roles
  }

  if (currentUserRole === 'broker') {
    return allRoles.filter(role => role.name.toLowerCase() === 'agente');
  }

  return []; // Otros roles no ven nada
});
const selectedRoleId = computed({
  get() {
    return props.agentDataupdate.user.roles?.[0]?.id || null;
  },
  set(newId) {
    const role = filteredRoles.value.find(r => r.id === newId);
    if (role) {
      props.agentDataupdate.user.roles = [role]; // Guardamos objeto completo { id, name }
    }
  },
});





const getUserRoleFromProps = () => {
  if (typeof roleName.value === 'string') {
    console.log("🎯 Rol recibido:", roleName.value);

    // Normalizar (por si lo querés comparar sin mayúsculas o tildes)
    const normalizedRole = roleName.value.trim().toLowerCase();

    // Ejemplo de validación
    if (normalizedRole === 'administrador') {
      console.log("✅ Es un Administrador 👑");
    } else if (normalizedRole === 'broker') {
      console.log("📢 Es un Broker");
    } else {
      console.warn("⚠️ Rol no reconocido:", roleName.value);
    }
  } else {
    console.warn("❌ Rol no es un string válido:", roleName.value);
  }
};


// **Listas de opciones**
const languages = ["English", "Portuguese - Bolivia", "Spanish - Bolivia"];
const genders = ["Masculino", "Femenino"];

// **Alternar visibilidad de la contraseña**
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};
const username = computed(() => {
  const firstName = props.agentDataupdate.user.first_name || "";
  const lastName = props.agentDataupdate.user.last_name || "";
  const officeId = props.agentDataupdate.office_id || "";

  const firstLetter = firstName.charAt(0);

  return `${officeId}${firstLetter}${lastName.toLowerCase()}`; // Generar username
});

watch(username, (newValue) => {
  if (!props.agentDataupdate.user.username) {
    props.agentDataupdate.user.username = newValue;
  }
});
const getDefaultBirthdate = () => {
  const defaultDate = new Date("2000-01-01");
  return defaultDate.toISOString().split("T")[0];
};

const formatDate = (dateString) => {
  if (!dateString) return dateString;

  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;

  return date.toISOString().split("T")[0];
};



// **Formatear el input cuando el usuario edita**
const formatInput = () => {
  formattedCommission.value = Number(formattedCommission.value).toFixed(0);
};
const nameToShow = computed(() => {
  const firstName = props.agentDataupdate.user.first_name || "";
  const lastName = props.agentDataupdate.user.last_name || "";
  return `${firstName} ${lastName}`.trim(); // Combina nombre y apellido
});
watch(nameToShow, (newValue) => {
  props.agentDataupdate.user.name_to_show = newValue;
});

// **Cargar opciones desde el store**
const fetchAgentOptions = async () => {
  loading.value = true;
  error.value = null;

  try {
    //await agentStore.fetchAgentOptions(); // Llama a la función del store
    console.log("Opciones cargadas:", props.options);
  } catch (err) {
    console.error("Error al obtener opciones del formulario:", err);
    error.value = "No se pudieron cargar las opciones del formulario.";
  } finally {
    loading.value = false;
  }
};


// Obtener `office_id`
const getOfficeFromLocalStorage = () => {
    const user = props.user;
if (user?.agent) {
  userOfficeId.value = user.agent.office_id || null;
  console.log*
  console.log("🏢 Oficina del usuario desde props:", userOfficeId.value);
} else {
  console.warn("No se encontró la oficina del usuario en los props.");
}
};

const filteredOffices = computed(() => {
  const offices = props.options.offices || [];
  console.log("oficcinas encontradass", offices);
  if (!Array.isArray(offices)) {
    console.error(" `offices` no es un array:", offices);
    return [];
  }
  const normalizedRole = (roleName.value || '').toLowerCase();

  console.log("📋 Filtrando oficinas con:", {
    allOffices: offices,
    userOfficeId: userOfficeId.value,
    roleName: normalizedRole,
  });

  if ( normalizedRole === 'broker') {
    console.log("oficcinas encontradas", offices);
    console.log("esta es la oficina del broker",userOfficeId.value);
    return offices.filter(office => office.office_id === userOfficeId.value);

  }

  if (normalizedRole === 'administrador') {
    console.log("oficcinas encontradas para el adm", offices);
    return offices;
  }
});
const hasPermission = (perm) => {
  const isAdmin = (roleName.value || '').toLowerCase() === 'administrador';
  return isAdmin || props.permissions?.some(p => p.name === perm);
};



onMounted(() => {
	fetchAgentOptions(); // Llamar a las opciones del agente
    getUserRoleFromProps();
    getOfficeFromLocalStorage();

	});


	// **Reaccionar a cambios en localStorage**
watch(() => localStorage.getItem("user"), getOfficeFromLocalStorage);

// **Filtrar Estados de Agente según Rol**
const filteredAgentStatus = computed(() => {
  const normalizedRole = (roleName.value || '').toLowerCase();
  if (props.options.agent_status && Array.isArray(props.options.agent_status)) {
    if (normalizedRole === 'administrador') {
      return props.options.agent_status.filter((status) =>
        [1, 2, 3, 4, 5].includes(status.id)
      );
    }
    if (normalizedRole === 'broker') {
      return props.options.agent_status.filter((status) =>  [ 1,2,3,4, 5].includes(status.id));
    }
    return props.options.agent_status;
  }
  return [];
});



</script>
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
</style>
