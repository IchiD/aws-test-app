<script setup>
import CustomAppLogo from "@/Components/CustomAppLogo.vue";
import { Link } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

// ダークモード管理
const darkMode = ref(false);

onMounted(() => {
    // システムの設定やユーザーの設定に基づいてダークモードを初期化
    const prefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)",
    ).matches;
    const user = usePage().props.auth?.user;
    darkMode.value = user?.dark_mode ?? prefersDark;
    applyDarkMode();
});

const applyDarkMode = () => {
    if (darkMode.value) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
};

watch(darkMode, () => {
    applyDarkMode();
});
</script>

<template>
    <div
        class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 transition-all duration-300"
    >
        <div class="w-full max-w-md px-6 py-8">
            <div class="text-center mb-8">
                <Link
                    href="/"
                    class="inline-block transform transition-transform duration-300 hover:scale-105"
                >
                    <CustomAppLogo class="mx-auto" />
                </Link>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden transition-all duration-300 transform hover:shadow-2xl"
            >
                <div
                    class="border-t-4 border-indigo-500 dark:border-indigo-600"
                ></div>
                <div class="p-8">
                    <slot />
                </div>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    &copy; {{ new Date().getFullYear() }} DiaryTasks -
                    あなたの日常をサポート
                </p>
            </div>
        </div>
    </div>
</template>
