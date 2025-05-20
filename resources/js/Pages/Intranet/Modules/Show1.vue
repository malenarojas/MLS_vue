<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
    module: Object,
    page: Object,
});
</script>

<template>
    <AppLayout :title="page.title">
        <div class="max-w-5xl mx-auto p-4">
            <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">
                {{ page.title }}
            </h1>

            <div
                v-for="section in page.sections"
                :key="section.id"
                class="mb-10 border-b pb-6"
            >
                <h2
                    class="text-xl font-semibold text-red-700 mb-4 uppercase tracking-wide"
                >
                    {{ section.title }}
                </h2>

                <ul class="space-y-3">
                    <li
                        v-for="el in section.elements"
                        :key="el.id"
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
                    >
                        <span class="text-gray-800 font-medium text-base">
                            {{ el.content.name }}
                        </span>

                        <a
                            :href="el.content?.url"
                            target="_blank"
                            class="text-sm text-blue-600 hover:underline"
                        >
                            Ver contenido
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
