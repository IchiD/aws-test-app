<script setup>
import { ref, onMounted, watch } from "vue";

const darkMode = ref(false);

// ページ読み込み時にダークモード設定を適用
onMounted(() => {
    // ローカルストレージから設定を読み込む
    const savedMode = localStorage.getItem("darkMode");
    darkMode.value = savedMode === "true";

    // 初期設定を適用
    applyDarkMode();
});

// ダークモード設定の変更を監視して適用
watch(darkMode, () => {
    applyDarkMode();
    // 設定をローカルストレージに保存
    localStorage.setItem("darkMode", darkMode.value);
});

// ダークモードの適用処理
const applyDarkMode = () => {
    if (darkMode.value) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
};

// トグル関数
const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
};
</script>

<template>
    <button
        @click="toggleDarkMode"
        class="flex items-center justify-center rounded-full p-2 transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-700"
        :title="darkMode ? 'ライトモードに切り替え' : 'ダークモードに切り替え'"
    >
        <!-- 太陽アイコン（ライトモード時に表示） -->
        <svg
            v-if="!darkMode"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-5 w-5 text-yellow-500"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
            />
        </svg>

        <!-- 月アイコン（ダークモード時に表示） -->
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-5 w-5 text-indigo-200"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
            />
        </svg>
    </button>
</template>
