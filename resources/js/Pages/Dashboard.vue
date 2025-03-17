<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";

const props = defineProps({
    recentTasks: Array,
    upcomingTasks: Array,
    recentEntries: Array,
    entriesByMonth: Array,
    taskStats: Object,
});

// アニメーション用の状態
const isLoaded = ref(false);

// ページ読み込み時にアニメーションを開始
onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});

// 完了・未完了タスクの合計
const totalTasks = computed(() => {
    return props.taskStats.completed + props.taskStats.incomplete;
});

// タスク完了率
const completionRate = computed(() => {
    if (totalTasks.value === 0) return 0;
    return Math.round((props.taskStats.completed / totalTasks.value) * 100);
});

// 日付のフォーマット関数
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("ja-JP", {
        year: "numeric",
        month: "long",
        day: "numeric",
        weekday: "short",
    }).format(date);
};

// 期限日の表示（相対時間または日付）
const formatDueDate = (dateString) => {
    if (!dateString) return "";

    const dueDate = new Date(dateString);
    const now = new Date();
    const diffDays = Math.round((dueDate - now) / (1000 * 60 * 60 * 24));

    if (diffDays < 0) {
        return `${Math.abs(diffDays)}日経過`;
    } else if (diffDays === 0) {
        return "今日まで";
    } else if (diffDays === 1) {
        return "明日まで";
    } else if (diffDays < 7) {
        return `${diffDays}日以内`;
    } else {
        return formatDate(dateString);
    }
};

// 月表示のフォーマット
const formatMonth = (monthString) => {
    const [year, month] = monthString.split("-");
    return `${year}年${month}月`;
};

// 短い内容のプレビュー表示
const truncate = (text, length = 50) => {
    if (!text) return "";
    return text.length > length ? text.substring(0, length) + "..." : text;
};
</script>

<template>
    <div>
        <Head title="Dashboard" />

        <AuthenticatedLayout>
            <template #header>
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    Dashboard
                </h2>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- タスク概要セクション -->
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 transition-all duration-500 transform"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        :style="{ transitionDelay: '100ms' }"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4"
                            >
                                タスク概要
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg"
                                >
                                    <h4
                                        class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1"
                                    >
                                        未完了タスク
                                    </h4>
                                    <p
                                        class="text-2xl font-bold text-blue-800 dark:text-blue-200"
                                    >
                                        {{ props.taskStats.incomplete }}
                                    </p>
                                </div>
                                <div
                                    class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg"
                                >
                                    <h4
                                        class="text-sm font-medium text-green-700 dark:text-green-300 mb-1"
                                    >
                                        完了タスク
                                    </h4>
                                    <p
                                        class="text-2xl font-bold text-green-800 dark:text-green-200"
                                    >
                                        {{ props.taskStats.completed }}
                                    </p>
                                </div>
                                <div
                                    class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg"
                                >
                                    <h4
                                        class="text-sm font-medium text-purple-700 dark:text-purple-300 mb-1"
                                    >
                                        合計タスク
                                    </h4>
                                    <p
                                        class="text-2xl font-bold text-purple-800 dark:text-purple-200"
                                    >
                                        {{ totalTasks }}
                                    </p>
                                </div>
                            </div>

                            <!-- 完了率プログレスバー -->
                            <div class="mt-6">
                                <div class="flex justify-between mb-1">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        タスク完了率
                                    </span>
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        {{ completionRate }}%
                                    </span>
                                </div>
                                <div
                                    class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5"
                                >
                                    <div
                                        class="bg-indigo-600 dark:bg-indigo-400 h-2.5 rounded-full transition-all duration-1000"
                                        :style="{ width: `${completionRate}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- メイングリッドレイアウト -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 左カラム：タスクデータ -->
                        <div class="space-y-6">
                            <!-- 期限が近いタスク -->
                            <div
                                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-all duration-500 transform"
                                :class="{
                                    'translate-y-0 opacity-100': isLoaded,
                                    'translate-y-4 opacity-0': !isLoaded,
                                }"
                                :style="{ transitionDelay: '300ms' }"
                            >
                                <div class="p-6">
                                    <h3
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex justify-between items-center"
                                    >
                                        <span>期限が近いタスク</span>
                                        <Link
                                            :href="route('tasks.index')"
                                            class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300"
                                            >すべて表示</Link
                                        >
                                    </h3>

                                    <div
                                        v-if="upcomingTasks.length > 0"
                                        class="space-y-3"
                                    >
                                        <div
                                            v-for="(
                                                task, index
                                            ) in upcomingTasks"
                                            :key="task.id"
                                            class="border-b border-gray-200 dark:border-gray-700 last:border-b-0 pb-3 last:pb-0 transition-all duration-300"
                                            :style="{
                                                transitionDelay: `${400 + index * 100}ms`,
                                            }"
                                        >
                                            <div
                                                class="flex justify-between items-start"
                                            >
                                                <div>
                                                    <h4
                                                        class="font-medium text-gray-900 dark:text-gray-100"
                                                    >
                                                        {{ task.title }}
                                                    </h4>
                                                    <p
                                                        v-if="task.description"
                                                        class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                                                    >
                                                        {{ task.description }}
                                                    </p>
                                                </div>

                                                <!-- 期限表示 -->
                                                <div>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                        :class="
                                                            task.is_overdue
                                                                ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200'
                                                                : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200'
                                                        "
                                                    >
                                                        {{
                                                            formatDueDate(
                                                                task.due_date,
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-else
                                        class="text-gray-500 dark:text-gray-400 italic text-center py-4"
                                    >
                                        期限が近いタスクはありません
                                    </div>
                                </div>
                            </div>

                            <!-- 最近追加されたタスク -->
                            <div
                                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-all duration-500 transform"
                                :class="{
                                    'translate-y-0 opacity-100': isLoaded,
                                    'translate-y-4 opacity-0': !isLoaded,
                                }"
                                :style="{ transitionDelay: '500ms' }"
                            >
                                <div class="p-6">
                                    <h3
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4"
                                    >
                                        最近追加されたタスク
                                    </h3>

                                    <div
                                        v-if="recentTasks.length > 0"
                                        class="space-y-3"
                                    >
                                        <div
                                            v-for="(task, index) in recentTasks"
                                            :key="task.id"
                                            class="flex items-center border-b border-gray-200 dark:border-gray-700 last:border-b-0 pb-3 last:pb-0 transition-all duration-300"
                                            :style="{
                                                transitionDelay: `${600 + index * 100}ms`,
                                            }"
                                        >
                                            <div
                                                class="h-3 w-3 rounded-full mr-3"
                                                :class="
                                                    task.completed
                                                        ? 'bg-green-500'
                                                        : 'bg-yellow-500'
                                                "
                                            ></div>
                                            <div class="flex-grow">
                                                <h4
                                                    class="font-medium text-gray-900 dark:text-gray-100"
                                                >
                                                    {{ task.title }}
                                                </h4>
                                                <p
                                                    v-if="task.description"
                                                    class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                                                >
                                                    {{ task.description }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-else
                                        class="text-gray-500 dark:text-gray-400 italic text-center py-4"
                                    >
                                        まだタスクがありません
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 右カラム：日記データ -->
                        <div class="space-y-6">
                            <!-- 最近の日記 -->
                            <div
                                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-all duration-500 transform"
                                :class="{
                                    'translate-y-0 opacity-100': isLoaded,
                                    'translate-y-4 opacity-0': !isLoaded,
                                }"
                                :style="{ transitionDelay: '400ms' }"
                            >
                                <div class="p-6">
                                    <h3
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex justify-between items-center"
                                    >
                                        <span>最近の日記</span>
                                        <Link
                                            :href="route('diary-entries.index')"
                                            class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300"
                                            >すべて表示</Link
                                        >
                                    </h3>

                                    <div
                                        v-if="recentEntries.length > 0"
                                        class="space-y-3"
                                    >
                                        <div
                                            v-for="(
                                                entry, index
                                            ) in recentEntries"
                                            :key="entry.id"
                                            class="border-b border-gray-200 dark:border-gray-700 last:border-b-0 pb-3 last:pb-0 transition-all duration-300"
                                            :style="{
                                                transitionDelay: `${500 + index * 100}ms`,
                                            }"
                                        >
                                            <div
                                                class="flex justify-between items-start"
                                            >
                                                <h4
                                                    class="font-medium text-gray-900 dark:text-gray-100"
                                                >
                                                    {{
                                                        formatDate(
                                                            entry.entry_date,
                                                        )
                                                    }}
                                                    <span
                                                        v-if="entry.mood"
                                                        class="ml-2 text-sm"
                                                    >
                                                        {{
                                                            entry.mood ===
                                                            "good"
                                                                ? "😊"
                                                                : entry.mood ===
                                                                    "neutral"
                                                                  ? "😐"
                                                                  : "😢"
                                                        }}
                                                    </span>
                                                </h4>
                                            </div>
                                            <p
                                                class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2"
                                            >
                                                {{ entry.content }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-else
                                        class="text-gray-500 dark:text-gray-400 italic text-center py-4"
                                    >
                                        日記がまだありません
                                    </div>
                                </div>
                            </div>

                            <!-- 月別の記録数 -->
                            <div
                                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-all duration-500 transform"
                                :class="{
                                    'translate-y-0 opacity-100': isLoaded,
                                    'translate-y-4 opacity-0': !isLoaded,
                                }"
                                :style="{ transitionDelay: '600ms' }"
                            >
                                <div class="p-6">
                                    <h3
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4"
                                    >
                                        月別の記録数
                                    </h3>
                                    <div
                                        v-if="entriesByMonth.length > 0"
                                        class="mt-4"
                                    >
                                        <div
                                            class="flex items-end h-40 space-x-2"
                                        >
                                            <div
                                                v-for="(
                                                    monthData, index
                                                ) in entriesByMonth"
                                                :key="monthData.month"
                                                class="flex flex-col items-center flex-1"
                                            >
                                                <div
                                                    class="w-full bg-indigo-500 dark:bg-indigo-600 rounded-t transition-all duration-1000"
                                                    :style="{
                                                        height: isLoaded
                                                            ? `${(monthData.count / Math.max(...entriesByMonth.map((m) => m.count))) * 120}px`
                                                            : '0px',
                                                        transitionDelay: `${700 + index * 100}ms`,
                                                    }"
                                                ></div>
                                                <div
                                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1"
                                                >
                                                    {{ monthData.month }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500 dark:text-gray-500"
                                                >
                                                    {{ monthData.count }}件
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="text-gray-500 dark:text-gray-400 italic text-center py-4"
                                    >
                                        データがありません
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- クイックアクションカード -->
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6 transition-all duration-500 transform"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        :style="{ transitionDelay: '700ms' }"
                    >
                        <Link
                            :href="route('tasks.index')"
                            class="bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-700 dark:to-purple-900 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-all duration-300 hover:scale-105"
                        >
                            <h3 class="text-xl font-bold mb-2">タスク管理</h3>
                            <p class="text-indigo-100 dark:text-indigo-200">
                                タスクの作成・編集・管理を行います
                            </p>
                        </Link>

                        <Link
                            :href="route('diary-entries.index')"
                            class="bg-gradient-to-br from-blue-500 to-teal-500 dark:from-blue-700 dark:to-teal-700 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-all duration-300 hover:scale-105"
                        >
                            <h3 class="text-xl font-bold mb-2">日記を書く</h3>
                            <p class="text-blue-100 dark:text-blue-200">
                                今日の出来事や感情を記録します
                            </p>
                        </Link>

                        <Link
                            :href="route('profile.edit')"
                            class="bg-gradient-to-br from-pink-500 to-red-500 dark:from-pink-700 dark:to-red-700 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-all duration-300 hover:scale-105"
                        >
                            <h3 class="text-xl font-bold mb-2">プロフィール</h3>
                            <p class="text-pink-100 dark:text-pink-200">
                                アカウント情報の確認・編集を行います
                            </p>
                        </Link>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>
