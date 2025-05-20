<template>
	<span v-if="props.valueB" :class="['font-bold opacity-75 text-xs whitespace-nowrap', color]">
        <i :class="arrow"></i>
			({{ formattedPercentage }} %)
	</span>
	<span v-else class="text-red-500 text-xs font-bold opacity-75">
		<i class="fa fa-minus"></i>
		(Sin datos)

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
	comparativeType : {
			type: String,
			required: true
	}
});

const percentageChange = computed(() => {
	if (props.valueB > 0) {
		if (props.comparativeType == 'year') {
			return (props.valueA - props.valueB) * 100 / props.valueB;
		} else {
			return props.valueA * 100 / props.valueB;
		}
	}
	return 100;
});

const arrow = computed(() => {
	if (props.comparativeType == 'year') {
		return percentageChange.value > 0 ? 'fa fa-arrow-up text-xs' : 'fa fa-arrow-down text-xs';
	}
	return ''; 
});

const color = computed(() => {
	if (props.comparativeType == 'year') {
		return percentageChange.value > 0 ? 'text-green-500' : 'text-red-500';
	} else {
		if (percentageChange.value > 99) return 'text-green-500';
		if (percentageChange.value >= 80) return 'text-yellow-500';
		return 'text-red-500';
	}
});

const formattedPercentage = computed(() => percentageChange.value.toFixed(2));
</script>
