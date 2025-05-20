<script setup>

import { onMounted, ref } from "vue";
import VuePdfApp from "vue3-pdf-app";
// import this to use default icons for buttons
import "vue3-pdf-app/dist/icons/main.css";
// either URL, Base64, binary, or document proxy
const { $apiClient } = useNuxtApp();
const route = useRoute();
const rutaPdf=ref()
const frame = ref()

// When accessing /posts/1, route.params.id will be 1
rutaPdf.value=$apiClient.defaults.baseURL+"/transactions/get-transaction/" + route.params.id + "/pdf";
console.log(rutaPdf.value);

onMounted(()=>{
	
})
</script>

<template>
	<div class="h-screen">
		<vue-pdf-app style="height: 100vh;" :pdf="rutaPdf"></vue-pdf-app>

	</div>
</template>
