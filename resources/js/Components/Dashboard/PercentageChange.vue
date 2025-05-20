<template>
	<span :class="['font-bold opacity-75 text-xs', percentageChange > 0 ? 'text-green-500' : 'text-red-500']">
			({{ formattedPercentage }} %)
	</span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
	valueA: {
			type: Number,
			required: true,
	},
	valueB: {
			type: Number,
			required: true,
	},
});

const percentageChange = computed(() => {
	if (props.valueB > 0) {
			return ((props.valueA - props.valueB) / props.valueB) * 100;
	}
	return 100; 
});

const formattedPercentage = computed(() => percentageChange.value.toFixed(2));
</script>
