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
import { Textarea } from '@/components/ui/textarea';
import { computed, ref, reactive } from 'vue';
import DialogFormCreate from '@/components/DialogFormCreate.vue';
import DialogFormEdit from '@/components/DialogFormEdit.vue';

interface Task {
    id: number;
    title: string;
    description?: string;
    status: 'pending' | 'completed';
    order: number;
    due_date?: string;
    list_id: number;
    user_id: number;
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
    description: '',
    lists_id: null as number | null, // Changed from list_id to lists_id
    due_date: '',
});

// Form for editing tasks
const editTaskForm = useForm({
    title: '',
    description: '',
    due_date: '',
});

// Form for editing lists
const editListForm = useForm({
    title: '',
});

// State management
const editingListId = ref<number | null>(null);
const editListTitle = ref('');

// Task creation modal state
const createTaskModal = reactive({
    isOpen: false,
    listId: null as number | null,
});

// Task editing modal state
const editTaskModal = reactive({
    isOpen: false,
    taskId: null as number | null,
    listId: null as number | null,
    title: '',
    description: '',
    due_date: ''
});

// Create new list
const addList = () => {
    listForm.post(route('lists.store'), {
        onSuccess: () => {
            listForm.reset('title');
        },
        onError: () => {
            console.error('Error creating list');
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
        },
        onError: () => {
            console.error('Error updating list');
        }
    });
};

// Delete list
const deleteList = (id: number) => {
    if (confirm('Are you sure you want to delete this list and all its tasks?')) {
        router.delete(route('lists.destroy', { lists: id }), {
            onSuccess: () => {
                console.log('List deleted successfully');
            },
            onError: () => {
                console.error('Error deleting list');
            }
        });
    }
};


// Toggle task completion
const toggleTaskCompletion = (task: Task) => {
    const newStatus = task.status === 'completed' ? 'pending' : 'completed';

    router.put(route('tasks.update', { task: task.id }), {
        status: newStatus
    }, {
        onSuccess: () => {
            console.log(`Task ${newStatus}`);
        },
        onError: () => {
            console.error('Error updating task');
        }
    });
};


// Delete task
const deleteTask = (task: Task, listId: number) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(route('lists.tasks.destroy', {
            lists: listId,
            task: task.id
        }), {
            onSuccess: () => {
                console.log('Task deleted successfully');
            },
            onError: () => {
                console.error('Error deleting task');
            }
        });
    }
};

// Get completed tasks count for a list
const getCompletedTasksCount = (list: List): number => {
    return list.tasks?.filter(task => task.status === 'completed').length || 0;
};

// Get progress percentage for a list
const getProgressPercentage = (list: List): number => {
    const totalTasks = list.tasks?.length || 0;
    if (totalTasks === 0) return 0;
    const completedTasks = getCompletedTasksCount(list);
    return Math.round((completedTasks / totalTasks) * 100);
};

// Format due date
const formatDueDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleDateString();
};

// Check if task is overdue
const isOverdue = (task: Task): boolean => {
    if (!task.due_date || task.status === 'completed') return false;
    return new Date(task.due_date) < new Date();
};

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
                                 New List
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
                    <!-- Add task button -->
                    <div class="flex justify-center">
                        <DialogFormCreate :list-id="list.id" />
                    </div>

                    <!-- Tasks list -->
                    <div class="space-y-2">
                        <div
                            v-for="task in list.tasks"
                            :key="task.id"
                            class="flex items-start gap-3 p-3 rounded-lg bg-muted/50 border"
                            :class="{ 'border-red-200 bg-red-50': isOverdue(task) }"
                        >
                            <!-- Task checkbox -->
                            <Checkbox
                                :id="`task-${task.id}`"
                                :checked="task.status === 'completed'"
                                @update:checked="toggleTaskCompletion(task)"
                                class="mt-1"
                            />

                            <!-- Status icon -->
                            <div class="flex-shrink-0 mt-1">
                                <CheckCircle
                                    v-if="task.status === 'completed'"
                                    class="w-4 h-4 text-green-600"
                                />
                                <Clock
                                    v-else
                                    class="w-4 h-4"
                                    :class="isOverdue(task) ? 'text-red-500' : 'text-orange-500'"
                                />
                            </div>

                            <!-- Task content -->
                            <div class="flex-1 min-w-0 space-y-1">
                                <div>
                                    <span
                                        :class="{ 'line-through text-muted-foreground': task.status === 'completed' }"
                                        class="block font-medium"
                                    >
                                        {{ task.title }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <Badge v-if="task.status === 'completed'" variant="secondary" class="text-xs">
                                        Completed
                                    </Badge>
                                    <Badge v-if="isOverdue(task)" variant="destructive" class="text-xs">
                                        Overdue
                                    </Badge>
                                    <Badge v-if="task.due_date && !isOverdue(task) && task.status !== 'completed'" variant="outline" class="text-xs">
                                        Due: {{ formatDueDate(task.due_date) }}
                                    </Badge>
                                </div>
                            </div>

                            <!-- Task actions -->
                            <div class="flex gap-1">
                                <DialogFormEdit/>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="deleteTask(task.id, listId)"
                                >
                                    <Trash2 class="w-3 h-3" />
                                </Button>
                            </div>
                        </div>

                        <!-- Empty state -->
                        <div v-if="!list.tasks || list.tasks.length === 0" class="text-center text-muted-foreground py-8">
                            <p>No tasks added</p>
                            <p class="text-sm">Click "Add Task" to get started</p>
                        </div>
                    </div>
                </CardContent>

                <!-- Card footer with progress -->
                <div class="p-4 border-t bg-muted/20">
                    <div class="flex items-center justify-between text-sm text-muted-foreground mb-2">
                        <span>Progress</span>
                        <span>{{ getCompletedTasksCount(list) }} of {{ list.tasks?.length || 0 }} tasks</span>
                    </div>
                    <Progress :value="getProgressPercentage(list)" class="h-2" />
                    <div class="text-xs text-muted-foreground mt-1 text-center">
                        {{ getProgressPercentage(list) }}% complete
                    </div>
                </div>
            </Card>

            <!-- Empty state for no lists -->
            <div v-if="lists.length === 0" class="col-span-full text-center p-8 bg-muted/50 rounded-lg">
                <p class="text-muted-foreground">No lists created. Create your first list above.</p>
            </div>
        </div>
    </AppLayout>
</template>
