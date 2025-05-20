<script setup>
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed } from "vue";
import { USER_ROLES } from "@/Constants/roles";

const props = defineProps({
    user: Object,
});

const reports = [
    {
        title: "Reporte de pagos de transacciones",
        category: "Transacciones",
        route: "reports.transaction-payment",
        color: "blue",
        view: true,
    },
    {
        title: "Reporte de usuarios activos",
        category: "Mis Listings",
        route: "reports.listings",
        color: "green",
        view: [USER_ROLES.ADMIN, USER_ROLES.BROKER].includes(
            props?.user?.roles[0]?.name
        ),
    },
];
const visibleReports = computed(() => reports.filter((report) => report.view));

function openReport(routeName) {
    router.visit(route(routeName));
}
</script>

<template>
    <AppLayout>
        <div class="p-4">
            <h1 class="font-bold text-2xl opacity-75">
                <i class="pi pi-file-check"></i> Reportes
            </h1>

            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4"
            >
                <div
                    v-for="(report, index) in visibleReports"
                    :key="index"
                    class="rounded overflow-hidden border shadow-md flex cursor-pointer select-none transition hover:scale-[1.01]"
                    :class="`border-${report.color}-500`"
                    @click="() => openReport(report.route)"
                >
                    <div :class="`bg-${report.color}-500 h-24 w-4`"></div>
                    <div class="p-2">
                        <div
                            :class="`rounded-full bg-${report.color}-100 text-${report.color}-500 px-3 py-1 text-sm inline`"
                        >
                            {{ report.category }}
                        </div>
                        <h2 class="ml-2 font-semibold mt-1 opacity-85">
                            {{ report.title }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<!-- <script setup>
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

function openTransactionPaymentReport() {
    router.visit("/reports/transaction-payment");
}
</script> -->
