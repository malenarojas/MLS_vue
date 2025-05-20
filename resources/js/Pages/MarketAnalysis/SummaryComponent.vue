<template>
  <div class="summary-container grid grid-cols-5 gap-4 p-4 bg-white rounded shadow-md">
    <div
      v-for="(value, label) in summaries"
      :key="label"
      :class="[gradientClasses(label), 'summary-item text-white p-4 rounded flex flex-col items-center justify-center']"
    >
      <span class="text-2xl font-bold">{{ formatValue(label, value) }}</span>
      <span class="text-sm">{{ label }}</span>
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'

const props = defineProps({
  summaries: {
    type: Object,
    required: true,
    default: () => ({
      "Días en el mercado": 0,
      "Precio captacion": 0,
      "Precio venta": 0,
      "Precio/M²": 0,
      "Proporción vendida a la lista": 0,
    }),
  },
  selectedCurrency: {
    type: Object,
    required: true,
    default: () => ({
      symbol: 'Bs',
      code: 'BOB'
    })
  }
});
watch(
  () => props.selectedCurrency,
  (newCurrency) => {
    console.log('💱 Moneda seleccionada cambió a:', newCurrency)
  },
  { immediate: true }
)
const gradientClasses = (label) => {
  const gradients = {
    "Días en el mercado": "bg-gradient-to-r from-red-500 to-red-300",
    "Precio captacion": "bg-gradient-to-r from-blue-500 to-blue-300",
    "Precio venta": "bg-gradient-to-r from-green-500 to-green-300",
    "Precio/M²": "bg-gradient-to-r from-orange-500 to-orange-300",
    "Proporción vendida a la lista": "bg-gradient-to-r from-purple-500 to-purple-300",
  };
  return gradients[label] || "bg-gradient-to-r from-gray-500 to-gray-300";
};

const formatValue = (label, value) => {
  if (label === "Días en el mercado") return `${value} días`;
  if (label === "Proporción vendida a la lista") return `${value}%`;

  const currencyCode = getCurrencyCode()
  return formatPrice(value, currencyCode)
}


function getCurrencyCode() {
  const symbol = props.selectedCurrency?.symbol

  if (symbol === 'Bs' || symbol === 'BOB') return 'BOB'
  if (symbol === '$' || symbol === 'USD') return 'USD'

  return 'BOB' // fallback seguro
}
function formatPrice(value, currencyCode = 'BOB') {
  return new Intl.NumberFormat("es-BO", {
    style: "currency",
    currency: currencyCode
  }).format(value)
}
</script>


<style scoped>
/* No necesitas estilos adicionales, Tailwind CSS cubre todo */
</style>

