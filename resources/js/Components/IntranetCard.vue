<template>
    <div
      class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 cursor-pointer group"
    >
      <img
        :src="image"
        :alt="title"
        class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div class="p-4 text-center">
        <h2 class="text-blue-800 font-semibold text-lg group-hover:text-blue-600 transition-colors">
          {{ title }}
        </h2>
      </div>
    </div>
  </template>

  <script setup>
  const props = defineProps({
    title: String,
    image: String
  });
  </script>
