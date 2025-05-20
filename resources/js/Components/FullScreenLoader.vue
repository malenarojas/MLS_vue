<template>
    <transition>
        <div
            class="fixed inset-0 z-[99] flex flex-col items-center justify-center bg-white bg-opacity-10 backdrop-blur-sm"
        >
            <div class="relative w-28 h-28">
                <div class="absolute inset-0 animate-spin-slow">
                    <div
                        class="loader-ring border-t-remax border-r-transparent"
                    ></div>
                </div>
                <div class="absolute inset-0 animate-spin-fast">
                    <div
                        class="loader-ring border-l-red-600 border-b-transparent"
                    ></div>
                </div>
                <img
                    :src="logo"
                    alt="Remax Logo"
                    class="w-12 h-auto absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-80 drop-shadow-md"
                />
            </div>
            <p class="mt-4 text-sm text-gray-600 animate-pulse">Cargando...</p>
        </div>
    </transition>
</template>

<script setup>
import logo from "@/assets/img/logo-remax-balloon.svg";
</script>

<style scoped>
.loader-ring {
    width: 100%;
    height: 100%;
    border-width: 6px;
    border-style: solid;
    border-radius: 9999px;
    box-sizing: border-box;
}
@keyframes spin-slow {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
@keyframes spin-fast {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(-360deg);
    }
}
.animate-spin-slow {
    animation: spin-slow 1.2s linear infinite;
}
.animate-spin-fast {
    animation: spin-fast 0.8s linear infinite;
}
.border-t-remax {
    border-top-color: #004084; /* Azul Remax */
}
.border-l-red-600 {
    border-left-color: #d11b2b; /* Rojo Remax */
}
</style>
