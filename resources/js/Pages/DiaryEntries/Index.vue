<template>
    <div>
        <Head title="日記" />

        <AuthenticatedLayout>
            <template #header>
                <div class="flex justify-between items-center">
                    <h2
                        class="font-semibold text-xl text-gray-800 leading-tight"
                    >
                        日記
                    </h2>
                    <button
                        @click="showNewEntryForm = !showNewEntryForm"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <span v-if="!showNewEntryForm">日記を書く</span>
                        <span v-else>フォームを閉じる</span>
                    </button>
                </div>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- 新規エントリーフォーム -->
                    <div
                        v-show="showNewEntryForm"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6"
                    >
                        <h3 class="text-lg font-semibold mb-4">新規作成</h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label
                                    for="content"
                                    class="block text-sm font-medium text-gray-700"
                                    >内容</label
                                >
                                <textarea
                                    v-model="form.content"
                                    id="content"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    for="mood"
                                    class="block text-sm font-medium text-gray-700"
                                    >気分</label
                                >
                                <select
                                    v-model="form.mood"
                                    id="mood"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">選択してください</option>
                                    <option value="😊 良い">😊 良い</option>
                                    <option value="😐 普通">😐 普通</option>
                                    <option value="😢 悪い">😢 悪い</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    for="entry_date"
                                    class="block text-sm font-medium text-gray-700"
                                    >日付</label
                                >
                                <input
                                    type="date"
                                    v-model="form.entry_date"
                                    id="entry_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >タグ</label
                                >
                                <div class="mt-1 flex flex-wrap gap-2">
                                    <button
                                        v-for="tag in availableTags"
                                        :key="tag"
                                        type="button"
                                        @click="toggleTag(tag)"
                                        :class="[
                                            'px-3 py-1 rounded-full text-sm',
                                            form.tags.includes(tag)
                                                ? 'bg-indigo-600 text-white'
                                                : 'bg-gray-200 text-gray-700',
                                        ]"
                                    >
                                        {{ tag }}
                                    </button>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    保存
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- フィルターと検索 -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6"
                    >
                        <div class="flex flex-wrap gap-4 items-center">
                            <!-- 年月選択 -->
                            <div class="flex gap-2">
                                <select
                                    v-model="filters.year"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="filterByDate"
                                >
                                    <option value="">年を選択</option>
                                    <option
                                        v-for="year in availableYears"
                                        :key="year"
                                        :value="year"
                                    >
                                        {{ year }}年
                                    </option>
                                </select>
                                <select
                                    v-model="filters.month"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="filterByDate"
                                >
                                    <option value="">月を選択</option>
                                    <option
                                        v-for="month in 12"
                                        :key="month"
                                        :value="
                                            month.toString().padStart(2, '0')
                                        "
                                    >
                                        {{ month }}月
                                    </option>
                                </select>
                            </div>

                            <!-- 検索ボックス -->
                            <div class="flex-grow">
                                <input
                                    type="text"
                                    v-model="filters.search"
                                    placeholder="日記を検索..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @input="debouncedSearch"
                                />
                            </div>

                            <!-- タグフィルター -->
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="tag in availableTags"
                                    :key="tag"
                                    @click="toggleTagFilter(tag)"
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm',
                                        filters.tags.includes(tag)
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-200 text-gray-700',
                                    ]"
                                >
                                    {{ tag }}
                                </button>
                            </div>

                            <!-- 気分フィルター -->
                            <div class="flex gap-2">
                                <button
                                    v-for="stat in moodStats"
                                    :key="stat.mood"
                                    @click="toggleMoodFilter(stat.mood)"
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm flex items-center gap-2',
                                        filters.mood === stat.mood
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-200 text-gray-700',
                                    ]"
                                >
                                    <span>{{ stat.mood }}</span>
                                    <span
                                        class="bg-white bg-opacity-20 px-2 rounded-full"
                                    >
                                        {{ stat.count }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- エントリー一覧 -->
                    <div class="space-y-6">
                        <div
                            v-for="(monthEntries, month) in filteredEntries"
                            :key="month"
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                        >
                            <div
                                class="p-4 bg-gray-50 border-b border-gray-200"
                            >
                                <h3 class="text-lg font-semibold">
                                    {{ formatMonthYear(month) }}
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <div
                                    v-for="entry in monthEntries"
                                    :key="entry.id"
                                    class="p-6 hover:bg-gray-50 transition-colors duration-150"
                                >
                                    <div
                                        class="flex justify-between items-start mb-2"
                                    >
                                        <div class="text-sm text-gray-500">
                                            {{ formatDate(entry.entry_date) }}
                                        </div>
                                        <div class="text-xl">
                                            {{ entry.mood }}
                                        </div>
                                    </div>
                                    <div class="mb-4 whitespace-pre-wrap">
                                        {{ entry.content }}
                                    </div>
                                    <div
                                        v-if="entry.tags && entry.tags.length"
                                        class="flex flex-wrap gap-2 mb-4"
                                    >
                                        <span
                                            v-for="tag in entry.tags"
                                            :key="tag"
                                            class="px-2 py-1 bg-gray-100 text-gray-700 text-sm rounded-full"
                                        >
                                            {{ tag }}
                                        </span>
                                    </div>
                                    <div class="flex justify-end space-x-2">
                                        <button
                                            @click="editEntry(entry)"
                                            class="text-indigo-600 hover:text-indigo-800"
                                        >
                                            編集
                                        </button>
                                        <button
                                            @click="destroy(entry.id)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            削除
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 編集モーダル -->
            <div
                v-if="showEditModal"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4"
            >
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">日記を編集</h3>
                        <button
                            @click="closeEditModal"
                            class="text-gray-500 hover:text-gray-700"
                        >
                            <span class="text-2xl">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="updateEntry" class="space-y-4">
                        <div>
                            <label
                                for="edit_content"
                                class="block text-sm font-medium text-gray-700"
                                >内容</label
                            >
                            <textarea
                                v-model="editForm.content"
                                id="edit_content"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>
                        <div>
                            <label
                                for="edit_mood"
                                class="block text-sm font-medium text-gray-700"
                                >気分</label
                            >
                            <select
                                v-model="editForm.mood"
                                id="edit_mood"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">選択してください</option>
                                <option value="😊 良い">😊 良い</option>
                                <option value="😐 普通">😐 普通</option>
                                <option value="😢 悪い">😢 悪い</option>
                            </select>
                        </div>
                        <div>
                            <label
                                for="edit_entry_date"
                                class="block text-sm font-medium text-gray-700"
                                >日付</label
                            >
                            <input
                                type="date"
                                v-model="editForm.entry_date"
                                id="edit_entry_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >タグ</label
                            >
                            <div class="mt-1 flex flex-wrap gap-2">
                                <button
                                    v-for="tag in availableTags"
                                    :key="tag"
                                    type="button"
                                    @click="toggleEditTag(tag)"
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm',
                                        editForm.tags.includes(tag)
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-200 text-gray-700',
                                    ]"
                                >
                                    {{ tag }}
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="closeEditModal"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                キャンセル
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import debounce from "lodash/debounce";

const props = defineProps({
    entries: {
        type: Object,
        required: true,
    },
    availableTags: {
        type: Array,
        required: true,
    },
    moodStats: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const filters = ref({
    search: props.filters.search || "",
    tags: props.filters.tags || [],
    mood: props.filters.mood || "",
    year: "",
    month: "",
});

const form = ref({
    content: "",
    mood: "",
    tags: [],
    entry_date: new Date().toISOString().split("T")[0],
});

// 新規エントリーフォームの表示状態
const showNewEntryForm = ref(false);

// 利用可能な年を計算
const availableYears = computed(() => {
    const years = new Set();
    Object.keys(props.entries).forEach((monthYear) => {
        const [year] = monthYear.split("-");
        years.add(year);
    });
    return Array.from(years).sort().reverse();
});

// フィルタリングされたエントリー
const filteredEntries = computed(() => {
    if (!filters.value.year && !filters.value.month) {
        return props.entries;
    }

    const filtered = {};
    Object.entries(props.entries).forEach(([monthYear, entries]) => {
        const [year, month] = monthYear.split("-");

        if (filters.value.year && year !== filters.value.year) {
            return;
        }

        if (filters.value.month && month !== filters.value.month) {
            return;
        }

        filtered[monthYear] = entries;
    });

    return filtered;
});

const filterByDate = () => {
    router.get("/diary-entries", { ...filters.value }, { preserveState: true });
};

// 検索の遅延実行
const debouncedSearch = debounce(() => {
    router.get("/diary-entries", { ...filters.value }, { preserveState: true });
}, 300);

// フィルターの変更を監視
watch(
    () => [filters.value.tag, filters.value.mood],
    () => {
        router.get(
            "/diary-entries",
            { ...filters.value },
            { preserveState: true }
        );
    }
);

const submit = () => {
    router.post("/diary-entries", form.value, {
        onSuccess: () => {
            form.value = {
                content: "",
                mood: "",
                tags: [],
                entry_date: new Date().toISOString().split("T")[0],
            };
        },
    });
};

const destroy = (id) => {
    if (!confirm("このエントリーを削除してもよろしいですか？")) {
        return;
    }
    router.delete(`/diary-entries/${id}`);
};

const toggleTag = (tag) => {
    const index = form.value.tags.indexOf(tag);
    if (index === -1) {
        form.value.tags.push(tag);
    } else {
        form.value.tags.splice(index, 1);
    }
};

const toggleTagFilter = (tag) => {
    const index = filters.value.tags.indexOf(tag);
    if (index === -1) {
        filters.value.tags.push(tag);
    } else {
        filters.value.tags.splice(index, 1);
    }
    router.get("/diary-entries", { ...filters.value }, { preserveState: true });
};

const toggleMoodFilter = (mood) => {
    filters.value.mood = filters.value.mood === mood ? "" : mood;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("ja-JP", {
        year: "numeric",
        month: "long",
        day: "numeric",
        weekday: "long",
    });
};

const formatMonthYear = (monthYear) => {
    const [year, month] = monthYear.split("-");
    return new Date(year, month - 1).toLocaleDateString("ja-JP", {
        year: "numeric",
        month: "long",
    });
};

// 編集モーダルの状態
const showEditModal = ref(false);
const editForm = ref({
    id: null,
    content: "",
    mood: "",
    tags: [],
    entry_date: "",
});

// 編集モーダルを開く
const editEntry = (entry) => {
    editForm.value = {
        id: entry.id,
        content: entry.content,
        mood: entry.mood || "",
        tags: entry.tags || [],
        entry_date: entry.entry_date,
    };
    showEditModal.value = true;
};

// 編集モーダルを閉じる
const closeEditModal = () => {
    showEditModal.value = false;
    editForm.value = {
        id: null,
        content: "",
        mood: "",
        tags: [],
        entry_date: "",
    };
};

// タグの切り替え（編集フォーム用）
const toggleEditTag = (tag) => {
    const index = editForm.value.tags.indexOf(tag);
    if (index === -1) {
        editForm.value.tags.push(tag);
    } else {
        editForm.value.tags.splice(index, 1);
    }
};

// 日記を更新
const updateEntry = () => {
    // 変更されたフィールドのみを抽出
    const changedFields = {};
    const originalEntry = props.entries[
        editForm.value.entry_date.substring(0, 7)
    ]?.find((entry) => entry.id === editForm.value.id);

    if (!originalEntry) {
        console.error("Original entry not found");
        return;
    }

    // 各フィールドを比較して、変更があった場合のみ更新対象に含める
    if (editForm.value.content !== originalEntry.content) {
        changedFields.content = editForm.value.content;
    }
    if (editForm.value.mood !== originalEntry.mood) {
        changedFields.mood = editForm.value.mood;
    }
    if (editForm.value.entry_date !== originalEntry.entry_date) {
        changedFields.entry_date = editForm.value.entry_date;
    }

    // タグの比較（配列の内容を比較）
    const originalTags = originalEntry.tags || [];
    if (
        JSON.stringify(editForm.value.tags.sort()) !==
        JSON.stringify(originalTags.sort())
    ) {
        changedFields.tags = editForm.value.tags;
    }

    // 変更があるフィールドのみを送信
    if (Object.keys(changedFields).length > 0) {
        router.put(`/diary-entries/${editForm.value.id}`, changedFields, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                closeEditModal();
            },
        });
    } else {
        // 変更がない場合はモーダルを閉じるだけ
        closeEditModal();
    }
};
</script>
