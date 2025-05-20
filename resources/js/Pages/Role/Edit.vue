

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CheckboxName from '@/Components/CheckboxName.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted } from 'vue'
import { Head, usePage, useForm, router } from '@inertiajs/vue3';

import { useToast } from "primevue/usetoast";
const { role } = usePage().props;
const { rolePermissions } = usePage().props;
const { permissions } = usePage().props;
const titulo = 'Permisos'
const toast = useToast();
onMounted(() => {
    form.id = role.id
    form.permisos = rolePermissions
});
const form = useForm({
    id: '',
    permisos: [],
})
//envio de formulario
const submit = () => {

    form.post(route('roles.update', form.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            show('success','Confirmado','Rol Editado');
            window.setTimeout(function () {
                router.get(route('roles.index'));
            }, 1000);
        },
        onFinish: () => {
        },
        onError: () => {

        }
    });

}
//check permisos
const check = (optionId, checked) => {
    if (checked) {
        rolePermissions.push(optionId);
    } else {
        rolePermissions.splice(rolePermissions.indexOf(optionId), 1);
    }
};

const show = (tipo,titulo,mensaje) => {
    toast.add({ severity: tipo, summary: titulo, detail: mensaje, life: 2000 });
};
</script>
<template>

        <Head title="Permisos" />
        <AppLayout :pagina="[{ 'label': titulo, link: false }]">
            <Toast />
            <div
                class="px-4 bg-white col-span-12 py-5 rounded-lg shadow-lg 2xl:col-span-12 dark:border-gray-700 sm:p-2 dark:bg-gray-800">
                <div class="px-5 pb-2 col-span-full flex justify-between items-center">
                    <h5 class="text-2xl font-medium">Permisos de {{ role.name }}</h5>
                </div>

                <form @submit.prevent="submit">

                    <div class="grid grid-cols-12 gap-2  mt-5 col-span-12  xl:col-span-6">
                        <div :key="permiso.id" class="col-span-12  xl:col-span-3 m-0.5 flex"
                            v-for="permiso, index in permissions">
                            <CheckboxName :checked="rolePermissions.includes(permiso.id)"
                                @update:checked="check(permiso.id, $event)" :fieldId="'permiso.id'" :label="permiso.name"
                                :key="permiso.id">
                            </CheckboxName>
                        </div>


                    </div>

                    <div class="flex justify-start pt-5">
                        <PrimaryButton
                            class="inline-block rounded bg-primary-900 p-2 text-sm font-medium text-white mr-1 mb-1 hover:bg-primary-100"
                            :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                            Guardar
                        </PrimaryButton>
                    </div>
                </form>

            </div>

        </AppLayout>
</template>


<style type="text/css" scoped></style>
