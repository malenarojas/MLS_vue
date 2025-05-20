<script setup>
import { computed, inject, onMounted, ref } from "vue";
import RadioButtonGroup from "@/Components/Common/RadioButtonGroup.vue";
import CheckboxGroup from "@/Components/Common/CheckboxGroup.vue";
import SelectInput from "@/Components/Common/SelectInput.vue";
import DateInput from "@/Components/Common/DateInput.vue";
import NumberInput from "@/Components/Common/NumberInput.vue";
import TextInput from "@/Components/Common/TextInput.vue";
import { TYPES_COMMISSION } from "@/Constants/constants";
import FeatureCheckboxGroup from "./FeatureCheckboxGroup.vue";
import Rooms from "./Rooms.vue";
import { useLeafletMap } from "@/Composables/useLeafletMap";
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue";
import debounce from "lodash/debounce";
import OwnerFormRow from "@/Components/Common/OwnerFormRow.vue";

const saleSignOptions = [
    { id: 1, name: "Si" }, // 1 = Si
    { id: 0, name: "No" }, // 0 = No
];
const toast = useToast();
const page = usePage();
const props = computed(() => page.props);
const permissions = props.value.permissions ?? [];
const showStatu = permissions.includes("listing.show_status");
const contactSearch = ref("");
const isAddingOwner = ref(false);

const form = inject("form", { errors: {} });

const { initializeMap, addMarker, moveMarker, moveCamera, onMarkerEvent } =
    useLeafletMap();

const mapElement = ref(null);

const updateLocation = () => {
    console.log(
        `Departamento ${form.location.state_id} ${form.location.province_id} ${form.location.city_id}`
    );
    router.visit(route("listings.edit", { key: form.key }), {
        method: "get",
        data: {
            state_id: form.location.state_id,
            province_id: form.location.province_id,
            city_id: form.location.city_id,
        },
        only: ["provinces", "cities", "zones"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        preserveUrl: true,
    });
};

const updateCity = (cityId) => {
    if (!cityId) return;

    const city = props.value?.cities?.find((city) => city.id === cityId);

    if (!city) return;

    const { latitude = null, longitude = null, id } = city;

    console.log(`city ${city?.latitude} ${city?.longitude}`);

    moveMarker(
        latitude ?? -17.782812972969907,
        longitude ?? -63.180772028878245,
        15
    );

    form.location.zone_id = null;
};

const updateZone = (zoneId) => {
    if (!zoneId) return;

    const zone = props.value?.zones?.find((zone) => zone.id === zoneId);
    if (!zone) return;

    console.log(`zone ${zone?.latitude} ${zone?.longitude}`);

    const { latitude = null, longitude = null, id } = zone;

    moveMarker(
        latitude ?? -17.782812972969907,
        longitude ?? -63.180772028878245,
        15
    );
};

const getContacts = () => {
    if (!contactSearch.value || contactSearch.value.length < 3) return;

    router.visit(route("listings.edit", { key: form.key }), {
        method: "get",
        data: {
            contact_search: contactSearch.value,
        },
        only: ["contacts"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        preserveUrl: true,
    });
};

const debounceGetContacts = debounce(getContacts, 700);

const calculateLandM2 = () => {
    if (form.listing_information.land_x && form.listing_information.land_y) {
        form.listing_information.land_m2 =
            form.listing_information.land_x * form.listing_information.land_y;

        calculateTotalArea();
    }
};

const calculateTotalArea = () => {
    if (
        form.listing_information.land_m2 ||
        form.listing_information.construction_area_m
    ) {
        form.listing_information.total_area =
            form.listing_information.land_m2 +
            form.listing_information.construction_area_m;
    }
};

const addNewOwner = (owner) => {
    if (!form.owners.find((o) => o.id === owner.id)) {
        form.owners.push(owner);
        contactSearch.value = "";
    }
};

const saveOwner = (newOwner) => {
    newOwner.isNew = true;
    form.owners.push(newOwner);
    isAddingOwner.value = false;
};

const removeOwner = (ownerId) => {
    const index = form.owners.findIndex((o) => o.id === ownerId);
    if (index !== -1) {
        form.owners.splice(index, 1);
    }
};

const getClipboardCoordinates = async () => {
    try {
        const text = await navigator.clipboard.readText();
        // Separar por coma o espacio
        const parts = text
            .replaceAll(",", " ")
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        if (parts.length >= 2) {
            const lat = parseFloat(parts[0].replace(",", "."));
            const lng = parseFloat(parts[1].replace(",", "."));

            // Validar que sean coordenadas válidas
            if (
                !isNaN(lat) &&
                lat >= -90 &&
                lat <= 90 &&
                !isNaN(lng) &&
                lng >= -180 &&
                lng <= 180
            ) {
                const latitude = parseFloat(lat.toFixed(8));
                const longitude = parseFloat(lng.toFixed(8));
                form.location.latitude = latitude;
                form.location.longitude = longitude;
                moveMarker(latitude, longitude, 15.5);
            } else {
                toast.add({
                    severity: "warn",
                    summary: "Error",
                    detail: "Texto del portapapeles no contiene coordenadas válidas.",
                    life: 5000,
                });
            }
        } else {
            toast.add({
                severity: "warn",
                summary: "Error",
                detail: "Texto del portapapeles no contiene coordenadas válidas.",
                life: 5000,
            });
        }
    } catch (err) {
        console.error("Error al leer el portapapeles:", err);
        toast.add({
            severity: "warn",
            summary: "Error",
            detail: "No se pudo leer el portapapeles.",
            life: 5000,
        });
    }
};

const getCoordinate = () => {
    if (
        props.value?.listing?.location?.latitude &&
        props.value?.listing?.location?.longitude
    ) {
        const latitude = props.value?.listing.location.latitude;
        const longitude = props.value?.listing.location.longitude;
        form.location.latitude = latitude;
        form.location.longitude = longitude;
        moveMarker(latitude, longitude, 15.5);
    } else {
        toast.add({
            severity: "warn",
            summary: "Error",
            title: "Error al obtener coordenadas",
            detail: "No se encontraron coordenadas del listing (Debe guardar primero).",
            life: 5000,
        });
    }
};

const updateMarket = () => {
    console.log("updateMarket");
    const latitude = form.location.latitude;
    const longitude = form.location.longitude;
    console.log(latitude, longitude);

    if (!latitude || !longitude) return;

    moveMarker(latitude, longitude, 15.5);
};

const debouncedUpdateMarket = debounce(updateMarket, 300);

onMounted(() => {
    initializeMap(mapElement.value, { zoom: 15.5 });
    const coordinates = {
        latitude: -17.782812972969907,
        longitude: -63.180772028878245,
    };

    if (form.location?.latitude && form.location?.longitude) {
        const { latitude, longitude } = form.location;
        addMarker(latitude, longitude);
        moveCamera(latitude, longitude, 15.5);
    } else {
        addMarker(coordinates.latitude, coordinates.longitude);
        moveCamera(coordinates.latitude, coordinates.longitude, 13.5);
        form.location.latitude = coordinates.latitude;
        form.location.longitude = coordinates.longitude;
    }

    onMarkerEvent("dragend", (event) => {
        const { lat, lng } = event.target.getLatLng();
        moveCamera(lat, lng, 15.5);

        form.location.latitude = lat;
        form.location.longitude = lng;
    });
});
</script>

<template>
    <section class="form-section justify-between">
        <RadioButtonGroup
            v-model="form.transaction_type_id"
            idPrefix="tran_type"
            :options="props.transaction_type"
            :error="form.errors?.transaction_type_id"
        />

        <RadioButtonGroup
            v-model="form.area_id"
            idPrefix="area"
            :options="props.areas"
            :error="form.errors?.area_id"
        />

        <CheckboxGroup
            v-model="form.is_published"
            binary
            binaryLabel="Esconder captación de la página pública"
            :error="form.errors?.is_published"
        />
    </section>

    <!-- <pre>{{ form }}</pre> -->

    <section class="section-row">
        <SelectInput
            v-model="form.status_listing_id"
            :disabled="!showStatu"
            label="Estado"
            :options="props.available_status"
            :error="form.errors?.status_listing_id"
            required
        />

        <SelectInput
            v-if="form.status_listing_id === 6"
            v-model="form.cancellation_reason_id"
            :disabled="!showStatu"
            label="Razón de cancelación"
            :options="props.cancellation_reasons"
            :error="form.errors?.cancellation_reason_id"
            required
        />

        <SelectInput
            v-model="form.contract_type_id"
            label="Tipo de contrato"
            :options="props.contract_types"
            required
        />

        <DateInput
            v-model="form.date_of_listing"
            label="Fecha captación"
            :error="form.errors?.date_of_listing"
        />

        <DateInput
            v-model="form.contract_end_date"
            label="Fecha finalización contrato"
            :error="form.errors?.contract_end_date"
        />
    </section>

    <!-- <pre>{{ props?.listing.price }}</pre> -->

    <section class="mb-5">
        <h3 class="section-title">Información precio</h3>

        <!-- <pre>{{ form.price }}</pre> -->

        <div class="section-row">
            <NumberInput
                v-model="form.price.amount"
                label="Precio"
                :error="form.errors?.price?.amount"
                required
                v-bind="{ maxFractionDigits: 2, suffix: ' Bs' }"
            />

            <SelectInput
                v-model="form.price_type_id"
                label="Tipo de precio"
                :options="props.price_types"
            />

            <SelectInput
                v-if="form.transaction_type_id === 2"
                v-model="form.rent_timeframe_id"
                label="Tiempo de alquiler"
                :options="props.rent_timeframes"
            />
        </div>
    </section>

    <section class="mb-5">
        <h3 class="section-title">Comisiones</h3>

        <!-- <pre>{{ form.commission_option }}</pre> -->

        <div class="form-section">
            <div
                class="w-full sm:w-auto flex flex-col sm:flex-row gap-5 items-center"
            >
                <NumberInput
                    v-model="form.commission_option.recruitment_commission"
                    label="Comisión de captación"
                    :error="
                        form.errors['commission_option.recruitment_commission']
                    "
                    v-bind="{ maxFractionDigits: 2 }"
                    required
                />

                <RadioButtonGroup
                    v-model="form.commission_option.type_recruitment_commission"
                    :options="TYPES_COMMISSION"
                    optionValue="value"
                    optionLabel="name"
                    idPrefix="recruitment"
                    :error="
                        form.errors?.[
                            'commission_option.type_recruitment_commission'
                        ]
                    "
                />
            </div>
            <div
                class="w-full sm:w-auto flex flex-col sm:flex-row gap-5 items-center"
            >
                <NumberInput
                    v-model="form.commission_option.sales_commission"
                    label="Comisión de venta"
                    :error="form.errors?.['commission_option.sales_commission']"
                    v-bind="{ maxFractionDigits: 2 }"
                />

                <RadioButtonGroup
                    v-model="form.commission_option.sales_commission_type"
                    :options="TYPES_COMMISSION"
                    optionValue="value"
                    optionLabel="name"
                    idPrefix="sales"
                    :error="
                        form.errors?.['commission_option.sales_commission_type']
                    "
                />
            </div>
        </div>
    </section>

    <!-- <pre>{{ form.property_number }}</pre> -->

    <!-- <section>
        <h3 class="section-title">Financiero</h3>

        <div class="section-row">
            <TextInput
                v-model="form.reference"
                label="Referencia"
                :error="form.errors?.reference"
            />

            <TextInput
                v-model="form.property_number"
                label="Número de registro
            de la propiedad"
                :error="form.errors?.property_number"
            />

            <TextInput
                v-model="form.financial_note"
                label="Notas financieras"
            />
        </div>
    </section> -->

    <!-- <pre>{{ form.listing_information.market_status_id }}</pre> -->
    <!-- <pre>{{ props.market_statuses }}</pre> -->

    <section class="mb-2">
        <h3 class="section-title">Información Propiedad</h3>

        <div class="section-row">
            <SelectInput
                v-model="form.listing_information.subtype_property_id"
                label="Subtipo de propiedad"
                :options="props.subtype_properties"
                :error="form.errors?.listing_information?.subtype_property_id"
                required
            />

            <SelectInput
                v-model="form.listing_information.market_status_id"
                label="Estado de mercado"
                :options="props.market_statuses"
                :error="form.errors?.listing_information?.market_status_id"
                optionValue="id"
                optionLabel="name_market_status"
            />

            <DateInput
                v-model="form.listing_information.available_date"
                label="Fecha de disponibilidad"
                :error="form.errors?.listing_information?.available_date"
            />

            <DateInput
                v-model="form.listing_information.year_construction"
                label="Fecha de construcción"
                format="yy"
                v-bind="{ view: 'year' }"
                :error="form.errors?.listing_information?.year_construction"
                required
            />
        </div>

        <div class="section-row">
            <SelectInput
                v-model="form.listing_information.state_property_id"
                label="Estado de la propiedad"
                :options="props.state_properties"
                :error="form.errors?.listing_information?.state_property_id"
                optionLabel="name_state_properties"
                required
            />

            <SelectInput
                v-model="form.listing_information.property_category_id"
                label="Categoría de propiedad"
                :options="props.property_categories"
                :error="form.errors?.listing_information?.property_category_id"
                optionLabel="name_properties_categories"
            />
        </div>

        <div class="section-row">
            <NumberInput
                v-model="form.listing_information.parking_slots"
                label="Espacio de paqueos"
                :error="form.errors?.listing_information?.parking_slots"
            />

            <NumberInput
                v-model="form.listing_information.plant_numbers"
                label="#Plantas"
                :error="form.errors?.listing_information?.plant_numbers"
            />

            <NumberInput
                v-model="form.listing_information.number_departments"
                label="#Deptos. en el edificio"
                :error="form.errors?.listing_information?.number_departments"
            />

            <!-- <pre>{{ form.listing_information.sale_sign }}</pre> -->

            <RadioButtonGroup
                v-model="form.listing_information.sale_sign"
                :options="saleSignOptions"
                id-prefix="saleSing"
                label="Letrero de venta"
            />

            <!-- <SelectInput
                v-model="form.listing_information.land_category_id"
                label="Categoría de terreno"
                :options="props.land_categories"
                :error="form.errors?.listing_information?.land_category_id"
                optionLabel="land_category_name"
            /> -->
        </div>

        <section class="section-row">
            <NumberInput
                v-model="form.listing_information.cubic_volume"
                label="Volumen cúbico"
                :error="form.errors?.listing_information?.land_area"
                placeholder="0 m³"
                v-bind="{ min: 0, suffix: ' m³', maxFractionDigits: '5' }"
            />

            <!-- <div class="col-span-2">
                <label for="land_area" class="font-bold">Terreno (m x m)</label>
                <div class="flex flex-wrap items-end gap-2" id="land_area">
                    <NumberInput
                        v-model="form.listing_information.land_x"
                        placeholder="Ancho"
                        v-bind="{
                            min: 0,
                            suffix: ' m',
                            maxFractionDigits: '5',
                        }"
                        :error="form.errors?.listing_information?.land_x"
                        @update:model-value="calculateLandM2"
                    />
                    <NumberInput
                        v-model="form.listing_information.land_y"
                        placeholder="Largo"
                        v-bind="{
                            min: 0,
                            suffix: ' m',
                            maxFractionDigits: '5',
                        }"
                        :error="form.errors?.listing_information?.land_y"
                        @update:model-value="calculateLandM2"
                    />
                </div>
            </div> -->

            <NumberInput
                v-model="form.listing_information.land_m2"
                label="Terreno"
                :error="form.errors?.listing_information?.land_m2"
                placeholder="0 m²"
                v-bind="{ min: 0, suffix: ' m²', maxFractionDigits: '5' }"
                @update:model-value="calculateTotalArea"
            />

            <NumberInput
                v-model="form.listing_information.construction_area_m"
                label="Area Construcción (m²)"
                :error="form.errors?.listing_information?.construction_area_m"
                placeholder="0 m²"
                v-bind="{ min: 0, suffix: ' m²', maxFractionDigits: '5' }"
                @update:model-value="calculateTotalArea"
                required
            />

            <NumberInput
                v-model="form.listing_information.total_area"
                label="Tot.m²=Terr+Cons"
                :error="form.errors?.listing_information?.total_area"
                placeholder="Total m²"
                v-bind="{
                    min: 0,
                    suffix: ' m²',
                    maxFractionDigits: '5',
                    readonly: true,
                }"
                required
            />
        </section>
    </section>

    <!-- <pre>{{ form.listing_information.land_x }}</pre>
    <pre>{{ form.listing_information.land_y }}</pre>
    <pre>{{ form.listing_information.land_m2 }}</pre> -->

    <!-- <pre>{{ form.features }}</pre> -->
    <!-- <pre>{{ form.errors }}</pre> -->

    <section class="mb-5 mt-2">
        <h3 class="section-title">Características</h3>

        <FeatureCheckboxGroup
            v-if="props.features"
            v-model="form.features"
            :features="props.features"
            :error="form.errors?.features"
        />
    </section>

    <!-- <pre>{{ form.rooms }}</pre> -->

    <section>
        <h3 class="section-title">Habitaciones</h3>

        <div class="section-row">
            <NumberInput
                v-model="form.listing_information.total_number_rooms"
                label="#Total de habitaciones"
                :error="form.errors?.listing_information?.total_number_rooms"
            />

            <NumberInput
                v-model="form.listing_information.number_bedrooms"
                label="#Dormitorios"
                :error="form.errors?.listing_information?.number_bedrooms"
                required
            />

            <NumberInput
                v-model="form.listing_information.number_bathrooms"
                label="Total baños"
                :error="form.errors?.listing_information?.number_bathrooms"
                required
            />

            <NumberInput
                v-model="form.listing_information.number_toiletrooms"
                label="Medio baños"
                :error="form.errors?.listing_information?.number_toiletrooms"
            />
        </div>

        <!-- <pre>{{ form.rooms }}</pre> -->
        <Rooms :room-types="props.room_types" />
    </section>

    <!-- <pre>{{ form.location }}</pre> -->

    <section class="mb-15 md:mb-5">
        <h3 class="section-title">Ubicación</h3>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="col-span-1 sm:col-span-2">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-5 justify-center items-center"
                >
                    <SelectInput
                        v-model="form.location.state_id"
                        label="Departamento"
                        :options="props.states"
                        @update:modelValue="
                            (value) => {
                                form.location.province_id = null;
                                form.location.city_id = null;
                                form.location.zone_id = null;
                                updateLocation(value);
                            }
                        "
                        required
                    />

                    <SelectInput
                        v-model="form.location.province_id"
                        label="Provincia"
                        :options="props.provinces"
                        @update:modelValue="
                            (value) => {
                                form.location.city_id = null;
                                form.location.zone_id = null;
                                updateLocation(value);
                            }
                        "
                        required
                    />

                    <SelectInput
                        v-model="form.location.city_id"
                        label="Ciudad"
                        :options="props.cities"
                        @update:modelValue="
                            (value) => {
                                updateCity(value);
                                updateLocation();
                            }
                        "
                        required
                    />

                    <SelectInput
                        v-if="props.zones?.length > 0"
                        v-model="form.location.zone_id"
                        label="Zona"
                        :options="props.zones"
                        :error="form.errors?.location?.zone_id"
                        @update:modelValue="
                            (value) => {
                                updateZone(value);
                                updateLocation();
                            }
                        "
                    />

                    <TextInput v-model="form.location.number" label="#Número" />

                    <TextInput
                        v-model="form.location.unit_department"
                        label="Depto/Unidad"
                    />

                    <TextInput
                        v-model="form.location.first_address"
                        label="Dirección"
                    />

                    <TextInput
                        v-model="form.location.second_address"
                        label="Dirección 2"
                    />

                    <TextInput
                        v-model="form.location.zip_code"
                        label="Código postal"
                    />

                    <SelectInput
                        v-model="form.location.type_floor_id"
                        label="Tipo de piso"
                        :options="props.type_floors"
                        optionValue="id"
                        optionLabel="name"
                    />

                    <TextInput
                        v-model="form.location.district"
                        label="Distrito"
                    />

                    <TextInput
                        v-model="form.location.access_number"
                        label="Número de Acceso"
                    />

                    <CheckboxGroup
                        v-model="form.location.show_addres_on_website"
                        binary
                        binaryLabel="Ubicación visible en la web"
                        :error="form.errors?.location?.show_addres_on_website"
                    />
                </div>
            </div>
            <div class="h-auto col-span-1 md:col-span-3">
                <h4 class="block md:hidden text-base font-medium mb-2">
                    Marcar coordenadas
                </h4>
                <div ref="mapElement" class="w-full h-96 z-0"></div>
                <div class="flex flex-wrap items-end gap-2 mt-2">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <NumberInput
                            v-model="form.location.latitude"
                            label="Latitud"
                            :error="form.errors?.location?.latitude"
                            v-bind="{
                                locale: 'en-US',
                                maxFractionDigits: 8,
                                min: -90,
                                max: 90,
                            }"
                            @input="debouncedUpdateMarket"
                            required
                        />

                        <NumberInput
                            v-model="form.location.longitude"
                            label="Longitud"
                            :error="form.errors?.location?.longitude"
                            v-bind="{
                                locale: 'en-US',
                                maxFractionDigits: 8,
                                min: -180,
                                max: 180,
                            }"
                            @input="debouncedUpdateMarket"
                            required
                        />
                    </div>

                    <Button
                        type="button"
                        label="Pegar coordenadas"
                        size="small"
                        icon="pi pi-clipboard"
                        class="h-10 py-2"
                        @click="getClipboardCoordinates"
                    />

                    <!-- <NumberInput -->
                    <Button
                        type="button"
                        label="Obtener coordenadas"
                        size="small"
                        icon="pi pi-map-marker"
                        class="h-10 py-2"
                        @click="getCoordinate"
                    />
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5 p-4">
        <h3 class="text-lg font-semibold mb-3 border-b pb-2">
            Información del Propietario
            <span class="text-red-500 cursor-help" title="Campo obligatorio"
                >*</span
            >
        </h3>

        <div class="flex items-center gap-3 mb-3">
            <div class="flex-grow pl-1 bg-red-500 rounded-md">
                <InputText
                    v-model="contactSearch"
                    id="search_owner"
                    placeholder="Busque contactos por nombre, apellido o información de contacto."
                    size="small"
                    @input="debounceGetContacts"
                    class="text-sm p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                />
            </div>

            <Button
                type="button"
                label="Propietario"
                icon="pi pi-user-plus"
                size="small"
                class="p-button-secondary"
                @click="isAddingOwner = true"
            />
        </div>

        <ul
            v-if="contactSearch.length > 2 && props.contacts"
            class="absolute z-10 bg-white border border-gray-300 rounded-md shadow-md mt-1 max-h-60 overflow-y-auto w-full"
        >
            <li
                v-for="result in props.contacts"
                :key="result.id"
                class="p-2 hover:bg-gray-100 cursor-pointer"
                @click="addNewOwner(result)"
            >
                {{ result.name }} {{ result.last_name }}
                <span>
                    {{ result.mobile ? `- ${result.mobile}` : "" }}
                </span>
                <span> {{ result.email ? `- ${result.email}` : "" }} </span>
            </li>
        </ul>

        <div class="flex flex-col gap-2 w-full mt-3">
            <OwnerFormRow
                v-for="owner in form.owners"
                :key="owner.id"
                :owner="owner"
                @remove="removeOwner"
            />

            <OwnerFormRow
                v-if="isAddingOwner"
                is-new
                @save="saveOwner"
                @cancel="isAddingOwner = false"
            />
        </div>
    </section>
</template>

<style scoped>
.section-title {
    @apply text-lg font-semibold border-b-2 border-blue-500 py-2 mb-5;
}

.field-label {
    @apply font-medium;
}

.section-row {
    @apply grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-3 items-center;
}

.form-section {
    @apply flex flex-wrap items-center gap-5 my-5 py-5 border-b-2;
}

.form-row {
    @apply grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-3 items-center;
}

.field-container {
    @apply w-full sm:w-auto;
}

.field {
    @apply flex flex-col justify-between gap-1 h-full;
}

/* .field-container .field input,
.field-container .field select {
    @apply w-full;
} */

.radio-button-container {
    @apply flex gap-2 items-center;
}
</style>
