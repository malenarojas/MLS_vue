<template>
	<div >
	  <h1>Desglose de calificaciones y comentarios</h1>
	  <DataTable :value="ratings" responsiveLayout="scroll">
		<!-- C<Column field="rating" header="Calificación" sortable>
		  <template #body="{ data }">
			<Rating v-model="data.rating" :cancel="false" :stars="5" disabled />
		  </template>
		</Column>

		<Column field="agent" header="Rating Agent" sortable>
		  <template #body="{ data }">
			{{ data.agent }}
		  </template>
		</Column>

		<Column field="type" header="Tipo" sortable>
		  <template #body="{ data }">
			{{ data.type }}
		  </template>
		</Column>

		<Column field="comments" header="Comentarios">
		  <template #body="{ data }">
			{{ data.comments }}
		  </template>
		</Column>-->
	  </DataTable>
	</div>
  </template>

  <script setup>
  import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Rating from "primevue/rating";
import { ref } from "vue";

  // Datos simulados
  const ratings = ref([
	{
	  id: 1,
	  rating: 4,
	  agent: "John Doe",
	  type: "Cliente",
	  comments: "Muy buen servicio.",
	},
	{
	  id: 2,
	  rating: 5,
	  agent: "Jane Smith",
	  type: "Cliente",
	  comments: "Excelente experiencia.",
	},
	{
	  id: 3,
	  rating: 3,
	  agent: "Carlos Lopez",
	  type: "Proveedor",
	  comments: "Servicio promedio.",
	},
  ]);
  </script>

  <style scoped>
  .container {
	margin-top: 2rem;
  }
  h1 {
	font-size: 1.5rem;
	font-weight: bold;
	margin-bottom: 1rem;
  }
  </style>
