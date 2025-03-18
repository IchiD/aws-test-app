<script setup>
import { ref, onMounted, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import CustomAppLogo from "@/Components/CustomAppLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import DarkModeToggle from "@/Components/DarkModeToggle.vue";
import { Link, router } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const darkMode = ref(
    usePage().props.auth.user.dark_mode ??
        window.matchMedia("(prefers-color-scheme: dark)").matches,
);

onMounted(() => {
    applyDarkMode();
});

const applyDarkMode = () => {
    if (darkMode.value) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
};

const toggleDarkMode = async () => {
    try {
        darkMode.value = !darkMode.value;
        applyDarkMode();
        await router.patch(route("dark-mode.update"), {
            dark_mode: darkMode.value,
        });
    } catch (error) {
        // Revert the change if the update fails
        darkMode.value = !darkMode.value;
        applyDarkMode();
        console.error("Failed to update dark mode:", error);
    }
};

watch(
    () => usePage().props.auth.user.dark_mode,
    (newValue) => {
        if (newValue !== undefined) {
            darkMode.value = newValue;
            applyDarkMode();
        }
    },
);
</script>

<template>
    <div>
        <div
            class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200"
        >
            <!-- 改良されたスタイリッシュなヘッダー -->
            <nav
                class="bg-gradient-to-r from-indigo-800 via-purple-800 to-indigo-900 dark:from-gray-800 dark:via-gray-900 dark:to-black shadow-md transition-colors duration-200"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link
                                    :href="route('dashboard')"
                                    class="transition duration-300 ease-in-out hover:opacity-80"
                                >
                                    <CustomAppLogo
                                        class="block h-9 w-auto text-white"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    class="text-white hover:text-white transition-all duration-300 ease-in-out font-medium dark:text-gray-100 dark:hover:text-white"
                                    active-class="border-b-2 border-pink-400 text-white"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('tasks.index')"
                                    :active="route().current('tasks.index')"
                                    class="text-white hover:text-white transition-all duration-300 ease-in-out font-medium dark:text-gray-100 dark:hover:text-white"
                                    active-class="border-b-2 border-pink-400 text-white"
                                >
                                    タスク管理
                                </NavLink>
                                <NavLink
                                    :href="route('diary-entries.index')"
                                    :active="
                                        route().current('diary-entries.index')
                                    "
                                    class="text-white hover:text-white transition-all duration-300 ease-in-out font-medium dark:text-gray-100 dark:hover:text-white"
                                    active-class="border-b-2 border-pink-400 text-white"
                                >
                                    日記
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- ダークモードトグルボタン -->
                            <div class="mr-4">
                                <button
                                    @click="toggleDarkMode"
                                    class="ml-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-100 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                >
                                    <svg
                                        v-if="!darkMode"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-opacity-20 bg-white px-3 py-2 text-sm font-medium leading-4 text-white dark:text-gray-100 transition duration-150 ease-in-out hover:bg-opacity-30 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-200 transition duration-150 ease-in-out hover:bg-indigo-700 hover:text-white focus:bg-indigo-700 focus:text-white focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            class="text-white dark:text-gray-100 hover:bg-indigo-700"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('tasks.index')"
                            :active="route().current('tasks.index')"
                            class="text-white dark:text-gray-100 hover:bg-indigo-700"
                        >
                            タスク管理
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('diary-entries.index')"
                            :active="route().current('diary-entries.index')"
                            class="text-white dark:text-gray-100 hover:bg-indigo-700"
                        >
                            日記
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-indigo-700 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-white">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-indigo-200">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('profile.edit')"
                                class="text-white dark:text-gray-100 hover:bg-indigo-700"
                            >
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="text-white dark:text-gray-100 hover:bg-indigo-700"
                            >
                                Log Out
                            </ResponsiveNavLink>
                            <!-- モバイル用ダークモードトグル -->
                            <div class="flex items-center px-4 py-2">
                                <span
                                    class="text-sm font-medium text-white mr-2"
                                    >ダークモード</span
                                >
                                <button
                                    @click="toggleDarkMode"
                                    class="ml-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-100 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                >
                                    <svg
                                        v-if="!darkMode"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white dark:bg-gray-800 shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
