<template>
    <Dialog
  v-model:visible="localShowModal"
  modal
  header="Detalles del Agente"
  :style="{ width: '50vw' }"
  :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
>
  <div class="agent-details-container">
    <div class="agent-image-container">
      <img
        v-if="props.agent.image_url"
        :src="props.agent.image_url"
        alt="Foto del agente"
        class="agent-image"
      />
      <p v-else class="text-muted">No hay imagen disponible</p>
    </div>

    <div class="agent-info-grid">
      <div class="info-item">
        <strong>Nombre:</strong>
        <span>{{ props.agent.user?.name_to_show ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <strong>Región:</strong>
        <span>{{ props.agent.region_name ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <strong>Oficina:</strong>
        <span>{{ props.agent.office_name ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <strong>Correo Electrónico:</strong>
        <span>{{ props.agent.user?.email ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <strong>Género:</strong>
        <span>{{ props.agent.user?.gender ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <strong>Teléfono:</strong>
        <span>{{ props.agent.user?.phone_number ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <strong>Marketing Slogan:</strong>
        <span>{{ props.agent.marketing_slogan ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <strong>Países de Interés:</strong>
        <span>{{ props.agent.countries_interested ?? 'N/A' }}</span>
      </div>
      <div class="info-item">
        <p>
        <strong>Fecha de Ingreso:</strong>
        {{ props.agent.date_joined ? new Date(props.agent.date_joined).toLocaleDateString('es-BO') : 'N/A' }}
        </p>
      </div>
    </div>
  </div>
</Dialog>
  </template>

  <script setup>
  import { computed, ref, watch } from 'vue';

import Dialog from 'primevue/dialog';
  const props = defineProps({
    agent: {
    type: Object,
      required: true,
    },
    showModal: {
      type: Boolean,
      required: true,
    },
  });

  console.log("agent resibido en el modal", props.agent);

  //const localShowModal = ref(props.showModal);
  const emit = defineEmits(['update:showModal']);
  const localShowModal = computed({
  get: () => props.showModal,
  set: (value) => emit('update:showModal', value),
});

  const agent = ref({});

  // Cuando el modal se abre y hay un ID, hacemos la petición
  watch(
		() => props.showModal,
		(newValue) => {
		  localShowModal.value = newValue;
		}
	  );

  </script>


  <style scoped>
  .agent-details-container {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  flex-wrap: wrap;
}

.agent-image-container {
  flex: 1;
  max-width: 200px;
}

.agent-image {
  width: 100%;
  height: auto;
  border-radius: 8px;
  border: 1px solid #ddd;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.agent-info-grid {
  flex: 2;
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* Fuerza dos columnas */
  gap: 1rem;
  font-size: 1rem;
  color: #333;
  width: 100%;
  gap: 0.25rem 1.5rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  word-break: break-word;
  margin-bottom: 0; /* 🔥 Evita espacio extra */
  padding: 0;
  margin: 0;
}

  </style>
