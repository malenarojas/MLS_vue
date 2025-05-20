<template>
    <AppLayout>
        <div class="card flex justify-center">
            <Stepper v-model:value="activeStep" class="w-full">
                <StepList>
                    <Step
                        v-for="(item, index) in steps"
                        v-slot="{ activateCallback, value, a11yAttrs }"
                        asChild
                        :value="index + 1"
                    >
                        <div
                            class="flex flex-row justify-center items-center"
                            v-bind="a11yAttrs.root"
                            v-if="
                                activeStep >= index + 1 &&
                                (index + 1 != 5 ||
                                    (!isEmptyObject(
                                        store.getStepData(transactionId, 1)
                                    ) &&
                                        store.getStepData(transactionId, 1)[
                                            'side'
                                        ] == 'both')) // && //Verifica si es both
                                //((index + 1 != 4 && index + 1 != 5) || (!isEmptyObject(store.getStepData(transactionId, 1)) && store.getStepData(transactionId, 1)['is_external'] == 0)) //Verifica si es external
                            "
                        >
                            <button
                                class="bg-transparent border-0 flex items-center gap-1"
                                @click="
                                    () => handleStepChange(activateCallback)
                                "
                                v-bind="a11yAttrs.header"
                            >
                                <span
                                    :class="[
                                        'rounded-full border-2 w-8 h-8 flex items-center justify-center',
                                        {
                                            'bg-indigo-200 text-indigo-600 border-indigo-200':
                                                value <= activeStep,
                                            'border-surface-200 dark:border-surface-700':
                                                value > activeStep,
                                        },
                                    ]"
                                >
                                    {{
                                        //Mega ternaria para ajustar el numero de los pasos
                                        // !isEmptyObject(store.getStepData(transactionId, 1)) && store.getStepData(transactionId, 1)['is_external'] == 1 ?
                                        // (
                                        // 	index + 1 == 6 ? 4 : (
                                        // 		index + 1 == 7 ? 5 : index + 1
                                        // 	)
                                        // )
                                        // :
                                        index + 1 < 5 ||
                                        (!isEmptyObject(
                                            store.getStepData(transactionId, 1)
                                        ) &&
                                            store.getStepData(transactionId, 1)[
                                                "side"
                                            ] == "both")
                                            ? index + 1
                                            : index
                                    }}
                                </span>
                                <!--
										<i
											class="text-black"
											:class="icons[index]"
											style="font-size: 1.5rem"
										></i>
									-->
                                <div
                                    class="w-24 hidden leading-tight md:inline-block"
                                >
                                    {{ steps[index] }}
                                </div>
                            </button>
                            <Divider class="hidden md:inline-block" />
                        </div>
                    </Step>
                </StepList>

                <StepPanels>
                    <StepPanel v-slot="{ activateCallback }" :value="1">
                        <div class="flex flex-col gap-2 mx-auto">
                            <Step1></Step1>
                        </div>
                        <div class="flex pt-6 justify-end">
                            <Button
                                size="small"
                                label="Next"
                                severity="info"
                                icon="pi pi-arrow-right"
                                iconPos="right"
                                @click="
                                    async () => {
                                        if (verifyData(1)) {
                                            activateCallback(2);
                                            await sendStepInformation(1); //Se tiene que esperar para obtener correctamente el precio
                                            await changeStep();
                                        }
                                    }
                                "
                            />
                        </div>
                    </StepPanel>

                    <StepPanel v-slot="{ activateCallback }" :value="2">
                        <div class="flex flex-col gap-2 mx-auto">
                            <Step2></Step2>
                        </div>
                        <div class="flex pt-6 justify-end">
                            <Button
                                size="small"
                                class="mr-3"
                                label="Back"
                                severity="secondary"
                                icon="pi pi-arrow-left"
                                @click="activateCallback(1)"
                            />
                            <Button
                                size="small"
                                label="Next"
                                severity="info"
                                icon="pi pi-arrow-right"
                                iconPos="right"
                                @click="
                                    () => {
                                        if (verifyData(2)) {
                                            activateCallback(3);
                                            sendStepInformation(2);
                                            changeStep();
                                        }
                                    }
                                "
                            />
                        </div>
                    </StepPanel>

                    <StepPanel v-slot="{ activateCallback }" :value="3">
                        <div class="flex flex-col gap-2 mx-auto">
                            <Step3></Step3>
                        </div>
                        <div class="flex pt-6 justify-end">
                            <Button
                                size="small"
                                label="Back"
                                class="mr-3"
                                severity="secondary"
                                icon="pi pi-arrow-left"
                                @click="activateCallback(2)"
                            />
                            <Button
                                size="small"
                                label="Next"
                                severity="info"
                                icon="pi pi-arrow-right"
                                iconPos="right"
                                @click="
                                    () => {
                                        if (verifyData(3)) {
                                            sendStepInformation(3);
                                            // if (!isEmptyObject(store.getStepData(transactionId, 1)) && store.getStepData(transactionId, 1)['is_external'] == 1) {
                                            // 	enviarp3(6);
                                            // 	activateCallback(6);
                                            // } else {
                                            enviarp3(4);
                                            activateCallback(4);
                                            // }
                                            changeStep();
                                        }
                                    }
                                "
                            />
                        </div>
                    </StepPanel>
                    <StepPanel v-slot="{ activateCallback }" :value="4">
                        <div class="flex flex-col gap-2 mx-auto">
                            <Step4></Step4>
                        </div>
                        <div class="flex pt-6 justify-end">
                            <Button
                                size="small"
                                label="Back"
                                class="mr-3"
                                severity="secondary"
                                icon="pi pi-arrow-left"
                                @click="activateCallback(3)"
                            />
                            <Button
                                size="small"
                                label="Next"
                                severity="info"
                                icon="pi pi-arrow-right"
                                iconPos="right"
                                @click="
                                    async () => {
                                        if (verifyData(4)) {
                                            enviarp3(4);
                                            if (
                                                !isEmptyObject(
                                                    store.getStepData(
                                                        transactionId,
                                                        1
                                                    )
                                                ) &&
                                                store.getStepData(
                                                    transactionId,
                                                    1
                                                )['side'] == 'both'
                                            ) {
                                                activateCallback(5);
                                            } else {
                                                activateCallback(6);
                                            }
                                            await sendStepInformation(4);
                                            await changeStep();
                                        }
                                    }
                                "
                            />
                        </div>
                    </StepPanel>
                    <StepPanel
                        v-slot="{ activateCallback }"
                        :value="5"
                        v-if="
                            !isEmptyObject(
                                store.getStepData(transactionId, 1)
                            ) &&
                            store.getStepData(transactionId, 1)['side'] ==
                                'both'
                        "
                    >
                        <div class="flex flex-col gap-2 mx-auto">
                            <Step5></Step5>
                        </div>
                        <div class="flex pt-6 justify-end">
                            <Button
                                size="small"
                                label="Back"
                                class="mr-3"
                                severity="secondary"
                                icon="pi pi-arrow-left"
                                @click="activateCallback(4)"
                            />
                            <Button
                                size="small"
                                label="Next"
                                icon="pi pi-arrow-right"
                                severity="info"
                                iconPos="right"
                                @click="
                                    async () => {
                                        if (verifyData(5)) {
                                            enviarp3(4);
                                            activateCallback(6);
                                            await sendStepInformation(5);
                                            await changeStep();
                                        }
                                    }
                                "
                            />
                        </div>
                    </StepPanel>
                    <StepPanel v-slot="{ activateCallback }" :value="6">
                        <div class="flex flex-col gap-2 mx-auto">
                            <Step6></Step6>
                        </div>
                        <div class="flex pt-6 justify-end">
                            <Button
                                size="small"
                                label="Back"
                                class="mr-3"
                                severity="secondary"
                                icon="pi pi-arrow-left"
                                @click="
                                    () => {
                                        if (
                                            !isEmptyObject(
                                                store.getStepData(
                                                    transactionId,
                                                    1
                                                )
                                            ) &&
                                            store.getStepData(transactionId, 1)[
                                                'is_external'
                                            ] == 1
                                        ) {
                                            activateCallback(3);
                                        } else {
                                            if (
                                                !isEmptyObject(
                                                    store.getStepData(
                                                        transactionId,
                                                        1
                                                    )
                                                ) &&
                                                store.getStepData(
                                                    transactionId,
                                                    1
                                                )['side'] == 'both'
                                            ) {
                                                activateCallback(5);
                                            } else {
                                                activateCallback(4);
                                            }
                                        }
                                    }
                                "
                            />
                            <Button
                                size="small"
                                label="Next"
                                icon="pi pi-arrow-right"
                                severity="info"
                                iconPos="right"
                                @click="
                                    () => {
                                        if (verifyData(6)) {
                                            enviarp3(5);
                                            activateCallback(7);
                                            sendStepInformation(6);
                                            changeStep();
                                        }
                                    }
                                "
                            />
                        </div>
                    </StepPanel>
                    <StepPanel v-slot="{ activateCallback }" :value="7">
                        <div class="flex flex-col gap-2 mx-auto">
                            <Step7></Step7>
                        </div>
                        <div class="flex pt-6 justify-end">
                            <Button
                                size="small"
                                label="Back"
                                severity="secondary"
                                class="mr-3"
                                icon="pi pi-arrow-left"
                                @click="activateCallback(6)"
                            />
                            <Button
                                size="small"
                                label="Next"
                                icon="pi pi-arrow-right"
                                iconPos="right"
                                severity="info"
                                @click="
                                    async () => {
                                        if (verifyData(7)) {
                                            enviarp3(7);
                                            await sendStepInformation(7);
                                            finish();
                                        }
                                    }
                                "
                            />
                        </div>
                    </StepPanel>
                </StepPanels>
            </Stepper>
        </div>
    </AppLayout>
</template>
<script setup>
import Step1 from "@/Components/Transactions/Steps/Step1.vue";
import Step2 from "@/Components/Transactions/Steps/Step2.vue";
import Step3 from "@/Components/Transactions/Steps/Step3.vue";
import Step4 from "@/Components/Transactions/Steps/Step4.vue";
import Step5 from "@/Components/Transactions/Steps/Step5.vue";
import Step6 from "@/Components/Transactions/Steps/Step6.vue";
import Step7 from "@/Components/Transactions/Steps/Step7.vue";

import AppLayout from "@/Layouts/AppLayout.vue";
import { useTransactionStepStore } from "@/Stores/useTransactionStore";
import { computed, ref, onMounted } from "vue";
import apiClient from "@/src/constansts/axiosClient";
import moment from "moment";
import { useToast } from "primevue";
import { useIsLoadingStore } from "@/Stores/isLoadingCharts";
import { usePage, router } from "@inertiajs/vue3";
// import Route from "vendor/tightenco/ziggy/src/js/Route";

const page = usePage();
const toast = useToast();
const loadingStore = useIsLoadingStore();

const activeStep = ref(1);
const completed = ref(false);
const products = ref();
const name = ref();
const email = ref();
const password = ref();

const steps = [
    "Detalles de la Transacción",
    "Comisión Captación",
    "Pagos",
    "Comisión Ag. Comprador",
    "Pagos Ag. Comprador",
    "Compradores",
    "Financiación",
];

const transactionId = page.props.transaction_id;

const store = useTransactionStepStore();

const enviarp3 = (val) => {};

async function changeStep() {
    if (isEmptyObject(store.getStepData(transactionId, activeStep.value))) {
        loadingStore.setLoading(true);
        store.saveStepData(
            transactionId,
            activeStep.value,
            await fetchStepData(activeStep.value)
        );
        loadingStore.setLoading(false);
        //Caso de que falte el sold_date, que ponga la fecha de hoy dia
        if (activeStep.value == 1) {
            store.getStepData(transactionId, 1).sold_date =
                store.getStepData(transactionId, 1).sold_date ??
                moment(new Date()).format("DD/MM/YYYY");
            store.getStepData(transactionId, 1).sold_price =
                store.getStepData(transactionId, 1).sold_price ??
                store.getStepData(transactionId, 1).current_listing_price;
        }

        // if(activeStep.value == 5) {
        // 	store.getStepData(transactionId, 5).expected_payment_date  = moment(store.getStepData(transactionId, 5).expected_payment_date, "DD/MM/YY");
        // }
    }
}

function finish() {
    const url = "/transactions/" + transactionId + "/show";
    router.visit(url);
}

async function fetchStepData(step) {
    const response = await apiClient.get("/transactions/get-step-data", {
        params: {
            step: step,
            internal_id: transactionId,
        },
    });

    return response.data;
}

async function handleStepChange(activateCallback) {
    await activateCallback();
    changeStep();
}

async function sendStepInformation(step) {
    let data = [];

    switch (step) {
        case 2:
            data = {
                commissions: store.getStepData(transactionId, step),
                transaction_id: transactionId,
            };
            break;
        case 3:
            data = {
                payments: store.getStepData(transactionId, step),
                transaction_id: transactionId,
            };
            break;
        case 4:
            data = {
                commissions: store.getStepData(transactionId, step),
                transaction_id: transactionId,
                other_transaction: true,
            };
            break;
        case 5:
            data = {
                payments: store.getStepData(transactionId, step),
                transaction_id: transactionId,
                other_transaction: true,
            };
            break;
        case 6:
            data = {
                buyers: store.getStepData(transactionId, step),
                transaction_id: transactionId,
            };
            break;

        default:
            data = store.getStepData(transactionId, step);
            data.transaction_id = transactionId;
            break;
    }

    const routes = [
        "/transactions/update",
        "/commissions/create-update",
        "/payments/create-update",
        "/commissions/create-update",
        "/payments/create-update",
        "/listings/update-buyers",
        "/transactions/set-finantiation",
    ];
    const response = await apiClient.post(routes[step - 1], data);
}

function isEmptyObject(obj) {
    return obj && Object.keys(obj).length === 0;
}

function verifyData(step) {
    const id = transactionId;
    switch (step) {
        case 1:
            if (
                store.getStepData(id, 1).sold_price &&
                store.getStepData(id, 1).sold_date
            ) {
                return true;
            } else {
                toast.add({
                    severity: "warn",
                    summary: "Campos obligatorios",
                    detail: "Los campos de precio de venta y fecha de  venta son obligatorios",
                    life: 5000,
                });
                return false;
            }
            break;

        case 2:
            return checkCommissions(2);
            break;
        case 3:
            return checkPayments(3);
            break;
        case 4:
            return checkCommissions(4);
            break;
        case 5:
            return checkPayments(5);
            break;
        case 6:
            if (
                (store.getStepData(transactionId, 1).side == "both" ||
                    store.getStepData(transactionId, 1).side == "selling") &&
                store.getStepData(transactionId, 6).length == 0
            ) {
                toast.add({
                    severity: "warn",
                    summary: "Comprador no ingresado",
                    detail: "Ingrese un comprador para asignar a la transacción.",
                    life: 3000,
                });
                return false;
            }
            return true;
            break;
        case 7:
            if (
                (store.getStepData(transactionId, 1).side == "both" ||
                    store.getStepData(transactionId, 1).side == "selling") &&
                (isEmptyObject(store.getStepData(transactionId, 7)) ||
                    !store.getStepData(transactionId, 7).bank_id)
            ) {
                toast.add({
                    severity: "warn",
                    summary: "Financiacion no ingresada",
                    detail: "Ingrese una financiacion para asignar a la transaccion.",
                    life: 3000,
                });
                return false;
            }
            return true;
            break;
    }
}

function checkCommissions(step) {
    const id = transactionId;

    if (
        !store.getStepData(id, step) ||
        isEmptyObject(store.getStepData(id, step))
    ) {
        toast.add({
            severity: "warn",
            summary: "Sin comisiones",
            detail: "Crea almenos una comision para continuar",
            life: 3000,
        });
        return false;
    }
    const commissions = store.getStepData(id, step);
    let allOk = true;

    commissions.forEach((commission) => {
        if (
            !commission.total_commission_amount ||
            parseInt(commission.total_commission_amount) == 0
        ) {
            allOk = false;
        }
    });

    if (!allOk) {
        toast.add({
            severity: "warn",
            summary: "Comision(es) invalidas",
            detail: "EL monto de almenos una comision esinvalido",
            life: 3000,
        });
    }

    return allOk;
}

function checkPayments(step) {
    const transaction_id = transactionId;
    const total_commissions = getTotalCommissions(step == 3 ? 2 : 4);
    const total_expected_payments = getTotalPaymentExpected(step);

    let allOk = true;

    if (total_commissions != total_expected_payments) {
        toast.add({
            severity: "warn",
            summary: "Monto no coincide",
            detail: "El monto esperado no coincide con el total a pagar",
            life: 3000,
        });
        allOk = false;
    }

    const totalPaymentByAgent = calculatePaymentsTotalByAgent(
        store.getStepData(transactionId, step)
    );
    const totalCommissionsByAgent = calculateCommissionsTotalByAgent(
        step == 3
            ? store.getStepData(transactionId, 2)
            : store.getStepData(transactionId, 4)
    );

    Object.values(totalCommissionsByAgent).forEach((commission) => {
        const payment = Object.values(totalPaymentByAgent).find(
            (payment) => payment.agent_id == commission.agent_id
        );
        if (!payment) {
            allOk = false;
            toast.add({
                severity: "warn",
                summary: "Monto no coincide",
                detail:
                    "No se ha registrado ningun pago para el agente: " +
                    commission.agent_name,
                life: 3000,
            });
        } else {
            if (
                commission.total_commission_amount.toFixed(2) !=
                payment.total_amount_expected.toFixed(2)
            ) {
                allOk = false;
                toast.add({
                    severity: "warn",
                    summary: "Monto no coincide",
                    detail:
                        "El monto esperado del agente " +
                        commission.agent_name +
                        " no coincide con la comision registrada.",
                    life: 3000,
                });
            }
        }
    });

    const payments = store.getStepData(transaction_id, step);

    console.log(payments);

    Object.values(payments).forEach((item) => {
        console.log(item);
        if (
            item.amount_expected.toFixed(2) !=
                item.amount_received.toFixed(2) &&
            item.received
        ) {
            toast.add({
                severity: "warn",
                summary: "Incoherencia en montos",
                detail:
                    "El monto esperado del agente " +
                    item.agent_name +
                    " es distinto al monto recibido.",
                life: 3000,
            });
            allOk = false;
        }
    });

    return allOk;
}

const calculatePaymentsTotalByAgent = (data) => {
    if (!isEmptyObject(data)) {
        data = Array.isArray(data) ? data : Object.values(data);
    }
    return data.reduce((totals, item) => {
        if (!totals[item.agent_id]) {
            totals[item.agent_id] = {
                agent_name: item.agent_name,
                total_amount_expected: 0,
                agent_id: item.agent_id,
            };
        }
        totals[item.agent_id].total_amount_expected += item.amount_expected;
        return totals;
    }, {});
};

const calculateCommissionsTotalByAgent = (data) => {
    return data.reduce((totals, item) => {
        if (!totals[item.agent_id]) {
            totals[item.agent_id] = {
                agent_name: item.agent_name,
                total_commission_amount: 0,
                agent_id: item.agent_id,
            };
        }
        totals[item.agent_id].total_commission_amount += parseFloat(
            item.total_commission_amount
        );
        return totals;
    }, {});
};

function getTotalCommissions(step) {
    const id = transactionId;
    const commissions = store.getStepData(id, step);
    let total = 0;

    commissions.forEach((commission) => {
        total += parseFloat(commission.total_commission_amount);
    });

    return total.toFixed(2);
}

function getTotalPaymentExpected(step) {
    const id = transactionId;
    const payments = store.getStepData(id, step);

    let total = 0;
    if (!isEmptyObject(payments)) {
        const stepDataArray = Array.isArray(payments)
            ? payments
            : Object.values(payments);
        stepDataArray.forEach((payment) => {
            total += parseFloat(payment.amount_expected || 0);
        });
    }

    return total.toFixed(2);
}

onMounted(() => {
    const store = useTransactionStepStore();
    store.saveStepData(transactionId, 1, {});
    store.saveStepData(transactionId, 2, {});
    store.saveStepData(transactionId, 3, {});
    store.saveStepData(transactionId, 4, {});
    store.saveStepData(transactionId, 5, {});
    store.saveStepData(transactionId, 6, {});
    store.saveStepData(transactionId, 7, {});
    changeStep();
});
</script>

<style>
.p-steplist {
    justify-content: flex-start !important;
}
</style>
