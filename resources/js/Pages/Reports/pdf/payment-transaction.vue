<script setup>

import { onMounted, ref } from "vue";
// either URL, Base64, binary, or document proxy
const { $apiClient } = useNuxtApp();
const route = useRoute();
const rutaPdf=ref()
const frame = ref()


rutaPdf.value = $apiClient.defaults.baseURL+`/payments/get-payments-transaction-pdf-get/${route.query.office_ids}/${route.query.start_date}/${route.query.end_date}`;

onMounted(()=>{
	
});
</script>

<template>
	<div class="h-screen">
		<iframe width="100%" height="100%" ref="frame" :src="rutaPdf" frameborder="0"></iframe>
	</div>
</template>
