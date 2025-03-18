<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
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
        <Head title="ログイン" />

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400 transition-all duration-300 transform translate-y-0 opacity-100"
        >
            {{ status }}
        </div>

        <h1
            class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600"
        >
            ログイン
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
                    autofocus
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
                :style="{ transitionDelay: '250ms' }"
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
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div
                class="block transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '350ms' }"
            >
                <label class="flex items-center">
                    <Checkbox
                        name="remember"
                        v-model="form.remember"
                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >ログイン状態を保存</span
                    >
                </label>
            </div>

            <div
                class="flex items-center justify-between transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '450ms' }"
            >
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline focus:outline-none transition-all duration-300"
                >
                    パスワードをお忘れですか？
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-75': form.processing }"
                    :disabled="form.processing"
                    class="ml-auto bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 hover:shadow-lg transform hover:scale-105"
                >
                    ログイン
                </PrimaryButton>
            </div>

            <div
                class="pt-4 text-center text-sm text-gray-600 dark:text-gray-400 transition-all duration-500 transform"
                :class="{
                    'opacity-100 translate-y-0': isLoaded,
                    'opacity-0 translate-y-4': !isLoaded,
                }"
                :style="{ transitionDelay: '550ms' }"
            >
                アカウントをお持ちでない場合は、
                <Link
                    :href="route('register')"
                    class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline transition-colors duration-300"
                >
                    新規登録
                </Link>
                してください。
            </div>
        </form>
    </GuestLayout>
</template>
