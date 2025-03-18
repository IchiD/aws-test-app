<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed, onMounted, reactive } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

// タスク配列をリアクティブにして、変更が直接UIに反映されるようにする
const tasksList = reactive([...props.tasks]);

// アニメーション用の状態
const isLoaded = ref(false);
const formVisible = ref(false);

// ページ読み込み時にアニメーションを開始
onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
        setTimeout(() => {
            formVisible.value = true;
        }, 300);
    }, 100);
});

const sortOption = ref("created_desc"); // created_desc, created_asc, due_date
const showCompleted = ref(true);
const showIncomplete = ref(true);

const filteredAndSortedTasks = computed(() => {
    let result = [...tasksList];

    // フィルタリング
    result = result.filter((task) => {
        if (showCompleted.value && showIncomplete.value) return true;
        if (showCompleted.value) return task.completed;
        if (showIncomplete.value) return !task.completed;
        return false;
    });

    // ソート
    result.sort((a, b) => {
        switch (sortOption.value) {
            case "created_asc":
                return new Date(a.created_at) - new Date(b.created_at);
            case "created_desc":
                return new Date(b.created_at) - new Date(a.created_at);
            case "due_date":
                if (!a.due_date) return 1;
                if (!b.due_date) return -1;
                return new Date(a.due_date) - new Date(b.due_date);
            default:
                return 0;
        }
    });

    return result;
});

const formatDateTime = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("ja-JP", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    }).format(date);
};

const form = useForm({
    title: "",
    description: "",
    due_date: "",
});

const editForm = useForm({
    title: "",
    description: "",
    completed: false,
    due_date: "",
});

const editingTask = ref(null);

const submit = () => {
    console.log("Submitting form data:", form.data());
    form.transform((data) => {
        console.log("Transformed data:", data);
        return {
            ...data,
            _method: "POST",
        };
    }).post(route("tasks.store"), {
        preserveState: true,
        onSuccess: (page) => {
            console.log("Success response:", page);
            // ページのpropsから最新のタスクリストを取得
            const latestTasks = page.props.tasks;
            if (latestTasks && Array.isArray(latestTasks)) {
                // タスクリストを更新
                tasksList.splice(0, tasksList.length, ...latestTasks);
                console.log("Updated tasks list:", tasksList);
            }
            form.reset();
        },
        onError: (errors) => {
            console.error("Error submitting task:", errors);
        },
    });
};

const startEditing = (task) => {
    editingTask.value = task;
    editForm.title = task.title;
    editForm.description = task.description || "";
    editForm.completed = task.completed;
    editForm.due_date = task.due_date;
};

const updateTask = (task) => {
    if (typeof task.completed === "boolean" && !editingTask.value) {
        // チェックボックスの更新時は完了状態と必須データを含める
        const updateData = {
            title: task.title,
            description: task.description || "",
            completed: task.completed,
            due_date: task.due_date,
        };

        // APIリクエスト送信
        useForm(updateData)
            .transform((data) => ({
                ...data,
                _method: "PUT", // Laravel は PUT メソッドをこの形で認識
            }))
            .post(route("tasks.update", task.id), {
                forceFormData: true, // FormData としてデータを送信
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log(
                        "タスク更新成功:",
                        task.title,
                        "完了状態:",
                        task.completed,
                    );
                },
                onError: (errors) => {
                    console.error("タスク更新エラー:", errors);
                    // エラーが発生した場合は状態を元に戻す
                    task.completed = !task.completed;
                },
            });
    } else {
        // 通常の編集フォームからの更新の場合
        editForm
            .transform((data) => ({
                ...data,
                _method: "PUT",
            }))
            .post(route("tasks.update", task.id), {
                forceFormData: true,
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // 成功したら編集中のタスクを更新
                    const taskIndex = tasksList.findIndex(
                        (t) => t.id === task.id,
                    );
                    if (taskIndex !== -1) {
                        tasksList[taskIndex].title = editForm.title;
                        tasksList[taskIndex].description = editForm.description;
                        tasksList[taskIndex].due_date = editForm.due_date;
                        tasksList[taskIndex].completed = editForm.completed;
                    }

                    editingTask.value = null;
                    editForm.reset();
                },
            });
    }
};

const deleteTask = (task) => {
    if (confirm("本当にこのタスクを削除しますか？")) {
        // ローカルのタスクリストからも削除（UI即時反映）
        const taskIndex = tasksList.findIndex((t) => t.id === task.id);
        if (taskIndex !== -1) {
            tasksList.splice(taskIndex, 1);
        }

        // バックエンドにも通知
        useForm().delete(route("tasks.destroy", task.id), {
            onError: () => {
                // エラーが発生した場合、再度タスクリストを取得するために
                // ページをリロード
                window.location.reload();
            },
        });
    }
};
</script>

<template>
    <div>
        <Head title="タスク管理" />

        <AuthenticatedLayout>
            <template #header>
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    タスク管理
                </h2>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 transition-all duration-500"
                        :class="{ 'animate-slide-down': isLoaded }"
                        :style="{ opacity: isLoaded ? 1 : 0 }"
                    >
                        <!-- タスク作成フォーム -->
                        <form
                            @submit.prevent="submit"
                            class="mb-8 transition-all duration-500 transform overflow-hidden"
                            :class="{
                                'max-h-[500px] opacity-100': formVisible,
                                'max-h-0 opacity-0': !formVisible,
                            }"
                        >
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <InputLabel
                                        for="title"
                                        value="タイトル"
                                        class="text-gray-800 dark:text-gray-200"
                                    />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        for="description"
                                        value="説明"
                                        class="text-gray-800 dark:text-gray-200"
                                    />
                                    <TextInput
                                        id="description"
                                        v-model="form.description"
                                        type="text"
                                        class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700"
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        for="due_date"
                                        value="期限"
                                        class="text-gray-800 dark:text-gray-200"
                                    />
                                    <TextInput
                                        id="due_date"
                                        v-model="form.due_date"
                                        type="datetime-local"
                                        class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700"
                                    />
                                </div>
                            </div>
                            <div class="mt-4">
                                <PrimaryButton
                                    :disabled="form.processing"
                                    class="transition-all duration-300 hover:shadow-md hover:scale-105"
                                >
                                    タスクを追加
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- ソートとフィルターコントロール -->
                        <div
                            class="mb-6 flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6 transition-all duration-500 opacity-0"
                            :class="{ 'opacity-100': isLoaded }"
                            :style="{ transitionDelay: '400ms' }"
                        >
                            <div class="flex items-center space-x-4">
                                <select
                                    v-model="sortOption"
                                    class="rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-300 hover:border-indigo-400 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200"
                                >
                                    <option value="created_desc">
                                        作成日時（新しい順）
                                    </option>
                                    <option value="created_asc">
                                        作成日時（古い順）
                                    </option>
                                    <option value="due_date">
                                        期限が近い順
                                    </option>
                                </select>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="showIncomplete"
                                            class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 transition-all duration-300"
                                        />
                                        <span
                                            class="ml-2 text-sm text-gray-600 dark:text-gray-400"
                                            >未完了のタスク</span
                                        >
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="showCompleted"
                                            class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 transition-all duration-300"
                                        />
                                        <span
                                            class="ml-2 text-sm text-gray-600 dark:text-gray-400"
                                            >完了したタスク</span
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- タスク一覧 -->
                        <div class="space-y-4">
                            <div
                                v-for="(task, index) in filteredAndSortedTasks"
                                :key="task.id"
                                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg transform transition-all duration-500 opacity-0"
                                :class="{ 'animate-slide-up': isLoaded }"
                                :style="{
                                    transitionDelay: `${500 + index * 100}ms`,
                                    opacity: isLoaded ? 1 : 0,
                                    transform: isLoaded
                                        ? 'translateY(0)'
                                        : 'translateY(20px)',
                                }"
                            >
                                <div v-if="editingTask?.id === task.id">
                                    <!-- 編集フォーム -->
                                    <form
                                        @submit.prevent="updateTask(task)"
                                        class="space-y-4 animate-fade-in"
                                    >
                                        <div
                                            class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                        >
                                            <div>
                                                <InputLabel
                                                    for="edit_title"
                                                    value="タイトル"
                                                    class="text-gray-800 dark:text-gray-200"
                                                />
                                                <TextInput
                                                    id="edit_title"
                                                    v-model="editForm.title"
                                                    type="text"
                                                    class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700"
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <InputLabel
                                                    for="edit_description"
                                                    value="説明"
                                                    class="text-gray-800 dark:text-gray-200"
                                                />
                                                <TextInput
                                                    id="edit_description"
                                                    v-model="
                                                        editForm.description
                                                    "
                                                    type="text"
                                                    class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700"
                                                />
                                            </div>
                                            <div>
                                                <InputLabel
                                                    for="edit_due_date"
                                                    value="期限"
                                                    class="text-gray-800 dark:text-gray-200"
                                                />
                                                <TextInput
                                                    id="edit_due_date"
                                                    v-model="editForm.due_date"
                                                    type="datetime-local"
                                                    class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700"
                                                />
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="editForm.completed"
                                                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 transition-all duration-300"
                                                />
                                                <span
                                                    class="ml-2 text-gray-800 dark:text-gray-200"
                                                    >完了</span
                                                >
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <PrimaryButton
                                                :disabled="editForm.processing"
                                                class="transition-all duration-300 hover:shadow-md hover:scale-105"
                                            >
                                                更新
                                            </PrimaryButton>
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-300 hover:shadow-md hover:scale-105"
                                                @click="editingTask = null"
                                            >
                                                キャンセル
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div v-else>
                                    <!-- タスク表示 -->
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-4"
                                        >
                                            <input
                                                type="checkbox"
                                                v-model="task.completed"
                                                @change="updateTask(task)"
                                                class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 transition-all duration-300"
                                            />
                                            <div>
                                                <h3
                                                    class="text-lg font-semibold transition-all duration-300 text-gray-800 dark:text-gray-200"
                                                    :class="{
                                                        'line-through':
                                                            task.completed,
                                                    }"
                                                >
                                                    {{ task.title }}
                                                </h3>
                                                <p
                                                    class="text-gray-600 dark:text-gray-400"
                                                    v-if="task.description"
                                                >
                                                    {{ task.description }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-500"
                                                    v-if="task.due_date"
                                                >
                                                    期限:
                                                    {{
                                                        formatDateTime(
                                                            task.due_date,
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button
                                                @click="startEditing(task)"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-all duration-300 hover:scale-110 px-3 py-1 rounded hover:bg-indigo-50 dark:hover:bg-indigo-900/30"
                                            >
                                                編集
                                            </button>
                                            <button
                                                @click="deleteTask(task)"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-all duration-300 hover:scale-110 px-3 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30"
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
            </div>
        </AuthenticatedLayout>
    </div>
</template>
