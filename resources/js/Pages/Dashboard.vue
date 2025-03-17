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
        <Head title="ダッシュボード" />

        <AuthenticatedLayout>
            <template #header>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    ダッシュボード
                </h2>
            </template>

            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <!-- タスク概要カード -->
                    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div
                            :class="{ 'animate-slide-in-left': isLoaded }"
                            class="overflow-hidden bg-white opacity-0 shadow-sm sm:rounded-lg transition-all duration-500 transform"
                            :style="{
                                transitionDelay: '100ms',
                                opacity: isLoaded ? 1 : 0,
                            }"
                        >
                            <div class="border-b border-gray-200 bg-white p-6">
                                <h3
                                    class="mb-4 text-lg font-medium text-gray-900"
                                >
                                    タスク概要
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <div
                                            class="text-3xl font-bold text-indigo-600 transition-all"
                                            :class="{
                                                'animate-bounce-in': isLoaded,
                                            }"
                                        >
                                            {{ taskStats.incomplete }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            未完了
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="text-3xl font-bold text-green-600 transition-all"
                                            :class="{
                                                'animate-bounce-in': isLoaded,
                                            }"
                                            style="transition-delay: 200ms"
                                        >
                                            {{ taskStats.completed }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            完了済み
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="text-3xl font-bold text-blue-600 transition-all"
                                            :class="{
                                                'animate-bounce-in': isLoaded,
                                            }"
                                            style="transition-delay: 300ms"
                                        >
                                            {{ totalTasks }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            合計
                                        </div>
                                    </div>
                                </div>

                                <!-- タスク完了率 -->
                                <div class="mt-6">
                                    <div class="mb-1 flex justify-between">
                                        <span
                                            class="text-sm font-medium text-gray-700"
                                            >完了率</span
                                        >
                                        <span
                                            class="text-sm font-medium text-gray-700"
                                            >{{ completionRate }}%</span
                                        >
                                    </div>
                                    <div
                                        class="h-2 overflow-hidden rounded-full bg-gray-200"
                                    >
                                        <div
                                            class="h-full bg-green-500 transition-all duration-1000 ease-out"
                                            :style="{
                                                width: isLoaded
                                                    ? `${completionRate}%`
                                                    : '0%',
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 期限近いタスク -->
                        <div
                            :class="{ 'animate-slide-in-left': isLoaded }"
                            class="overflow-hidden bg-white opacity-0 shadow-sm sm:rounded-lg transition-all duration-500 transform"
                            :style="{
                                transitionDelay: '200ms',
                                opacity: isLoaded ? 1 : 0,
                            }"
                        >
                            <div class="border-b border-gray-200 bg-white p-6">
                                <div class="mb-4 flex justify-between">
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        期限が近いタスク
                                    </h3>
                                    <Link
                                        :href="route('tasks.index')"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors"
                                        >すべて見る</Link
                                    >
                                </div>
                                <div
                                    v-if="upcomingTasks.length"
                                    class="space-y-3"
                                >
                                    <div
                                        v-for="(task, index) in upcomingTasks"
                                        :key="task.id"
                                        class="flex items-start justify-between border-b border-gray-100 pb-2 opacity-0 transition-all duration-300"
                                        :class="{ 'animate-fade-in': isLoaded }"
                                        :style="{
                                            transitionDelay: `${300 + index * 100}ms`,
                                            opacity: isLoaded ? 1 : 0,
                                        }"
                                    >
                                        <div>
                                            <div class="font-medium">
                                                {{ task.title }}
                                            </div>
                                            <div
                                                class="mt-1 text-sm text-gray-500"
                                            >
                                                <span
                                                    :class="{
                                                        'text-red-500':
                                                            new Date(
                                                                task.due_date,
                                                            ) < new Date(),
                                                        'text-orange-500':
                                                            new Date(
                                                                task.due_date,
                                                            ) <=
                                                                new Date(
                                                                    new Date().setDate(
                                                                        new Date().getDate() +
                                                                            1,
                                                                    ),
                                                                ) &&
                                                            new Date(
                                                                task.due_date,
                                                            ) >= new Date(),
                                                    }"
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
                                    class="py-4 text-center text-gray-500"
                                >
                                    期限が設定されたタスクはありません
                                </div>
                            </div>
                        </div>

                        <!-- 最近の日記 -->
                        <div
                            :class="{ 'animate-slide-in-left': isLoaded }"
                            class="overflow-hidden bg-white opacity-0 shadow-sm sm:rounded-lg transition-all duration-500 transform"
                            :style="{
                                transitionDelay: '300ms',
                                opacity: isLoaded ? 1 : 0,
                            }"
                        >
                            <div class="border-b border-gray-200 bg-white p-6">
                                <div class="mb-4 flex justify-between">
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        最近の日記
                                    </h3>
                                    <Link
                                        :href="route('diary-entries.index')"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors"
                                        >すべて見る</Link
                                    >
                                </div>
                                <div
                                    v-if="recentEntries.length"
                                    class="space-y-3"
                                >
                                    <div
                                        v-for="(entry, index) in recentEntries"
                                        :key="entry.id"
                                        class="border-b border-gray-100 pb-2 opacity-0 transition-all duration-300"
                                        :class="{ 'animate-fade-in': isLoaded }"
                                        :style="{
                                            transitionDelay: `${300 + index * 100}ms`,
                                            opacity: isLoaded ? 1 : 0,
                                        }"
                                    >
                                        <div class="flex justify-between">
                                            <div class="text-sm text-gray-500">
                                                {{
                                                    formatDate(entry.entry_date)
                                                }}
                                            </div>
                                            <div>{{ entry.mood }}</div>
                                        </div>
                                        <div class="mt-1 text-sm">
                                            {{ truncate(entry.content) }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="py-4 text-center text-gray-500"
                                >
                                    日記がまだありません
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 下部セクション：最近のタスクと日記の月間統計 -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- 最近のタスク -->
                        <div
                            :class="{ 'animate-slide-up': isLoaded }"
                            class="overflow-hidden bg-white opacity-0 shadow-sm sm:rounded-lg transition-all duration-500 transform"
                            :style="{
                                transitionDelay: '400ms',
                                opacity: isLoaded ? 1 : 0,
                            }"
                        >
                            <div class="border-b border-gray-200 bg-white p-6">
                                <div class="mb-4 flex justify-between">
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        最近追加したタスク
                                    </h3>
                                    <Link
                                        :href="route('tasks.index')"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors"
                                        >すべて見る</Link
                                    >
                                </div>
                                <div
                                    v-if="recentTasks.length"
                                    class="divide-y divide-gray-100"
                                >
                                    <div
                                        v-for="(task, index) in recentTasks"
                                        :key="task.id"
                                        class="py-3 opacity-0 transition-all duration-300"
                                        :class="{ 'animate-fade-in': isLoaded }"
                                        :style="{
                                            transitionDelay: `${500 + index * 100}ms`,
                                            opacity: isLoaded ? 1 : 0,
                                        }"
                                    >
                                        <div class="flex items-center">
                                            <div
                                                class="mr-3 h-3 w-3 rounded-full transition-all"
                                                :class="{
                                                    'bg-green-500':
                                                        task.completed,
                                                    'bg-yellow-500':
                                                        !task.completed,
                                                }"
                                            ></div>
                                            <div
                                                :class="{
                                                    'line-through text-gray-400':
                                                        task.completed,
                                                }"
                                            >
                                                {{ task.title }}
                                            </div>
                                        </div>
                                        <div
                                            v-if="task.description"
                                            class="mt-1 pl-6 text-sm text-gray-500"
                                        >
                                            {{ truncate(task.description, 40) }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="py-4 text-center text-gray-500"
                                >
                                    タスクがまだありません
                                </div>
                            </div>
                        </div>

                        <!-- 月間の日記統計 -->
                        <div
                            :class="{ 'animate-slide-up': isLoaded }"
                            class="overflow-hidden bg-white opacity-0 shadow-sm sm:rounded-lg transition-all duration-500 transform"
                            :style="{
                                transitionDelay: '500ms',
                                opacity: isLoaded ? 1 : 0,
                            }"
                        >
                            <div class="border-b border-gray-200 bg-white p-6">
                                <h3
                                    class="mb-4 text-lg font-medium text-gray-900"
                                >
                                    月別の記録数
                                </h3>
                                <div
                                    v-if="entriesByMonth.length"
                                    class="space-y-4"
                                >
                                    <div
                                        v-for="(
                                            monthData, index
                                        ) in entriesByMonth"
                                        :key="monthData.month"
                                        class="flex items-center opacity-0 transition-all duration-500"
                                        :class="{ 'animate-fade-in': isLoaded }"
                                        :style="{
                                            transitionDelay: `${600 + index * 100}ms`,
                                            opacity: isLoaded ? 1 : 0,
                                        }"
                                    >
                                        <div class="w-28 font-medium">
                                            {{ formatMonth(monthData.month) }}
                                        </div>
                                        <div class="flex-1">
                                            <div
                                                class="h-6 overflow-hidden rounded-full bg-gray-100"
                                            >
                                                <div
                                                    class="h-full bg-indigo-500 transition-all duration-1000 ease-out"
                                                    :style="{
                                                        width: isLoaded
                                                            ? `${Math.min(
                                                                  100,
                                                                  monthData.count *
                                                                      5,
                                                              )}%`
                                                            : '0%',
                                                    }"
                                                ></div>
                                            </div>
                                        </div>
                                        <div class="ml-3 w-10 text-right">
                                            {{ monthData.count }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="py-4 text-center text-gray-500"
                                >
                                    日記の記録がまだありません
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- クイックリンク -->
                    <div
                        :class="{ 'animate-slide-up': isLoaded }"
                        class="mt-6 overflow-hidden bg-white opacity-0 shadow-sm sm:rounded-lg transition-all duration-500 transform"
                        :style="{
                            transitionDelay: '600ms',
                            opacity: isLoaded ? 1 : 0,
                        }"
                    >
                        <div class="border-b border-gray-200 bg-white p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">
                                クイックアクション
                            </h3>
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                                <Link
                                    :href="route('tasks.index')"
                                    class="flex items-center justify-center rounded-lg bg-indigo-50 p-4 text-center transition-all duration-300 hover:bg-indigo-100 hover:shadow-md hover:scale-105"
                                >
                                    <div>
                                        <div
                                            class="mb-2 text-3xl text-indigo-500"
                                        >
                                            📝
                                        </div>
                                        <div class="font-medium text-gray-800">
                                            タスク管理
                                        </div>
                                    </div>
                                </Link>
                                <Link
                                    :href="route('diary-entries.index')"
                                    class="flex items-center justify-center rounded-lg bg-green-50 p-4 text-center transition-all duration-300 hover:bg-green-100 hover:shadow-md hover:scale-105"
                                >
                                    <div>
                                        <div
                                            class="mb-2 text-3xl text-green-500"
                                        >
                                            📔
                                        </div>
                                        <div class="font-medium text-gray-800">
                                            日記を書く
                                        </div>
                                    </div>
                                </Link>
                                <Link
                                    :href="route('profile.edit')"
                                    class="flex items-center justify-center rounded-lg bg-orange-50 p-4 text-center transition-all duration-300 hover:bg-orange-100 hover:shadow-md hover:scale-105"
                                >
                                    <div>
                                        <div
                                            class="mb-2 text-3xl text-orange-500"
                                        >
                                            👤
                                        </div>
                                        <div class="font-medium text-gray-800">
                                            プロフィール
                                        </div>
                                    </div>
                                </Link>
                                <div
                                    class="flex items-center justify-center rounded-lg bg-blue-50 p-4 text-center transition-all duration-300 hover:bg-blue-100 hover:shadow-md hover:scale-105"
                                >
                                    <div>
                                        <div
                                            class="mb-2 text-3xl text-blue-500"
                                        >
                                            📊
                                        </div>
                                        <div class="font-medium text-gray-800">
                                            今日のログイン
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{
                                                new Date().toLocaleDateString(
                                                    "ja-JP",
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>
