<template>
    <div v-if="!agenteSeleccionado"
					class="rounded-xl shadow-md p-4 col-span-2 md:col-span-1 border-indigo-200 border mb-auto w-full h-full">

					<div class="flex items-center border-b pb-2  justify-between">
						<h2 class="font-semibold opacity-75 uppercase">Ranking</h2>

						<button @click="openModal = true">
							<i class="pi pi-window-maximize opacity-75"></i>
						</button>

					</div>

					<ul class="w-full h-full overflow-y-auto max-h-80">
						<li v-for="(item, index) in itemsRanking" :key="index" @click="
										if(!oficinaSeleccionada) {
											seleccionarOficina(item.id);
											openModal = false;
										}else{
											if(!agenteSeleccionado) {
												seleccionarAgente(item.id);
											}
										}
										" class="py-1 border-b flex gap-2 items-center justify-between">
							<div class="flex gap-2 items-center">
								<span class="font-bold opacity-75 text-sm mr-2">{{ parseInt(index) + 1}}</span>

								<!-- <img v-if="!oficinaSeleccionada" :src="item.image && item.image.trim() !== '' 
								? `http://127.0.0.1:8000/${item.image}` 
								: 'http://127.0.0.1:8000/images/oficinas/remax.jpg'" class="h-8" alt=""> -->

								<span class="text-black text-semibold opacity-75 text-xs " :title="item.name">
									{{ item.name.length > 15 ? item.name.substr(0, 12)+ '...' : item.name }}
								</span>
							</div>
							<span class="text-sm font-semibold opacity-85">
								$ {{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(item.total) }}
							</span>
						</li>
					</ul>
					<transition name="fade" @before-enter="onBeforeEnter" @after-enter="onAfterEnter"
						@before-leave="onBeforeLeave" @after-leave="onAfterLeave">
						<div v-if="openModal" class="fixed inset-0 bg-gray-800 bg-opacity-50  z-[52] h-screen 
						p-4 md:p-20">
							<div class="bg-white rounded-lg shadow-lg w-2xl p-6 max-h-full overflow-y-auto">

								<div class="flex justify-between items-center mb-4 pb-2 border-b ">
									<h3 class="text-lg font-semibold uppercase opacity-75">Ranking</h3>
									<button class="text-gray-400 hover:text-gray-600" @click="openModal = false">
										&times;
									</button>
								</div>

								<div class="w-full overflow-x-auto">
									<table class="w-full table-auto">
										<thead class="font-semibold opacity-85 text-sm text-left bg-indigo-50">
											<tr class="*:p-2">
												<th>Puesto</th>
												<th>{{ oficinaSeleccionada && oficinaSeleccionada ? 'Agente' :
													'Oficina' }}</th>
												<th>Tiempo en mercado</th>
												<th v-if="!oficinaSeleccionada">Agentes Activos</th>
												<th>Captaciones</th>
												<th>Monto</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="(item, index) in itemsRanking" :key="index" class="border-b *:p-2 cursor-pointer"
												@click="
												if(!oficinaSeleccionada) {
													seleccionarOficina(item.id);
													openModal = false;
												}else{
													if(!agenteSeleccionado) {
														seleccionarAgente(item.id);
													}
												}
										">

												<td :class="['font-bold opacity-75 text-sm mr-2',
												index == 0 ? 'text-yellow-400' : 
												index == 1 ? 'text-gray-400' :
												index == 2 ? 'text-amber-700' : 'opacity-55'
											]">
													{{ parseInt(index) + 1}}
												</td>

												<td class="flex gap-4">

													<!-- <img :src="
													item.image && item.image.trim() !== '' ? `http://127.0.0.1:8000/${item.image}` :
													'http://127.0.0.1:8000/images/oficinas/remax.jpg'" class="h-8 hidden sm:inline-block" alt=""> -->

													<div class="flex flex-col sm:ml-2">
														<span class="text-black font-semibold opacity-85 text-sm ">
															{{ item.name }}
														</span>
														<span v-if="!oficinaSeleccionada" class="text-xs opacity-75 hidden md:inline-block">
															{{ item.city }}, {{ item.country }}
														</span>
													</div>
												</td>

												<td>
													<span class="text-sm  opacity-95">
														{{ item.tiempo_activa }}
													</span>
												</td>

												<td v-if="!oficinaSeleccionada">
													<span class="text-sm  opacity-95">
														{{ item.agentes_activos }}
													</span>
												</td>

												<td>
													<span class="text-sm  opacity-95">
														{{ item.captaciones }}
													</span>
												</td>

												<td class="flex justify-end">
													<span class="text-sm font-bold opacity-95">
														$ {{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(item.total) }}
													</span>
												</td>


											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</transition>

				</div>
</template>    

<script setup>

import { defineProps, ref  } from 'vue';

const props = defineProps({
	itemsRanking: Array,
	agenteSeleccionado: String,
	oficinaSeleccionada: String,
	seleccionarOficina: Function,
	seleccionarAgente : Function
});

let openModal = ref(false);

</script>