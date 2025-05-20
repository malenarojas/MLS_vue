<script setup>
import { computed, ref } from "vue";
// import Checkbox from "primevue/checkbox";

const props = defineProps({
    features: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: Array,
        required: true,
    },
    error: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["update:modelValue"]);
const showAll = ref(false);

// Último índice
const lastIndex = computed(() => props.features?.length - 1);

// Computed para manejar los valores seleccionados
const selectedFeatures = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

// Función para manejar la selección de una subcaracterística
const toggleFeature = (featureId) => {
    const index = selectedFeatures.value.indexOf(featureId);
    if (index === -1) {
        selectedFeatures.value.push(featureId);
    } else {
        selectedFeatures.value.splice(index, 1);
    }
};

// Computed para mostrar solo la primera y última característica o todas si está expandido
const filteredFeatures = computed(() => {
    if (showAll.value) return props.features;
    return props?.features?.filter(
        (_, index) => index === 0 || index === lastIndex?.value
    );
});
</script>

<template>
    <div class="flex flex-col gap-4">
        <div v-for="feature in filteredFeatures" :key="feature.id">
            <label class="font-semibold block mb-2">
                {{ feature.name }}
            </label>

            <!-- Grid con 4 columnas en pantallas grandes, 2 en medianas y 1 en móviles -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
            >
                <div
                    v-for="child in feature.children"
                    :key="child.id"
                    class="flex items-center gap-2"
                >
                    <Checkbox
                        :inputId="`feature-${child.id}`"
                        :value="child.id"
                        v-model="selectedFeatures"
                        @change="toggleFeature(child.id)"
                    />
                    <label :for="`feature-${child.id}`" class="text-sm">
                        {{ child.name }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Botón para mostrar más -->
        <div class="mx-auto">
            <button
                v-if="props?.features?.length > 2"
                type="button"
                @click="showAll = !showAll"
                class="text-blue-500 text-sm mt-2 hover:underline flex items-center gap-1"
            >
                {{ showAll ? "Mostrar menos..." : "Mostrar más..." }}
                <span :class="showAll ? 'rotate-180' : ''">▼</span>
            </button>
        </div>

        <p v-if="error" class="text-red-500 text-sm mt-1">
            {{ error }}
        </p>
    </div>
</template>
