<script setup lang="ts">
import { Pencil, Trash2, Plus, ChevronUp, ChevronDown, Check, CheckCircle, Clock, X } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Progress } from '@/components/ui/progress';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { computed, ref, reactive } from 'vue';

interface Task {
    id: number;
    title: string;
    completed: boolean;
    list_id: number;
}

interface List {
    id: number;
    title: string;
    tasks?: Task[];
}

const page = usePage<{ lists?: List[] }>();
const lists = computed(() => page.props.lists || []);

// Form for creating new lists
const listForm = useForm({
    title: '',
});

// Form for creating new tasks
const taskForm = useForm({
    title: '',
    list_id: null as number | null,
});

// Form for editing tasks
const editTaskForm = useForm({
    title: '',
});

// Form for editing lists
const editListForm = useForm({
    title: '',
});

// State management
const editingListId = ref<number | null>(null);
const editListTitle = ref('');

// Task editing modal state
const editTaskModal = reactive({
    isOpen: false,
    taskId: null as number | null,
    listId: null as number | null,
    title: ''
});

// Toast notifications state
const toasts = ref<Array<{ id: number; message: string; type: 'success' | 'error' | 'info' }>>([]);

// Create new list
const addList = () => {
    listForm.post(route('lists.store'), {
        onSuccess: () => {
            listForm.reset('title');
            showToast('List created successfully!', 'success');
        },
        onError: () => {
            showToast('Error creating list', 'error');
        }
    });
};

// Start editing list
const startEditingList = (list: List) => {
    editingListId.value = list.id;
    editListTitle.value = list.title;
};

// Save list edit
const saveListTitle = (list: List) => {
    editListForm.title = editListTitle.value;
    editListForm.put(route('lists.update', { lists: list.id }), {
        onSuccess: () => {
            editingListId.value = null;
            showToast('List updated successfully!', 'success');
        },
        onError: () => {
            showToast('Error updating list', 'error');
        }
    });
};

// Delete list
const deleteList = (id: number) => {
    if (confirm('Are you sure you want to delete this list and all its tasks?')) {
        router.delete(route('lists.destroy', { lists: id }), {
            onSuccess: () => {
                showToast('List successfully deleted!', 'info');
            },
            onError: () => {
                showToast('Error deleting list', 'error');
            }
        });
    }
};

// Add task to list
const addTask = (listId: number, newTaskTitle: string) => {
    if (!newTaskTitle.trim()) return;

    taskForm.title = newTaskTitle;
    taskForm.list_id = listId;

    taskForm.post(route('tasks.store'), {
        onSuccess: () => {
            taskForm.reset();
            showToast('Task added successfully!', 'success');
        },
        onError: () => {
            showToast('Error adding task', 'error');
        }
    });
};

// Toggle task completion
const toggleTaskCompletion = (task: Task) => {
    router.put(route('tasks.update', { task: task.id }), {
        completed: !task.completed
    }, {
        onSuccess: () => {
            const status = !task.completed ? 'concluída' : 'reaberta';
            showToast(`Task ${status}!`, !task.completed ? 'success' : 'info');
        },
        onError: () => {
            showToast('Error updating task', 'error');
        }
    });
};

// Open edit task modal
const openEditTaskModal = (task: Task) => {
    editTaskModal.isOpen = true;
    editTaskModal.taskId = task.id;
    editTaskModal.listId = task.list_id;
    editTaskModal.title = task.title;
};

// Close edit task modal
const closeEditTaskModal = () => {
    editTaskModal.isOpen = false;
    editTaskModal.taskId = null;
    editTaskModal.listId = null;
    editTaskModal.title = '';
};

// Save task edit
const saveTaskEdit = () => {
    if (!editTaskModal.title.trim()) return;

    editTaskForm.title = editTaskModal.title;
    editTaskForm.put(route('tasks.update', { task: editTaskModal.taskId }), {
        onSuccess: () => {
            closeEditTaskModal();
            showToast('Task updated successfully!', 'success');
        },
        onError: () => {
            showToast('Error updating task', 'error');
        }
    });
};

// Delete task
const deleteTask = (taskId: number) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(route('tasks.destroy', { task: taskId }), {
            onSuccess: () => {
                showToast('Task successfully deleted!', 'info');
            },
            onError: () => {
                showToast('Error deleting task', 'error');
            }
        });
    }
};

// Get completed tasks count for a list
const getCompletedTasksCount = (list: List): number => {
    return list.tasks?.filter(task => task.completed).length || 0;
};

// Get progress percentage for a list
const getProgressPercentage = (list: List): number => {
    const totalTasks = list.tasks?.length || 0;
    if (totalTasks === 0) return 0;
    const completedTasks = getCompletedTasksCount(list);
    return Math.round((completedTasks / totalTasks) * 100);
};

// Toast notification functions
const showToast = (message: string, type: 'success' | 'error' | 'info' = 'info') => {
    const toast = {
        id: Date.now(),
        message,
        type
    };
    toasts.value.push(toast);

    // Auto-remove toast after 3 seconds
    setTimeout(() => {
        removeToast(toast.id);
    }, 3000);
};

const removeToast = (id: number) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index > -1) {
        toasts.value.splice(index, 1);
    }
};

const getToastClass = (type: string) => {
    switch (type) {
        case 'success':
            return 'bg-green-50 text-green-800 border-green-200';
        case 'error':
            return 'bg-red-50 text-red-800 border-red-200';
        case 'info':
        default:
            return 'bg-blue-50 text-blue-800 border-blue-200';
    }
};

// Reactive task input refs for each list
const taskInputs = ref<Record<number, string>>({});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'To-do List',
        href: '/lists',
    },
];
</script>

<template>
    <Head title="To-do List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Create new list form -->
        <form @submit.prevent="addList">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
                <Card class="w-full max-w-md">
                    <CardHeader>
                        <CardTitle class="text-center pb-2 ">Manage Lists</CardTitle>
                        <div class="flex w-full items-center gap-2">
                            <Input
                                v-model="listForm.title"
                                id="list-title"
                                type="text"
                                placeholder="New List Name"
                                class="flex-1"
                            />
                            <Button type="submit" :disabled="listForm.processing">
                                <Plus class="w-4 h-4 mr-2" />
                                + New List
                            </Button>
                        </div>
                    </CardHeader>
                </Card>
            </div>
        </form>

        <!-- Lists container -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <!-- Individual list cards -->
            <Card v-for="list in lists" :key="list.id" class="flex flex-col">
                <!-- Card header with list title and actions -->
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-lg">
                            <template v-if="editingListId === list.id">
                                <Input
                                    v-model="editListTitle"
                                    class="text-lg font-semibold"
                                    @keyup.enter="saveListTitle(list)"
                                    @blur="saveListTitle(list)"
                                />
                            </template>
                            <template v-else>
                                {{ list.title }}
                            </template>
                        </CardTitle>

                        <div class="flex gap-2">
                            <Button
                                v-if="editingListId !== list.id"
                                variant="outline"
                                size="sm"
                                @click="startEditingList(list)"
                            >
                                <Pencil class="w-4 h-4" />
                            </Button>
                            <Button
                                v-else
                                variant="default"
                                size="sm"
                                @click="saveListTitle(list)"
                            >
                                <Check class="w-4 h-4" />
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="deleteList(list.id)"
                            >
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>
                    </div>
                    <Separator />
                </CardHeader>

                <!-- Card content with tasks -->
                <CardContent class="flex-1 space-y-4">
                    <!-- Add task form -->
                    <form @submit.prevent="addTask(list.id, taskInputs[list.id] || ''); taskInputs[list.id] = ''">
                        <div class="flex gap-2">
                            <Input
                                v-model="taskInputs[list.id]"
                                type="text"
                                placeholder="New Task"
                                class="flex-1"
                            />
                            <Button type="submit" size="sm">
                                <Plus class="w-4 h-4" />
                            </Button>
                        </div>
                    </form>

                    <!-- Tasks list -->
                    <div class="space-y-2">
                        <div
                            v-for="task in list.tasks"
                            :key="task.id"
                            class="flex items-center gap-3 p-3 rounded-lg bg-muted/50 border"
                        >
                            <!-- Task checkbox -->
                            <Checkbox
                                :id="`task-${task.id}`"
                                :checked="task.completed"
                                @update:checked="toggleTaskCompletion(task)"
                            />

                            <!-- Status icon -->
                            <div class="flex-shrink-0">
                                <CheckCircle
                                    v-if="task.completed"
                                    class="w-4 h-4 text-green-600"
                                />
                                <Clock
                                    v-else
                                    class="w-4 h-4 text-orange-500"
                                />
                            </div>

                            <!-- Task text -->
                            <div class="flex-1 min-w-0">
                                <span
                                    :class="{ 'line-through text-muted-foreground': task.completed }"
                                    class="block truncate"
                                >
                                    {{ task.title }}
                                </span>
                                <Badge v-if="task.completed" variant="secondary" class="text-xs mt-1">
                                    Completed
                                </Badge>
                            </div>

                            <!-- Task actions -->
                            <div class="flex gap-1">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="openEditTaskModal(task)"
                                >
                                    <Pencil class="w-3 h-3" />
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="deleteTask(task.id)"
                                >
                                    <Trash2 class="w-3 h-3" />
                                </Button>
                            </div>
                        </div>

                        <!-- Empty state -->
                        <div v-if="!list.tasks || list.tasks.length === 0" class="text-center text-muted-foreground py-8">
                            <p>No tasks added</p>
                            <p class="text-sm">Add a task above to get started</p>
                        </div>
                    </div>
                </CardContent>

                <!-- Card footer with progress -->
                <div class="p-4 border-t bg-muted/20">
                    <div class="flex items-center justify-between text-sm text-muted-foreground mb-2">
                        <span>Progress</span>
                        <span>{{ getCompletedTasksCount(list) }} de {{ list.tasks?.length || 0 }} tasks</span>
                    </div>
                    <Progress :value="getProgressPercentage(list)" class="h-2" />
                    <div class="text-xs text-muted-foreground mt-1 text-center">
                        {{ getProgressPercentage(list) }}% complete
                    </div>
                </div>
            </Card>

            <!-- Empty state for no lists -->
            <div v-if="lists.length === 0" class="col-span-full text-center p-8 bg-muted/50 rounded-lg">
                <p class="text-muted-foreground">No list created. Create your first list above.</p>
            </div>
        </div>
    </AppLayout>

    <!-- Edit Task Modal -->
    <Dialog v-model:open="editTaskModal.isOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Edit Tasks</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="saveTaskEdit" class="space-y-4">
                <Input
                    v-model="editTaskModal.title"
                    type="text"
                    placeholder="Task title"
                    class="w-full"
                />
                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="closeEditTaskModal">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="editTaskForm.processing">
                        Save
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Toast Notifications -->
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2">
        <div
            v-for="toast in toasts"
            :key="toast.id"
            :class="`${getToastClass(toast.type)} rounded-lg border p-4 shadow-md transition-all duration-300`"
        >
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <CheckCircle v-if="toast.type === 'success'" class="w-4 h-4" />
                    <X v-if="toast.type === 'error'" class="w-4 h-4" />
                    <Clock v-if="toast.type === 'info'" class="w-4 h-4" />
                    <span class="font-medium">{{ toast.message }}</span>
                </div>
                <Button
                    variant="ghost"
                    size="sm"
                    @click="removeToast(toast.id)"
                    class="h-auto p-1"
                >
                    <X class="w-4 h-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
