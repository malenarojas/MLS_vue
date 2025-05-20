<template  >
	<div v-if="agentData" >
  <!-- Información Personal -->
	<div class="section-title">Información Personal</div>
    <div class="form-group">
	  <div class="field">
		<label for="first_name" class="block">Nombre</label>
		<input
			type="text"
			v-model="agentData.user.first_name"
			id="first_name"
			placeholder="Ingrese su primer nombre"
            :class="{ 'border-red-500': validationErrors['user.first_name'] }"
		/>
		<span v-if="validationErrors['user.first_name']" class="text-sm text-red-600">
         {{ validationErrors['user.first_name'] }}
        </span>
	  </div>

      <div class="field">
        <label for="last_name" >Apellido</label>
        <input v-model= "agentData.user.last_name" id="last_name" type="text" placeholder="Ingrese su apellido" />
      </div>
			<div class="field">
        <label for="name_to_show">Nombre a enseñar</label>
        <input v-model= "nameToShow" id="name_to_show"  type="text"  placeholder="Nombre a enseñar" readonly />
      </div>
	  <div class="field">
		<label for="ci">Cédula de Identidad</label>
		<input
			v-model="agentData.user.ci"
			id="ci"
			type="text"
    :class="{ 'border-red-500': validationErrors['user.ci'] }"
		/>
		<span v-if="validationErrors['user.ci']" class="text-sm text-red-600">
    {{ validationErrors['user.ci'] }}
  </span>
		</div>
      <div class="field">
				<label for="gender">Sexo</label>
        <select v-model="agentData.user.gender" id="gender"
				 :class="{ 'border-red-500': validationErrors['user.gender'] }">
					<option v-for="(gender, index) in genders" :key="index" :value="gender">
						{{ gender }}
					</option>
				</select>
				<span v-if="validationErrors['user.gender']" class="error-message">
			   {{ validationErrors['user.gender'] }}
		  </span>
      </div>
		<div class="field">
        <label for="birthdate">fecha de Nacimiento</label>
        <DatePicker v-model= "agentData.user.birthdate" id="birthdate" showIcon placeholder="fecha nacimiento " :class="{ 'border-red-500': validationErrors['user.birthdate'] }"/>
      <span v-if="validationErrors['user.birthdate']" class="error-message">
          {{ validationErrors['user.birthdate'] }}
      </span>
        </div>
        <div class="field">
        <label for="username">Nombre de Usuario</label>
        <input
          v-model="agentData.user.username"
          id="username"
          type="text"
          placeholder="120001agent"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          :class="{ 'border-red-500': validationErrors['user.username'] }"
                readonly
        />
        <span v-if="validationErrors['user.username']" class="text-sm text-red-600">
          {{ validationErrors['user.username'] }}
        </span>
        </div>
		<div class="field">
  <label for="password">Contraseña</label>
  <div class="password-wrapper">
    <input
      v-model="agentData.user.password"
      id="password"
      :type="showPassword ? 'text' : 'password'"
      placeholder="Ingrese contraseña"
      :class="{ 'border-red-500': validationErrors['user.password'] }"
    />
    <button
      type="button"
      @click="togglePasswordVisibility"
      class="toggle-password"
    >
      {{ showPassword ? '👁️' : '🙈' }}
    </button>
  </div>
  <span v-if="validationErrors['user.password']" class="text-sm text-red-600">
    {{ validationErrors['user.password'] }}
  </span>
</div>
    </div>

	 <!-- Información de Contacto -->
	 <div class="section-title">Información de Contacto</div>
  <div class="form-group">
    <div class="field">
      <label for="address">Dirección</label>
      <input type="text" v-model="agentData.address"
      id="address" placeholder="Ingrese su dirección" />
    </div>
    <!--<div class="field">
      <label>Redes Sociales</label>
      <input type="text" value="@andre_aviles" placeholder="Ingrese redes sociales" />
    </div>-->
	<div class="field">
  <label for="email">Correo Electrónico</label>
  <input
    type="email"
    v-model="agentData.user.email"
    id="email"

     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
    :class="{ 'border-red-500': validationErrors['user.email'] }"
  />
  <span v-if="validationErrors['user.email']" class="text-sm text-red-600">
    {{ validationErrors['user.email'] }}
  </span>
</div>
      <div class="field">
        <label for ="phone_number">Celular</label>
        <input type="text" v-model = "agentData.user.phone_number" id ="phone_number"
				 placeholder="Ingrese número de celular"
				 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
				 :class="{ 'border-red-500': validationErrors['user.email'] }"/>
				 <span v-if="validationErrors['user.phone_number']" class="text-sm text-red-600">
					{{ validationErrors['user.phone_number'] }}
				</span>
      </div>
      <div class="field">
        <label for="landline_phone">Telefono fijo</label>
        <input type="text"v-model = "agentData.landline_phone" id ="landline_phone" placeholder="Ingrese teléfono fijo" />
      </div>
  </div>
  <!-- Información del Agente -->
	<div class="section-title" >Información del agente</div>
    <hr />
    <div class="form-group">
	  <div class="field">
		<label for="countries_interested:">Idioma Preferido</label>
			<select v-model="agentData.countries_interested" id="preferred-language">
				<option disabled value="">No seleccionado</option>
					<option v-for="(language, index) in languages" :key="index" :value="language">
						{{ language }}
					</option>
			</select>
			</div>
      <div class="field">
        <label for="nro_internacional_remax">Nº RE/MAX Internacional</label>
        <input v-model= "agentData.nro_internacional_remax" id="nro_internacional_remax" type="number" value="7626916" placeholder="Ingrese número" />
      </div>
	    <div class="field">
        <label for="remax_start_date" >Fecha de Inicio en Remax</label>
        <DatePicker v-model= "agentData.user.remax_start_date" id="remax_start_date" showIcon placeholder="Fecha inicio remax" />
      </div>
      <div class="field">
        <label for="id_business_agent">ID del Agente Empresarial</label>
        <input  v-model= "agentData.id_business_agent" id="id_business_agent" type="number" value="7626916" placeholder="Ingrese ID" />
      </div>
			<div class="field">
      <label for="region_id">Región</label>
      <div :class="{ 'border-red-500': validationErrors['region_id'] }" class="relative w-full">
        <Select
              v-model="agentData.region_id"
              :options="props.options.formOptions.regions"
              optionLabel="name"
              optionValue="id"
              placeholder="Seleccione una región"
              class="w-full md:w-56"
              :invalid="!!validationErrors['region_id']"
            >
          <template #option="slotProps">
            <div>{{ slotProps.option.name }}</div>
          </template>
        </Select>
      </div>
      <span v-if="validationErrors['region_id']" class="text-sm text-red-600">
        {{ validationErrors['region_id'] }}
      </span>
    </div>

    <div class="field">
      <label for="office_id">Oficina</label>
      <div :class="{ 'border-red-500': props.validationErrors['office_id'] }" class="relative w-full">
        <Select
          v-model="agentData.office_id"
          :options="filteredOffices"
          optionLabel="name"
          optionValue="office_id"
          placeholder="Seleccione una oficina"
          class="w-full md:w-56"
          :invalid="!!props.validationErrors['office_id']"
        >
          <template #option="slotProps">
            <div>{{ slotProps.option.name }}</div>
          </template>
        </Select>
      </div>
      <span v-if="props.validationErrors['office_id']" class="text-sm text-red-600">
        {{ props.validationErrors['office_id'] }}
      </span>
    </div>

	    <!-- Estatus del Equipo -->
		<div class="field">
		<label for="team_status_id">Estatus del Equipo</label>
		<Select
			v-model="agentData.user.team_status_id"
			:options="props.options.formOptions.team_statuses"
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
      <label for="agent_status_id">Estado del Agente</label>
      <div :class="{ 'border-red-500': props.validationErrors['agent_status_id']}" class="relative w-full">
        <Select
          v-model="agentData.agent_status_id"
          :options="filteredAgentStatus"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione un estado"
          class="w-full md:w-56"
          :invalid="!!props.validationErrors['agent_status_id']"
        >
          <template #option="slotProps">
            <div>{{ slotProps.option.name }}</div>
          </template>
        </Select>
      </div>
      <span v-if="props.validationErrors['agent_status_id']" class="text-sm text-red-600">
        {{ props.validationErrors['agent_status_id'] }}
      </span>
    </div>

		<div class="field">
		<label for="remax_title_id">Título Actual en Remax</label>
		<Select
			v-model="agentData.user.remax_title_id"
			:options="props.options.formOptions.remax_titles"
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
			v-model="agentData.user.remax_title_to_show_id"
			:options="props.options.formOptions.remax_titles_to_show"
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
		<label for="role_id">Roles</label>
    <div  :class="{ 'border-red-500': validationErrors['role_id'] }" class="relative w-full">
		<Select
			v-model="agentData.role_id"
			:options="filteredRoles"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione un título"
			class="w-full md:w-56"
      :invalid="!!validationErrors['role_id']"
		>
			<template #option="slotProps">
			<div>{{ slotProps.option.name }}</div>
			</template>
		</Select>
    </div>
    <span v-if="validationErrors['role_id']" class="text-sm text-red-600">
    {{ validationErrors['role_id'] }}
    </span>
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
			v-model="agentData.date_joined"
			id="date_joined"
			showIcon
			fluid
			:showOnFocus="false"
			placeholder="Ingrese fecha de inicio"
		/>
    </div>
		<div class="field">
      <label for="date_termination">Fecha de Fin</label>
      <DatePicker  v-model= "agentData.date_termination" id="date_termination" showIcon placeholder="Ingrese fecha de inicio" />
    </div>
    <div class="field">
      <label for = "license_type">Tipo de Licencia</label>
      <input v-model = "agentData.license_type" id="license_type"type="text"  placeholder="Ingrese tipo de licencia" />
    </div>
    <div class="field">
      <label for ="license_number">Número de Licencia</label>
      <input v-model = "agentData.license_number" id ="license_number"type="text" placeholder="Ingrese número de licencia" />
    </div>
    <div class="field">
      <label for = "year_obtained_license">Año que Obtuvo Licencia</label>
      <input v-model = " agentData.year_obtained_license" id= "year_obtanied"type="text" wplaceholder="Ingrese año" />
    </div>
		<div class="field">
      <label for = "license_department"  >Dpto que obtubo la licencia</label>
      <input v-model = "agentData.license_department" id = "license_department" type="Text"  placeholder="departamento donde obtubo la licencia" />
    </div>
		<div class="field">
      <label for="expiration_date_license"  >fecha de caducidad</label>
      <DatePicker  v-model = "agentData.expiration_date_license" id="expiration_date_license" showIcon placeholder="Ingrese año" />
    </div>
    <div class="field">
      <label>Años de Experiencia</label>
      <input type="text" value="5" placeholder="Ingrese años de experiencia" />
    </div>
    <div class="field">
      <label for = "previus_occupation">Ocupacion previa a RE/MAX</label>
      <input  v-model= "agentData.previous_occupation"  id="previous_occupation"type="text"  placeholder="Ingrese último cargo" />
    </div>
    <div class="field">
      <label>Áreas de Especialización</label>
      <input type="text" value="Venta de propiedades" placeholder="Ingrese áreas de especialización" />
    </div>
		<div class="field">
      <label >Estudios</label>
      <input v-model = "agentData.studies" id= "studies"type="text" placeholder="Ingrese áreas de especialización" />
    </div>
		<div class="field">
      <label for="addtional_education" >Educacion Adicional</label>
      <input v-model = "agentData.additional_education" id ="additional_education"    type="text" placeholder="Ingrese áreas de especialización" />
    </div>
		<div class="field">
      <label for = "commission_percentage">Comssion de porcentage</label>
      <input v-model = "agentData.commission_percentage" id ="commission_percentage"    type="number" placeholder="Ingrese áreas de especialización" />
    </div>
	<div class="field">
  <label for="custom_preference_id">Preferencia Personalizada</label>
  <Select
    id="custom_preference_id"
    v-model="agentData.user.customer_preference_id"
    :options="props.options.formOptions.customer_preference"
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
//import { useAgentStore } from "@/Stores/Agents"; // Importa el store
import { computed, onMounted, ref, watch } from "vue";

import DatePicker from 'primevue/datepicker';

// **Props**
const props = defineProps({
  agentData: {
    type: Object,
    required: true,
  },
  validationErrors: {
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
});


const loading = ref(false);
const error = ref(null);
const roleId = ref(null);
const user = ref(props.user|| {});
const roleName = ref(props.role|| {});
console.log("opciones cargadas", props.options);
//const router = useRouter();
const userOfficeId = ref(null);

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
const filteredRoles = computed(() => {
  const allRoles = props.options.formOptions?.all_roles || [];
  const currentUserRole = roleName.value.trim().toLowerCase();

  if (currentUserRole === 'administrador') {
    return allRoles; // Muestra todos los roles
  }

  if (currentUserRole === 'broker') {
    return allRoles.filter(role => role.name.toLowerCase() === 'agente');
  }

  return []; // Otros roles no ven nada
});

watch(filteredRoles, (newVal) => {
  console.log("Roles visibles para este usuario:", newVal);
});

const nameToShow = computed(() => {
  const firstName = props.agentData.user.first_name || "";
  const lastName = props.agentData.user.last_name || "";
  return `${firstName} ${lastName}`.trim(); // Combina nombre y apellido
});
watch(nameToShow, (newValue) => {
  props.agentData.user.name_to_show = newValue;
});
const username = computed(() => {
  const firstName = props.agentData.user.first_name || ""; // Obtener el primer nombre
  const lastName = (props.agentData.user.last_name || "").replace(/\s+/g, "");
  const officeId = props.agentData.office_id || ""; // Obtener el `office_id` como base

  const firstLetter = firstName.charAt(0); // Obtener la primera letra en minúscula

  return `${officeId}${firstLetter}${lastName.toLowerCase()}`; // Generar username
});

watch(username, (newValue) => {
  props.agentData.user.username = newValue;
});
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

const getDefaultBirthdate = () => {
  const defaultDate = new Date("2000-01-01");
  return defaultDate.toISOString().split("T")[0];
};

watch(
  () => props.agentData.user.birthdate,
  (newValue) => {
    if (!newValue) {
      props.agentData.user.birthdate = getDefaultBirthdate();
    }
  },
  { immediate: true } // ✅ Se ejecuta al inicio
);

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
  const offices = props.options.formOptions.offices || [];
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


// **Ejecutar al montar el componente**
onMounted(() => {
	if (!props.agentData.user.birthdate) {
    props.agentData.user.birthdate = getDefaultBirthdate();
  }
  if (!props.agentData.user.gender) {
    props.agentData.user.gender = "Masculino"; // Asignar "Masculino" si no hay valor
  }
  if (!props.agentData.agent_status_id) {
    props.agentData.agent_status_id = 4; // Valor predeterminado al cargar
  }

	if (!props.agentData.user.remax_title_to_show_id) {
    props.agentData.user.remax_title_to_show_id = 1; // Valor predeterminado al cargar
  }
  if (!props.agentData.region_id) {
    props.agentData.region_id = 120; // Valor predeterminado al cargar
  }
	if (!props.agentData.user.remax_title_id) {
    props.agentData.user.remax_title_id = 1; // Valor predeterminado al cargar
  }
	if (!props.agentData.team_statuses) {
    props.agentData.team_status_id = 2; // Valor predeterminado al cargar
  }

  fetchAgentOptions(); // Llamar a las opciones del agente
  getUserRoleFromProps();
  getOfficeFromLocalStorage();


});
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const canEditFields = computed(() => {
  return normalizedRole === 'administrador'; // Solo rol 1 y 2 pueden editar
});
const genders = ref(["Masculino", "Femenino"]);
const languages = ["English", "Portuguese - Bolivia", "Spanish - Bolivia"];

// **Reaccionar a cambios en localStorage**
watch(() => localStorage.getItem("user"), getOfficeFromLocalStorage);

// **Filtrar Estados de Agente según Rol**
const filteredAgentStatus = computed(() => {
  const normalizedRole = (roleName.value || '').toLowerCase();
  if (props.options.formOptions.agent_status && Array.isArray(props.options.formOptions.agent_status)) {
    if (normalizedRole === 'administrador') {
      return props.options.formOptions.agent_status.filter((status) =>
        [1, 2, 3, 4, 5].includes(status.id)
      );
    }
    if (normalizedRole === 'broker') {
      return props.options.formOptions.agent_status.filter((status) => status.id === 4);
    }
    return props.options.formOptions.agent_status;
  }
  return [];
});



</script>
