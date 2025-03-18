<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

// アニメーション用の状態
const isLoaded = ref(false);

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});
</script>

<template>
    <GuestLayout>
        <Head title="アカウント登録" />

        <h1
            class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600"
        >
            新規アカウント登録
        </h1>

        <form @submit.prevent="submit" class="space-y-5">
            <div
                class="transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '150ms' }"
            >
                <InputLabel
                    for="name"
                    value="ユーザー名"
                    class="text-gray-700 dark:text-gray-300"
                />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="例：山田太郎"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div
                class="transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '250ms' }"
            >
                <InputLabel
                    for="email"
                    value="メールアドレス"
                    class="text-gray-700 dark:text-gray-300"
                />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="yourname@example.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div
                class="transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '350ms' }"
            >
                <InputLabel
                    for="password"
                    value="パスワード"
                    class="text-gray-700 dark:text-gray-300"
                />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div
                class="transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '450ms' }"
            >
                <InputLabel
                    for="password_confirmation"
                    value="パスワード（確認）"
                    class="text-gray-700 dark:text-gray-300"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div
                class="flex items-center justify-between pt-2 transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '550ms' }"
            >
                <Link
                    :href="route('login')"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline focus:outline-none transition-all duration-300"
                >
                    すでに登録済みの方はこちら
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-75': form.processing }"
                    :disabled="form.processing"
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 hover:shadow-lg transform hover:scale-105"
                >
                    登録する
                </PrimaryButton>
            </div>

            <div
                class="pt-4 text-center text-sm text-gray-500 dark:text-gray-400 transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '650ms' }"
            >
                登録すると、利用規約とプライバシーポリシーに同意したことになります。
            </div>
        </form>
    </GuestLayout>
</template>
