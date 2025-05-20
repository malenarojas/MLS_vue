<script setup>
import { InputText, Select } from "primevue";
import { useToast } from "primevue/usetoast";
import { computed, inject, ref } from "vue";
const form = inject("form");
const totalSize = ref(0);
const totalSizePercent = ref(0);

const toast = useToast();
const files = ref([]); // Lista de archivos seleccionados
const props = defineProps({
  agentData: {
    type: Object,
    required: true,
  },
  options:{
    type: Object,
    required: true,
  },
});

const parentDocumentationTypes = computed(() => {
  const allTypes = props.options.formOptions.documentationtype || [];

  console.log("Todos los tipos de documentación disponibles:", allTypes);

  const filteredTypes = allTypes.filter(type => type.parent_id === 39);

  console.log("Tipos de documentación filtrados (parent_id = 39):", filteredTypes);

  return filteredTypes;
});


const handleFileSelection = (event) => {
  const selectedFiles = Array.from(event.target.files);

  selectedFiles.forEach((file) => {
    // Validar tamaño máximo (1MB = 1024 * 1024 bytes)
    if (file.size > 1024 * 1024) {
      toast.add({
        severity: "warn",
        summary: "Archivo muy grande",
        detail: `El archivo "${file.name}" supera 1MB y no se ha agregado.`,
        life: 5000,
      });
      return; // No lo agregues si se pasa
    }

    const [name, extension] = file.name.split(/\.(?=[^\.]+$)/);

    const documentData = {
      file,
      name,
      extension,
      description: "",
      type: null,
    };

    props.agentData.documents.push(documentData);
  });

  event.target.value = ""; // Limpiar el input
};


const removeFile = (index, event) => {
  if (event) event.preventDefault(); // ✅ ahora sí funciona si lo necesitás

  props.agentData.documents.splice(index, 1);
  toast.add({
    severity: "info",
    summary: "Archivo eliminado",
    detail: "El archivo ha sido eliminado de la lista.",
    life: 3000,
  });
};


defineExpose({ files });


</script>

<!-- <template>
    <div class="mx-auto bg-white p-5 rounded shadow">
        Documentos
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Documentos</h2>
            <input
                type="file"
                multiple
                @change="handleFileSelection"
                class="hidden"
                id="file-input"
            />
            <label
                for="file-input"
                class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600"
            >
                Subir documentos
            </label>
        </div>

        <div v-if="files.length > 0">
            <div
                v-for="(file, index) in files"
                :key="index"
                class="border p-2 rounded flex justify-between items-center"
            >
                <span class="font-semibold"
                    >{{ file.name }}.{{ file.extension }}</span
                >
                <InputText
                    v-model="file.description"
                    placeholder="Descripción"
                    class="w-48"
                />
                <Select
                    v-model="file.type"
                    :options="documentTypes"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Tipo"
                />
                <button
                    @click="removeFile(index)"
                    class="text-red-500 hover:text-red-700"
                >
                    <i class="pi pi-times text-lg"></i>
                </button>
            </div>

            <div class="flex justify-end mt-2">
                <button
                    @click="uploadFiles"
                    class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600"
                >
                    Subir
                </button>
            </div>
        </div>

        <div v-if="form.documents?.length > 0">
            <h3 class="mt-6 font-semibold">Archivos Subidos</h3>
            <div
                v-for="(doc, index) in form.documents"
                :key="index"
                class="border p-2 rounded flex justify-between items-center"
            >
                <span>{{ doc.name }}.{{ doc.extension }}</span>
                <span>{{ doc.description }}</span>
                <button
                    @click="deleteDoc(index)"
                    class="text-red-500 hover:text-red-700"
                >
                    <i class="pi pi-trash text-lg"></i>
                </button>
            </div>
        </div>-->
        <template>
            <div class="mx-auto bg-white p-5 rounded shadow">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Documentos</h2>
                <input type="file" multiple @change="handleFileSelection" class="hidden" id="file-input" />
                <label for="file-input" class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                  Subir documentos
                </label>
              </div>
              <div v-if="agentData.documents.length > 0">
                <div v-for="(doc, index) in agentData.documents" :key="index" class="border p-2 rounded flex justify-between items-center">
                  <span class="font-semibold">{{ doc.name }}.{{ doc.extension }}</span>
                  <InputText v-model="doc.description" placeholder="Descripción" class="w-48" />
                  <Select v-model="doc.type" :options="parentDocumentationTypes" optionLabel="name" optionValue="id" placeholder="Tipo" />
                  <span
                    @click="removeFile(index)"
                    class="text-red-500 hover:text-red-700 cursor-pointer"
                    >
                    <i class="pi pi-times text-lg"></i>
                    </span>
                </div>
              </div>
            </div>
</template>
