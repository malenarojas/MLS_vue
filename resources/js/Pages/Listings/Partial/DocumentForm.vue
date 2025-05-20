<script setup>
import { inject, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { usePage } from "@inertiajs/vue3";

const form = inject("form", { errors: {} });
const toast = useToast();
const props = usePage().props;

const publicDocumentType = computed(() => props.public_documents || []);
const privateDocumentType = computed(() => props.private_documents || []);

/**
 * Handles file selection for both public and private documents.
 */
const handleFileSelection = (event, type) => {
    const selectedFiles = Array.from(event.target.files);

    selectedFiles.forEach((file) => {
        const [name, extension] = file.name.split(/\.(?=[^\.]+$)/);
        const fileData = {
            file,
            name,
            extension,
            description: "",
            documentation_type_id: null,
            is_new: true,
            link: URL.createObjectURL(file), // Creates a preview link
        };

        if (type === "private") {
            form.private_documentation.push(fileData);
        } else {
            form.public_documentation.push(fileData);
        }
    });

    event.target.value = "";
};

/**
 * Removes a file from either private or public documents.
 */
const removeFile = (index, type) => {
    if (type === "private") {
        form.private_documentation.splice(index, 1);
    } else {
        form.public_documentation.splice(index, 1);
    }
};

/**
 * Shows toast notifications.
 */
const showToast = (severity, summary, detail) => {
    toast.add({ severity, summary, detail, life: 3000 });
};
</script>

<template>
    <div class="mx-auto bg-white p-5 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800 flex items-center">
            <i class="pi pi-folder text-blue-500 mr-2"></i> Documentos Privados
        </h2>

        <!-- Upload Button -->
        <div class="flex justify-end mb-4">
            <input
                type="file"
                multiple
                @change="(e) => handleFileSelection(e, 'private')"
                class="hidden"
                id="private-file-input"
            />
            <label
                for="private-file-input"
                class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600"
            >
                Subir documentos privados
            </label>
        </div>

        <!-- Table for Documents -->
        <table
            class="w-full border-collapse border border-gray-200 rounded-lg shadow-md"
        >
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-left">
                    <th class="p-3 border border-gray-200">Nombre</th>
                    <th class="p-3 border border-gray-200">Descripción</th>
                    <th class="p-3 border border-gray-200">Tipo</th>
                    <th class="p-3 border border-gray-200">Fecha de Subida</th>
                    <th class="p-3 border border-gray-200 text-center">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(file, index) in form.private_documentation"
                    :key="index"
                    class="hover:bg-gray-50"
                >
                    <td class="p-3 border border-gray-200">
                        <a
                            :href="file.full_link"
                            target="_blank"
                            class="text-blue-500 hover:underline"
                        >
                            {{ file.name }}.{{ file.extension }}
                        </a>
                    </td>
                    <td class="p-3 border border-gray-200">
                        <InputText
                            v-model="file.description"
                            placeholder="Descripción"
                            class="w-full"
                        />
                    </td>
                    <td class="p-3 border border-gray-200">
                        <Select
                            v-model="file.documentation_type_id"
                            :options="privateDocumentType"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Tipo"
                            class="w-full"
                        />
                    </td>
                    <td class="p-3 border border-gray-200 text-center">
                        {{ file.upload_date || "-" }}
                    </td>
                    <td class="p-3 border border-gray-200 text-center">
                        <button
                            @click="removeFile(index, 'private')"
                            class="text-red-500 hover:text-red-700"
                        >
                            <i class="pi pi-trash text-lg"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Public Documents -->
    <div class="mx-auto bg-white p-5 rounded shadow mt-10">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800 flex items-center">
            <i class="pi pi-folder text-blue-500 mr-2"></i>
            Documentos Publicos
        </h2>

        <!-- Upload Button -->
        <div class="flex justify-end mb-4">
            <input
                type="file"
                multiple
                @change="(e) => handleFileSelection(e, 'public')"
                class="hidden"
                id="public-file-input"
            />
            <label
                for="public-file-input"
                class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600"
            >
                Subir documentos públicos
            </label>
        </div>

        <!-- Table for Documents -->
        <table
            class="w-full border-collapse border border-gray-200 rounded-lg shadow-md"
        >
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-left">
                    <th class="p-3 border border-gray-200">Nombre</th>
                    <th class="p-3 border border-gray-200">Descripción</th>
                    <th class="p-3 border border-gray-200">Tipo</th>
                    <th class="p-3 border border-gray-200">Fecha de Subida</th>
                    <th class="p-3 border border-gray-200 text-center">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(file, index) in form.public_documentation"
                    :key="index"
                    class="hover:bg-gray-50"
                >
                    <td class="p-3 border border-gray-200">
                        <a
                            :href="file.full_link"
                            target="_blank"
                            class="text-blue-500 hover:underline"
                        >
                            {{ file.name }}.{{ file.extension }}
                        </a>
                    </td>
                    <td class="p-3 border border-gray-200">
                        <InputText
                            v-model="file.description"
                            placeholder="Descripción"
                            class="w-full"
                        />
                    </td>
                    <td class="p-3 border border-gray-200">
                        <Select
                            v-model="file.documentation_type_id"
                            :options="publicDocumentType"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Tipo"
                            class="w-full"
                        />
                    </td>
                    <td class="p-3 border border-gray-200 text-center">
                        {{ file.upload_date || "-" }}
                    </td>
                    <td class="p-3 border border-gray-200 text-center">
                        <button
                            @click="removeFile(index, 'public')"
                            class="text-red-500 hover:text-red-700"
                        >
                            <i class="pi pi-trash text-lg"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
