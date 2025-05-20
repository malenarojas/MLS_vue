<script setup>
import OwnerHeader from "./OwnerHeader.vue";
import { useNumberHelpers } from "@/Composables/useNumberHelpers";

defineProps({
    listing: {
        type: Object,
        required: true,
    },
});

const { currencyFormat } = useNumberHelpers();

const statusColors = {
    1: "bg-gray-500",
    2: "bg-green-600",
    3: "bg-yellow-600",
    4: "bg-orange-500",
    5: "bg-indigo-500",
    6: "bg-red-600",
    7: "bg-emerald-600",
    8: "bg-teal-600",
    9: "bg-blue-500",
    10: "bg-rose-600",
};

const getStatusColor = (id) => {
    return statusColors[id] || "bg-gray-700"; // fallback
};
</script>

<template>
    <section
        class="flex flex-wrap justify-center md:justify-between items-center md:items-start gap-6 bg-white p-6 rounded-lg shadow-md mb-5"
    >
        <!-- Imagen y estado -->
        <div class="relative w-56">
            <img
                :src="
                    listing?.multimedias[0]?.url ?? '/listing-default-logo.gif'
                "
                alt="Imagen de la propiedad"
                class="w-full h-48 object-cover rounded-md border-4 border-blue-100"
            />
            <span
                class="absolute top-0 left-0 bg-blue-900 text-white px-2 py-1 text-sm rounded-br-md"
            >
                <!-- {{ listing?.price?.amount
                }}<span class="ml-1">{{
                    listing?.price?.currency?.symbol
                }}</span> -->
                {{
                    currencyFormat(
                        parseFloat(listing?.price?.amount ?? 0).toFixed(2),
                        {
                            style: "decimal",
                            currency: "BOB",
                            minimumFractionDigits: 2,
                        }
                    )
                }}
                <span>{{ listing?.price?.currency?.symbol }}</span>
            </span>
            <!-- <button
                :class="`w-full mt-2 ${getStatusColor(
                    listing?.status_listing_id
                )} text-white py-1 rounded text-sm hover:opacity-90 transition `"
            >
                {{ listing?.status_listing?.name }}
            </button> -->
            <span
                :class="`w-full inline-block mt-2 text-white text-center font-semibold px-3 py-1 rounded-2xl tracking-wider ${getStatusColor(
                    listing?.status_listing_id
                )}`"
            >
                {{ listing?.status_listing?.name }}
            </span>
        </div>

        <!-- <pre>{{ listing.price }}</pre> -->

        <!-- Info principal -->
        <div class="flex-1 max-w-2xl">
            <h4 class="font-semibold text-lg text-gray-800 mb-1">
                {{ listing?.location?.full_address }}
            </h4>
            <div class="flex flex-wrap gap-2 mb-4">
                <span
                    class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full"
                    >Residencial</span
                >
                <span
                    class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full"
                    >{{ listing?.transaction_type?.name ?? "Venta" }}</span
                >
                <span
                    class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full"
                >
                    {{
                        listing?.listing_information?.subtype_property?.name ??
                        "Casa"
                    }}
                </span>
            </div>

            <div class="text-sm text-gray-700 space-y-1">
                <div class="flex justify-between">
                    <p>Total de habitaciones</p>
                    <p>
                        {{
                            listing?.listing_information?.total_number_rooms ??
                            0
                        }}
                    </p>
                </div>
                <div class="flex justify-between">
                    <p>Dormitorios</p>
                    <p>
                        {{ listing?.listing_information?.number_bedrooms ?? 0 }}
                    </p>
                </div>
                <div class="flex justify-between">
                    <p>Baños</p>
                    <p>
                        {{
                            listing?.listing_information?.number_bathrooms ?? 0
                        }}
                    </p>
                </div>
                <div class="flex justify-between">
                    <p>Tot.m² = Terr + Const</p>
                    <p>{{ listing?.listing_information?.total_area ?? 0 }}</p>
                </div>
                <div class="flex justify-between">
                    <p>Año construcción</p>
                    <p>
                        {{
                            listing?.listing_information?.year_construction
                                ? new Date(
                                      listing.listing_information.year_construction
                                  ).getFullYear()
                                : 2004
                        }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Agente + stats -->
        <div class="flex flex-col items-center w-72">
            <OwnerHeader
                v-if="listing?.owners?.length > 0"
                :user="listing?.owners[0]"
            />

            <!-- Métricas de visitas -->
            <div class="grid grid-cols-3 gap-2 my-4 text-center w-full">
                <div class="bg-red-100 text-red-700 p-2 rounded">
                    <p class="text-lg font-bold">0</p>
                    <p class="text-xs">Vistas</p>
                </div>
                <div class="bg-blue-100 text-blue-700 p-2 rounded">
                    <p class="text-lg font-bold">0</p>
                    <p class="text-xs">Prospectos</p>
                </div>
                <div class="bg-cyan-100 text-cyan-700 p-2 rounded">
                    <p class="text-lg font-bold">0%</p>
                    <p class="text-xs">Tasa de conversión</p>
                </div>
            </div>

            <!-- Info extra -->
            <div class="text-xs text-gray-600 text-center mb-4">
                <p>
                    Días en el mercado:
                    <strong>{{ listing.days_in_market }}</strong>
                </p>
                <p>Compradores potenciales: <strong>0</strong></p>
            </div>
        </div>

        <!-- Calidad de captación -->
        <div class="mt-2 flex flex-col items-center">
            <p class="text-sm text-gray-700 mb-1">Calidad de captación</p>
            <div class="relative w-24 h-24">
                <svg
                    class="w-full h-full transform -rotate-90"
                    viewBox="0 0 36 36"
                >
                    <path
                        class="text-gray-200"
                        stroke-width="3"
                        stroke="currentColor"
                        fill="none"
                        d="M18 2.0845
                               a 15.9155 15.9155 0 0 1 0 31.831
                               a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <path
                        class="text-orange-500"
                        stroke-width="3"
                        stroke-dasharray="0, 100"
                        stroke-linecap="round"
                        stroke="currentColor"
                        fill="none"
                        d="M18 2.0845
                               a 15.9155 15.9155 0 0 1 0 31.831
                               a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                </svg>
                <div
                    class="absolute inset-0 flex items-center justify-center text-orange-600 font-bold text-lg"
                >
                    0
                </div>
            </div>
            <button
                class="mt-2 text-sm border px-3 py-1 rounded hover:bg-gray-100"
            >
                Mejorar calidad
            </button>
        </div>
    </section>
</template>
