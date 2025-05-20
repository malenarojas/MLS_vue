
<template>
  <div class="border py-4 px-6 rounded-xl shadow-md w-72 bg-white">
    <div class="flex justify-between mb-2">
      <div class="flex items-center justify-start">
        <i :class="['pi inline-block', icon, '!text-2xl']"></i>
      </div>
      <div class="flex justify-end items-center">
        <div>
          <div class="flex gap-2 items-center">
            <PercentageChange :valueA="valueA" :valueB="valueB" v-if="valueB" />
            <h3 class="text-2xl font-semibold opacity-75 text-end">{{ Intl.NumberFormat(('en-US')).format(valueA) }}</h3>
          </div>
          <p class="text-sm opacity-75 text-end">{{ title }}</p>
        </div>
      </div>
    </div>

    <div class="border-t">
      <div  class="my-1 flex justify-start">
        <p class="text-sm opacity-75 text-end" v-if="goal">Meta: {{ Intl.NumberFormat(('en-US')).format(goal) }}</p>
        <p class="text-sm opacity-75 text-end" v-else>Meta no definida</p>
      </div>

      <ProgressBar 
        :value="goalReached" 
        size="small" 
        :style="{
          '--p-progressbar-value-background': getColor(goalReached)
        }"/>
    </div>
  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';
import PercentageChange from '@/Components/Dashboard/PercentageChange.vue';

const props = defineProps({
  icon: String,
  title: String,
  valueA: Number,
  valueB: Number,
  goal : Number
});

const goalReached = computed (() => {
  return props.goal ? parseFloat(props.valueA * 100 / props.goal).toFixed(2): 0
});

const getColor = (value) => {
  if (value < 80) return '#EF4444';        
  if (value < 99) return '#F59E0B';        
  return '#10B981';      
}
</script>
