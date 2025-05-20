<template>
    <div class="w-full">
      <div class="rounded-xl shadow-md bg-indigo-50 p-4 border-indigo-200 border mb-auto w-full">
        <div class="flex justify-start items-center border-b pb-2">
          <h2 class="font-semibold opacity-75 uppercase">Agentes</h2>
        </div>
        <div class="border-b border-indigo-100 py-2 flex justify-center">
          <div class="text-center">
            <i class="pi pi-users !text-9xl inline-block text-indigo-950"></i>
            <h3 class="text-lg font-semibold uppercase opacity-85">Total: {{ data['totalAgentes'] }}</h3>
          </div>
        </div>
        <div class="grid grid-cols-2 divide-x mb-2">
          <div class="flex justify-center h-12 py-4 text-green-500 text-4xl">
            <div class="text-center flex gap-4">
              <i class="pi pi-user-plus !text-4xl inline-block"></i>
              <h4 class="text-black opacity-75 font-semibold text-md">{{ data['totalAgentesIngresados'] }}</h4>
            </div>
          </div>
          <div class="flex justify-center h-12 py-4 text-red-500 text-4xl">
            <div class="text-center flex gap-4">
              <i class="pi pi-user-minus !text-4xl inline-block"></i>
              <h4 class="text-black opacity-75 font-semibold text-md">{{ data['totalAgentesRetirados'] }}</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="rounded-xl shadow-md bg-indigo-50 p-4 border-indigo-200 border mb-auto mt-4">
        <div class="flex gap-4 flex-wrap">
          <div v-for="comision in data['comisionesAgentes']" :key="comision.id"
            :class="[!comision.porcentaje ? 'hidden' : 'rounded shadow-md text-center bg-green-200 py-1 px-2']">
            <span class="text-2xl opacity-85">{{ comision.total }}</span>
            <span class="block font-bold opacity-85">{{ parseFloat(comision.porcentaje) * 100 }}%</span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  defineProps({
    data: Array
  });
  </script>