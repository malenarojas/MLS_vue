<template>
	<div class="container pt-4">

		<div class="grid grid-cols-12 gap-3 items-center mx-2">

			<div class="col-span-12 md:col-span-4  text-lg font-semibold">En desarrollo </div>
			</div>

	</div>

</template>

<script setup>

import { storeToRefs } from "pinia";
import { useContactStore } from "@/Stores/useContactStore";
const storeApp = useContactStore();
const { datos } = storeToRefs(storeApp);
import { useOptLista } from "@/Composables/useOptLista"
const { tipoComission } = useOptLista();

</script>
