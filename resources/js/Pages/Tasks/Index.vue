<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    },
});

const sortOption = ref("created_desc"); // created_desc, created_asc, due_date
const showCompleted = ref(true);
const showIncomplete = ref(true);

const filteredAndSortedTasks = computed(() => {
    let result = [...props.tasks];

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
    form.post(route("tasks.store"), {
        onSuccess: () => form.reset(),
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
        // チェックボックスの更新時は既存の値を保持
        editForm.title = task.title;
        editForm.description = task.description;
        editForm.due_date = task.due_date;
        editForm.completed = task.completed;
    }
    editForm.put(route("tasks.update", task.id), {
        onSuccess: () => {
            editingTask.value = null;
            editForm.reset();
        },
    });
};

const deleteTask = (task) => {
    if (confirm("本当にこのタスクを削除しますか？")) {
        useForm().delete(route("tasks.destroy", task.id));
    }
};
</script>

<template>
    <div>
        <Head title="タスク管理" />

        <AuthenticatedLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    タスク管理
                </h2>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <!-- タスク作成フォーム -->
                        <form @submit.prevent="submit" class="mb-8">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <InputLabel for="title" value="タイトル" />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        for="description"
                                        value="説明"
                                    />
                                    <TextInput
                                        id="description"
                                        v-model="form.description"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="due_date" value="期限" />
                                    <TextInput
                                        id="due_date"
                                        v-model="form.due_date"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
                                    />
                                </div>
                            </div>
                            <div class="mt-4">
                                <PrimaryButton :disabled="form.processing">
                                    タスクを追加
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- ソートとフィルターコントロール -->
                        <div
                            class="mb-6 flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6"
                        >
                            <div class="flex items-center space-x-4">
                                <label class="text-sm font-medium text-gray-700"
                                    >ソート:</label
                                >
                                <select
                                    v-model="sortOption"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
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
                                <label class="text-sm font-medium text-gray-700"
                                    >表示:</label
                                >
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="showIncomplete"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-600"
                                            >未完了のタスク</span
                                        >
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="showCompleted"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-600"
                                            >完了したタスク</span
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- タスク一覧 -->
                        <div class="space-y-4">
                            <div
                                v-for="task in filteredAndSortedTasks"
                                :key="task.id"
                                class="bg-gray-50 p-4 rounded-lg"
                            >
                                <div v-if="editingTask?.id === task.id">
                                    <!-- 編集フォーム -->
                                    <form
                                        @submit.prevent="updateTask(task)"
                                        class="space-y-4"
                                    >
                                        <div
                                            class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                        >
                                            <div>
                                                <InputLabel
                                                    for="edit_title"
                                                    value="タイトル"
                                                />
                                                <TextInput
                                                    id="edit_title"
                                                    v-model="editForm.title"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <InputLabel
                                                    for="edit_description"
                                                    value="説明"
                                                />
                                                <TextInput
                                                    id="edit_description"
                                                    v-model="
                                                        editForm.description
                                                    "
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                />
                                            </div>
                                            <div>
                                                <InputLabel
                                                    for="edit_due_date"
                                                    value="期限"
                                                />
                                                <TextInput
                                                    id="edit_due_date"
                                                    v-model="editForm.due_date"
                                                    type="datetime-local"
                                                    class="mt-1 block w-full"
                                                />
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="editForm.completed"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                />
                                                <span class="ml-2">完了</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <PrimaryButton
                                                :disabled="editForm.processing"
                                            >
                                                更新
                                            </PrimaryButton>
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
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
                                                :checked="task.completed"
                                                @change="
                                                    updateTask({
                                                        ...task,
                                                        completed:
                                                            !task.completed,
                                                    })
                                                "
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <div>
                                                <h3
                                                    class="text-lg font-semibold"
                                                    :class="{
                                                        'line-through':
                                                            task.completed,
                                                    }"
                                                >
                                                    {{ task.title }}
                                                </h3>
                                                <p
                                                    class="text-gray-600"
                                                    v-if="task.description"
                                                >
                                                    {{ task.description }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-500"
                                                    v-if="task.due_date"
                                                >
                                                    期限:
                                                    {{
                                                        formatDateTime(
                                                            task.due_date
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button
                                                @click="startEditing(task)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                編集
                                            </button>
                                            <button
                                                @click="deleteTask(task)"
                                                class="text-red-600 hover:text-red-900"
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
