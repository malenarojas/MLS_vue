<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import TextInput from "@/Components/Common/TextInput.vue";
// import Checkbox from "@/Components/Checkbox.vue";
// import InputError from "@/Components/InputError.vue";
// import InputLabel from "@/Components/InputLabel.vue";
// import PrimaryButton from "@/Components/PrimaryButton.vue";
// import TextInput from "@/Components/TextInput.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    username: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- <pre>{{ form.errors }}</pre> -->

        <div
            v-if="status"
            class="mb-4 font-medium text-sm text-green-600 dark:text-green-400"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <TextInput
                    id="username"
                    v-model="form.username"
                    label="Usuario"
                    type="text"
                    class="w-full"
                    size="large"
                    placeholder="Ingrese su usuario"
                    :error="form.errors.username || form.errors.email || null"
                    required
                />
            </div>

            <div class="mt-4">
                <TextInput
                    id="password"
                    type-input="password"
                    v-model="form.password"
                    label="Contraseña"
                    placeholder="Ingrese su contraseña"
                    class="w-full"
                    size="large"
                    required
                />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >Recuerdame</span
                    >
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Olvidaste tu contraseña?
                </Link>

                <Button
                    type="submit"
                    severity="info"
                    class="ms-4 w-2/3 sm:w-1/3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Ingresar
                </Button>

                <div v-if="form.hasErrors" class="mt-2 text-red-600 text-sm">
                    {{ form.errors.username || form.errors.email || form.errors.password }}
                </div>

            </div>
        </form>
    </AuthenticationCard>
</template>
