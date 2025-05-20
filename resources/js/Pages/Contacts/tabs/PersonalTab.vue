<template>
	<div class="container pt-4">

		<!-- Información Personal -->
		<div class="grid grid-cols-12 gap-3 items-center mx-2">

			<div class="col-span-12 md:col-span-4  text-lg font-semibold">Información Personal </div>
			<InputGroup class="col-span-12 md:col-span-8">
				<Select v-model="datos.agent_id" size="small"  name="agent_id" :options="agentes" filter class="w-[100px]" 
				optionValue="id"
				optionLabel="agent"
					placeholder="Seleccione" />
				<InputGroupAddon size="small">
					<Button label="Transferir" severity="info" size="small" class="w-full p-0 m-0 text-end"
						iconPos="right" />
				</InputGroupAddon>
			</InputGroup>
		</div>
		<hr class="col-span-12 my-4 border-[#0ea5e9] border">
		<div class="grid grid-cols-12 gap-3">
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Título</label>
				<Select v-model="datos.title_id" class="w-full" size="small" name="title_id" optionValue="key_name"
				optionLabel="value" filter :options="titulos" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Saludo</label>
				<InputText size="small" type="text" v-model="datos.salutation" name="salutation" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Nombre</label>
				<InputText size="small" type="text" :invalid="contactStore.errors.name" v-model="datos.name" name="name" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Apellidos</label>
				<InputText size="small" type="text" :invalid="contactStore.errors.last_name" v-model="datos.last_name" name="last_name" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Nombre a enseñar</label>
				<InputText size="small" type="text" :invalid="contactStore.errors.display_name" v-model="datos.display_name" name="display_name" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3 flex items-center md:justify-center mt-5">
				<Checkbox v-model="datos.sell" binary  size="large" :value="datos.sell" :true-value=1 :false-value=0 />
				<label class="text-lg ml-2">Vender</label>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Empresa</label>
				<InputText size="small" type="text" :invalid="contactStore.errors.company" v-model="datos.company" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-[14px] font-normal ml-2 mb-2">Nombre de Departamento</label>
				<InputText size="small" type="text" v-model="datos.department_name" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Número de identificación</label>
				<InputText size="small" type="text" v-model="datos.identification_number" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Fecha de nacimiento</label>
				<DatePicker @update:modelValue="setDateI" size="small" :manualInput="false" v-model="datos.birth_day"
					dateFormat="dd/mm/yy" showIcon fluid iconDisplay="input" name="birth_day" inputId="icondisplay" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-4">
				<label class="text-base font-normal ml-2 mb-2">Recordatorio Cumpleaños</label>
				<Select v-model="datos.birthday_reminder" class="w-full" size="small" name="birthday_reminder" optionValue="key_name"
				optionLabel="value" filter :options="recordatorios" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-5">
				<label class="text-base font-normal ml-2 mb-2">Plantilla de cumpleaños predeterminada</label>
				<Select v-model="datos.birthday_template" class="w-full" size="small" name="birthday_template" optionValue="id"
					optionLabel="name" filter :options="templates" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Sexo</label>
				<Select v-model="datos.gender_id" class="w-full" size="small" name="gender_id" optionValue="key_name"
					optionLabel="value" filter :options="generos" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">
					Estado civil</label>
				<Select v-model="datos.marital_statu_id" class="w-full" size="small" name="marital_statu_id" optionValue="key_name"
				optionLabel="value" filter :options="maritals" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Nacionalidad</label>
				<Select v-model="datos.nationalitie_id" class="w-full" size="small" name="nationalitie_id" optionValue="id"
					optionLabel="name" filter :options="nationalities" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Idioma preferido</label>
				<Select v-model="datos.preferred_language_id" :invalid="contactStore.errors.preferred_language_id" class="w-full" size="small" name="preferred_language_id" optionValue="id"
					optionLabel="name" filter :options="languages" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Time Frame</label>
				<Select v-model="datos.time_frame_id" class="w-full" size="small" name="time_frame_id"
				 optionValue="key_name"	optionLabel="value" filter :options="time_frames" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Motivation</label>
				<Select v-model="datos.motivation" class="w-full" size="small" name="motivation" 
				optionValue="key_name"	optionLabel="value"
				 filter :options="motivations" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-2">
				<label class="text-base font-normal ml-2 mb-2">Hijos</label>
				<InputText size="small" type="text" v-model="datos.children" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>

			<div class="col-span-12 md:col-span-4 flex items-center mt-5">
				<Checkbox v-model="datos.first_time_buyer" name="first_time_buyer" value="0" size="large" />
				<label class="text-md ml-2">Comprador por primera vez </label>
			</div>

			<div class="col-span-12 md:col-span-3">
				<label class="text-md ml-2">Comisión del comprador </label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<InputNumber v-model="datos.buyer_commission" size="small" class="p-0 m-0 h-9"
						name="buyer_commission" min="0" inputId="locale-us" locale="en-US" :minFrac1tionDigits="2">
					</InputNumber>
					<InputGroupAddon size="small" class="h-9">%
					</InputGroupAddon>
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Tipo Comisión</label>
				<Select v-model="datos.type_buyer_commission" class="w-full" size="small" name="type_buyer_commission"
				optionValue="key_name"	optionLabel="value" filter :options="type_commissions" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>

		</div>

		<div class="grid grid-cols-12 gap-3 items-center mx-2">
			<div class="col-span-12 mt-5 text-lg font-semibold">Comunicación</div>
			<hr class="col-span-12 border-[#0ea5e9] border">
			<div class="col-span-12 md:col-span-4">
				<label class="text-md ml-2">Correo</label>
				<InputGroup size="small"  class="col-span-12 md:col-span-8">
					<InputGroupAddon size="small" class="h-9"><i class="pi pi-envelope"></i></InputGroupAddon>
					<InputText v-model="datos.email" :invalid="contactStore.errors.email" type="email" size="small" class="p-0 m-0 h-9" name="email">
					</InputText>
				</InputGroup>
			</div>
			<div class="col-span-12 md:col-span-4">
				<label class="text-md ml-2">Teléfono Casa</label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<InputGroupAddon size="small" class="h-9"><i class="pi pi-phone"></i></InputGroupAddon>
					<InputText v-model="datos.home_phone" type="text" size="small" class="p-0 m-0 h-9" name="home_phone">
					</InputText>
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-4">
				<label class="text-md ml-2">Celular</label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<InputGroupAddon size="small" class="h-9"><i class="pi pi-mobile"></i></InputGroupAddon>
					<InputText v-model="datos.mobile" type="text" size="small" class="p-0 m-0 h-9" name="mobile">
					</InputText>
				</InputGroup>
			</div>
			<div class="col-span-12 md:col-span-4">
				<label class="text-md ml-2">Fax</label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<InputGroupAddon size="small" class="h-9"><i class="fa-solid fa-fax"></i></InputGroupAddon>
					<InputText v-model="datos.fax" type="text" size="small" class="p-0 m-0 h-9" name="fax">
					</InputText>
				</InputGroup>
			</div>
			<div class="col-span-12 md:col-span-4">
				<label class="text-md ml-2">Otro Teléfono</label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<InputGroupAddon size="small" class="h-9"><i class="pi pi-phone"></i></InputGroupAddon>
					<InputText v-model="datos.other_phone" type="text" size="small" class="p-0 m-0 h-9" name="other_phone">
					</InputText>
				</InputGroup>
			</div>
			<div class="col-span-12 md:col-span-4">
				<label class="text-sm font-normal ml-2 mb-2">Método de Comunicación Preferido</label>
				<Select v-model="datos.preferred_communication_method_id" :invalid="contactStore.errors.preferred_communication_method_id" class="w-full" size="small"
					name="preferred_communication_method_id" optionValue="key_name"	optionLabel="value" filter
					:options="preferred_communication_methods" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
		</div>

		<div class="grid grid-cols-12 gap-3 items-center mx-2">
			<div class="col-span-12 mt-5 text-lg font-semibold">Habilitar chat</div>
			<hr class="col-span-12 border-[#0ea5e9] border">

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-11">Telegram</label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-telegram text-[#24A1DE] fa-xl"></i>
					</div>
					<InputText size="small" type="text" v-model="datos.chat_telegram" class="w-full" />
					<Button rounded as="a" variant="text" href="https://telegram.org/faq#usernames-and-t-me"
						target="_blank" rel="noopener">
						<i class="fa-solid fa-circle-info text-blue-300 fa-xl"></i>
					</Button>

				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-11">Viber</label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-viber text-[#7360f2] fa-xl"></i>
					</div>
					<InputText size="small" type="text" v-model="datos.chat_viber" class="w-full" />
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-11">Facebook Messenger</label>
				<InputGroup size="small" class="col-span-12 md:col-span-8">
					<div class="flex my-auto px-2">
						<i
							class="fa-brands fa-facebook-messenger bg-clip-text text-linear-to-bl from-violet-500 to-fuchsia-500 fa-xl"></i>
					</div>
					<InputText size="small" type="text" v-model="datos.chat_messenger" class="w-full" />
					<Button rounded as="a" variant="text" href="https://www.facebook.com/help/1740158369563165"
						target="_blank" rel="noopener">
						<i class="fa-solid fa-circle-info text-blue-300 fa-xl"></i>
					</Button>
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-11">Whatsapp</label>
				<InputGroup size="small" class="col-span-13 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-whatsapp text-[#25d366] fa-xl"></i>
					</div>
					<InputText size="small" type="text" v-model="datos.chat_whatsapp" class="w-full" />
				</InputGroup>
			</div>

			<hr class="col-span-12 border-[#0ea5e9] my-3 border">

			<div class="col-span-12 md:col-span-3">
				<label class="text-base font-normal ml-2 mb-2">Tipo Dirección</label>
				<Select v-model="datos.address_type_id" class="w-full" size="small" name="address_type_id" optionValue="key_name"	optionLabel="value" filter :options="type_address" placeholder="Seleccionar">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>

			<div class="col-span-12 md:col-span-3">
				<label class="text-base ">Departamento</label>
				<Select v-model="datos.state_id" class="w-full" size="small" name="state_id" optionValue="id"
					optionLabel="name" filter :options="departamentos" placeholder="Seleccionar"
					@value-change="getProvincias()">
				</Select>
				<Message severity="error" size="small" variant="simple"></Message>
			</div>

			<div class="col-span-12 md:col-span-3">
				<label class="text-base">Provincia</label>
				<Select v-model="datos.province_id" class="w-full" optionValue="id" size="small" optionLabel="name"
					filter :options="provincias" placeholder="Seleccionar" @value-change="getCiudades()">
				</Select>
			</div>

			<div class="col-span-12 md:col-span-3">
				<label class="text-base ">Ciudad</label>
				<Select v-model="datos.city_id" class="w-full" size="small" optionValue="id" optionLabel="name" filter
					:options="ciudades" placeholder="Seleccionar" @value-change="getZonas()">
				</Select>
			</div>

			<div class="col-span-12 md:col-span-3" v-if="zonas.length > 0">
				<label class="text-base ">Zona Local</label>
				<Select v-model="datos.zone_id" class="w-full" size="small" optionValue="id" optionLabel="name" filter
					:options="zonas" placeholder="Seleccionar">
				</Select>
			</div>

			<div class="col-span-6 md:col-span-2">
				<label class="text-base">Número</label>
				<InputNumber v-model="datos.number" size="small" inputId="withoutgrouping" :useGrouping="false" fluid />
			</div>
			<div class="col-span-6 md:col-span-2">
				<label class="text-base">Depto./Unidad</label>
				<InputNumber v-model="datos.unit" size="small" inputId="withoutgrouping" :useGrouping="false" fluid />
			</div>
			<div class="col-span-6 md:col-span-2">
				<label class="text-base">Código postal</label>
				<InputNumber v-model="datos.zip_code" size="small" inputId="withoutgrouping" :useGrouping="false"
					fluid />
			</div>
			<div class="col-span-6 md:col-span-2">
				<label class="text-base"># Plantas</label>
				<InputNumber v-model="datos.floor_number" size="small" inputId="withoutgrouping" :useGrouping="false"
					fluid />
			</div>
			<div class="col-span-6 md:col-span-4">
				<label class="text-base">Pisos</label>
				<Select v-model="datos.floor_types" class="w-full" size="small" name="floor_types" optionValue="key_name"	optionLabel="value" filter :options="floors" placeholder="Seleccionar">
				</Select>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-base font-normal ml-2 mb-2">Dirección </label>
				<InputText size="small" type="text" v-model="datos.address" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>
			<div class="col-span-12 md:col-span-6">
				<label class="text-base font-normal ml-2 mb-2">Dirección 2 </label>
				<InputText size="small" type="text" v-model="datos.address2" class="w-full" />
				<Message severity="error" size="small" variant="simple"></Message>
			</div>

		</div>

		<div class="grid grid-cols-12 gap-3 items-center mx-2">
			<div class="col-span-12 mt-5 text-lg font-semibold">Redes Sociales</div>
			<hr class="col-span-12 border-[#0ea5e9] border">

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-14">Facebook</label>
				<InputGroup size="small" class="col-span-13 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-facebook text-[#3b5998] fa-2xl"></i>
					</div>
					<InputText size="small" type="text" v-model="datos.red_facebook" class="w-full" />
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-14">Twitter</label>
				<InputGroup size="small" class="col-span-13 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-square-x-twitter text-[#000000] fa-2xl"></i>
					</div>
					<InputText size="small" type="text" v-model="datos.red_twitter" class="w-full" />
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-14">Youtube</label>
				<InputGroup size="small" class="col-span-13 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-youtube text-[#c4302b] fa-2xl "></i>
					</div>
					<InputText size="small" type="text" v-model="datos.red_youtube" class="w-full" />
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-14">Linkedin</label>
				<InputGroup size="small" class="col-span-13 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-linkedin text-[#0e76a8] fa-2xl "></i>
					</div>
					<InputText size="small" type="text" v-model="datos.red_linkedin" class="w-full" />
				</InputGroup>
			</div>

			<div class="col-span-12 md:col-span-6">
				<label class="text-md ml-14">Instagram</label>
				<InputGroup size="small" class="col-span-13 md:col-span-8">
					<div class="flex my-auto px-2">
						<i class="fa-brands fa-square-instagram text-[#C13584] fa-2xl "></i>
					</div>
					<InputText size="small" type="text" v-model="datos.red_instagram" class="w-full" />
				</InputGroup>
			</div>
		</div>
		{{ datos}}
	</div>

</template>

<script setup>
import { onMounted, ref } from "vue";
import { storeToRefs } from "pinia";
import { useContactStore } from "@/Stores/useContactStore";
import { useAppStore } from "@/Stores/useAppStore";
import apiClient from "@/src/constansts/axiosClient";
import moment from 'moment';

const storeApp = useAppStore();
const contactStore = useContactStore();
const { datos } = storeToRefs(contactStore);
const { lang } = storeApp;

let departamentos = ref([])
let agentes = ref([])
let provincias = ref([])
let ciudades = ref([])
let zonas = ref([])
let generos = ref([])
let titulos = ref([])
let recordatorios = ref([])
let maritals = ref([])
let time_frames = ref([])
let motivations = ref([])
let type_commissions = ref([])
let floors = ref([])
let preferred_communication_methods = ref([])
let type_address = ref([])
let languages = ref([])
let nationalities = ref([])
let templates = ref([])


async function getAgentes() {
	const data = {
		// office_id :
	};
	const response = await apiClient.get("/options/get-agents");
	if (response.status == 200) {
		agentes.value = response.data;
	}
}
async function getDepartamentos() {
	const data = {
		// office_id :
	};
	const response = await apiClient.get("/options/get-departamentos");
	if (response.status == 200) {
		departamentos.value = response.data;
	}
}
async function getProvincias() {
	provincias.value = []
	ciudades.value = []
	zonas.value = []
	const data = {
		'state_id': [contactStore.datos.state_id]
	};
	const response = await apiClient.post('/options/get-provincias', data);
	provincias.value = response.data;
}

async function getSelects(key,valor) {
	valor.valor=[]
	const data = {
		'code':lang,
		'category':key
	};
	const response = await apiClient.post('/options/get-select', data);
	
	valor.value = response.data;
}


async function getCiudades() {
	ciudades.value = []
	const data = {
		'province_id': [contactStore.datos.province_id]
	};
	const response = await apiClient.post('/options/get-ciudades', data);
	ciudades.value = response.data;
}

async function getZonas() {
	zonas.value = []
	const data = {
		'city_id': [contactStore.datos.city_id]
	};
	const response = await apiClient.post('/options/get-zonas', data);
	zonas.value = response.data;
}

const setDateI=(date)=> {
	datos.birth_day = moment(date).format('YYYY-MM-DD');
}

onMounted(() => {
	getDepartamentos()
	getAgentes()
	getSelects('Gender',generos)
	getSelects('Contact.Title',titulos)
	getSelects('Birthday Reminder',recordatorios)
	getSelects('Contact.MaritalStatus',maritals)
	getSelects('Contact.TimeframeForTransaction',time_frames)
	getSelects('Contact.MotivationForTransaction',motivations)
	getSelects('CommissionType',type_commissions)
	getSelects('Listing.FloorType',floors)
	getSelects('Contact.PreferredCommMethod',preferred_communication_methods)
	getSelects('AddressType',type_address)


})
</script>
