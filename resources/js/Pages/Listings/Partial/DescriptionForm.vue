<script setup>
import { computed, ref, inject, watchEffect } from "vue";
import { usePage } from "@inertiajs/vue3";
import TextInput from "@/Components/Common/TextInput.vue";
import CustomTextarea from "@/Components/Common/CustomTextarea.vue";
import CustomEditor from "@/Components/Common/CustomEditor.vue";

const form = inject("form");

const page = usePage();
const props = computed(() => page.props);

// Definir los idiomas desde Inertia
const languages = computed(
    () =>
        props.value.languages ?? [
            { code: "ESB", name: "Español", is_default: 1 },
        ]
);

// Encontrar el idioma predeterminado
const defaultLanguage = computed(() =>
    languages.value.find((lang) => lang.is_default)
);

const activeLanguage = ref(defaultLanguage.value?.code ?? "ESB");

// **Inicializar `translations` si no existen**
watchEffect(() => {
    if (!form.translations) {
        form.translations = {};
    }

    languages.value.forEach((language) => {
        if (!language.is_default) {
            if (!form.translations[language.code]) {
                form.translations[language.code] = {
                    title: "",
                    description_website: "",
                    marketing_description: "",
                    location_information: "",
                };
            }
        }
    });
});

const changeLanguage = (code) => {
    activeLanguage.value = code;
};

// **Computed Property para manejar `v-model` de forma segura**
const getFieldModel = (field, language) => {
    return computed({
        get: () => {
            return language.is_default
                ? form[field]
                : form.translations?.[language.code]?.[field] ?? "";
        },
        set: (value) => {
            if (language.is_default) {
                form[field] = value;
            } else {
                if (!form.translations[language.code]) {
                    form.translations[language.code] = {};
                }
                form.translations[language.code][field] = value;
            }
        },
    });
};

// **Función para obtener el nombre del campo correcto en validaciones**
const getFieldName = (field, languageCode) => {
    return languageCode === defaultLanguage.value?.code
        ? field
        : `translations.${languageCode}.${field}`;
};
</script>

<template>
    <div
        class="flex flex-col sm:flex-row gap-5 justify-center bg-slate-100 p-5 rounded-md shadow-md"
    >
        <div class="w-full sm:w-1/4 sm:pr-4 sm:border-r sm:border-gray-300">
            <div class="flex flex-col gap-3">
                <button
                    v-for="language in languages"
                    :key="language.code"
                    type="button"
                    :class="[
                        'px-4 py-2 text-left font-semibold rounded-md',
                        activeLanguage === language.code
                            ? 'bg-blue-500 text-white shadow-md'
                            : 'bg-gray-200 text-gray-600 hover:bg-gray-300',
                    ]"
                    @click="changeLanguage(language.code)"
                >
                    {{ language.name }}
                </button>
            </div>
        </div>

        <!-- <pre>{{ form }}</pre> -->

        <div class="w-full sm:w-3/4 sm:pl-4 py-4">
            <div
                v-for="language in languages"
                :key="language.code"
                v-show="activeLanguage === language.code"
            >
                <div class="mb-4">
                    <TextInput
                        v-model="getFieldModel('title', language).value"
                        :label="`Título de la captación (${language.name})`"
                        :error="
                            form.errors[getFieldName('title', language.code)]
                        "
                        placeholder="Título de la captación"
                        required
                    />
                </div>

                <div class="mb-4">
                    <CustomTextarea
                        v-model="
                            getFieldModel('description_website', language).value
                        "
                        :label="`Descripción para la Web (${language.name})`"
                        :error="
                            form.errors[
                                getFieldName(
                                    'description_website',
                                    language.code
                                )
                            ]
                        "
                        placeholder="Descripción para la Web"
                        :maxlength="1000000"
                        v-bind="{ rows: 12 }"
                    />
                </div>

                <div class="mb-4">
                    <CustomTextarea
                        v-model="
                            getFieldModel('marketing_description', language)
                                .value
                        "
                        :label="`Descripción de marketing (${language.name})`"
                        :error="
                            form.errors[
                                getFieldName(
                                    'marketing_description',
                                    language.code
                                )
                            ]
                        "
                        placeholder="Descripción de marketing"
                        :maxlength="350"
                        v-bind="{ rows: 8 }"
                    />
                </div>

                <div class="mb-4">
                    <!-- <CustomTextarea
                        v-model="
                            getFieldModel('location_information', language)
                                .value
                        "
                        :label="`Información de Ubicación (${language.name})`"
                        :error="
                            form.errors[
                                getFieldName(
                                    'location_information',
                                    language.code
                                )
                            ]
                        "
                        placeholder="Información de Ubicación"
                        v-bind="{ rows: 12 }"
                    /> -->
                    <!-- <pre>{{ form.location_information }}</pre> -->
                    <CustomTextarea
                        v-model="
                            getFieldModel('location_information', language)
                                .value
                        "
                        :label="`Información de Ubicación (${language.name})`"
                        :error="
                            form.errors[
                                getFieldName(
                                    'location_information',
                                    language.code
                                )
                            ]
                        "
                        :maxlength="1000000"
                        v-bind="{ rows: 12 }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
