<script setup>
import { InputText, Select } from "primevue";
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import { useToast } from "primevue/usetoast";
import { computed, inject, onMounted, ref } from "vue";
const form = inject("form");
const totalSize = ref(0);
const totalSizePercent = ref(0);
const previewVisible = ref(false);
const previewUrl = ref('');
const previewExtension = ref('');
const toast = useToast();
const files = ref([]); // Lista de archivos seleccionados
const props = defineProps({
  agentDataupdate: {
    type: Object,
    required: true,
  },
  options:{
    type: Object,
    required: true,
  },
});

const openPreview = (doc) => {
  previewUrl.value = doc.full_link;
  previewExtension.value = doc.extension.toLowerCase();
  previewVisible.value = true;
};

onMounted(() => {
  console.log("📄 Documentos cargados desde base de datos:", props.agentDataupdate.user.documentation);
});

console.log("documentos cargados", props.agentDataupdate.documentation);
const parentDocumentationTypes = computed(() => {
  const allTypes = props.options.documentationtype || [];

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

    props.agentDataupdate.documents.push(documentData);
  });

  event.target.value = ""; // Limpiar el input
};


const removeFile = (index) => {
  props.agentDataupdate.documents.splice(index, 1); // ✅ Eliminá del array real
  toast.add({
    severity: "info",
    summary: "Archivo eliminado",
    detail: "El archivo ha sido eliminado de la lista.",
    life: 3000,
  });
};
const getTypeName = (typeId) => {
  console.log("🔍 Buscando tipo con ID:", typeId);

  const allTypes = props.options.documentationtype || [];
  const type = allTypes.find((t) => t.id === typeId);

  if (type) {
    console.log("✅ Tipo encontrado:", type.name);
    return type.name;
  } else {
    console.warn("⚠️ Tipo no encontrado para ID:", typeId);
    return `Tipo ID: ${typeId}`;
  }
};
const deleteServerDoc = async (doc, index) => {
  try {
    const confirmed = confirm(`¿Estás seguro que deseas eliminar "${doc.name}.${doc.extension}"?`);

    if (!confirmed) return;

    // Reemplazá esta URL por la tuya real
    await axios.delete(`/documents/${doc.id}`);

    // Eliminar del array local después del éxito
    toast.add({
      severity: "success",
      summary: "Documento eliminado",
      detail: `"${doc.name}.${doc.extension}" fue eliminado correctamente.`,
      life: 3000,
    });
  } catch (error) {
    console.error("❌ Error al eliminar documento:", error);
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "No se pudo eliminar el documento. Intenta de nuevo.",
      life: 5000,
    });
  }
};




// Enviar archivos al componente padre para que se incluyan en `agentData`
defineExpose({ files });


</script>
        <template>
            <div class="mx-auto bg-white p-5 rounded shadow">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Documentos</h2>
                <input type="file" multiple @change="handleFileSelection" class="hidden" id="file-input" />
                <label for="file-input" class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                  Subir documentos
                </label>
              </div>

              <div v-if=" agentDataupdate.documents.length > 0">
                <div v-for="(doc, index) in  agentDataupdate.documents" :key="index" class="border p-2 rounded flex justify-between items-center">
                  <span class="font-semibold">{{ doc.name }}.{{ doc.extension }}</span>
                  <InputText v-model="doc.description" placeholder="Descripción" class="w-48" />
                  <Select v-model="doc.type" :options="parentDocumentationTypes" optionLabel="name" optionValue="id" placeholder="Tipo" />
                  <button @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                    <i class="pi pi-times text-lg"></i>
                  </button>
                </div>
              </div>
              <div v-if="agentDataupdate.documentation?.length > 0" class="mt-8 border-t pt-4">
                <h3 class="text-lg font-semibold text-blue-700 mb-2">📁 Documentos cargados</h3>
                <div
                  v-for="(doc, index) in agentDataupdate.documentation"
                  :key="'loaded-' + index"
                  class="border p-3 rounded mb-2 bg-gray-100 flex justify-between items-center"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-700">
                      {{ doc.name }}.{{ doc.extension }}
                    </p>
                    <p class="text-xs text-gray-600">{{ doc.description }}</p>
                    <p class="text-xs text-gray-500 italic">
                      Tipo: {{ getTypeName(doc.type) }}
                    </p>
                  </div>
                  <Button
                    label="Ver"
                    icon="pi pi-eye"
                    severity="info"
                    size="small"
                    @click="openPreview(doc)"
                    />

                </div>
              </div>

            </div>

            <Dialog
        v-model:visible="previewVisible"
        modal
        header="Vista previa del documento"
        :style="{ width: '70vw' }"
        :breakpoints="{ '960px': '90vw', '640px': '100vw' }"
        >
        <div class="text-center">
            <!-- Si es imagen -->
            <img
            v-if="['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(previewExtension)"
            :src="previewUrl"
            alt="Vista previa de imagen"
            class="max-h-[500px] mx-auto"
            />

            <!-- Si es PDF -->
            <iframe
            v-else-if="previewExtension === 'pdf'"
            :src="previewUrl"
            class="w-full h-[500px]"
            ></iframe>

            <!-- Si no soporta preview -->
            <div v-else>
            <p class="text-gray-500">No se puede mostrar la vista previa para este tipo de archivo.</p>
            <a :href="previewUrl" target="_blank" class="text-blue-500 underline mt-2 inline-block">Descargar archivo</a>
            </div>
        </div>
        </Dialog>

</template>
