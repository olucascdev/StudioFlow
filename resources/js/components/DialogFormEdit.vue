<script setup lang="ts">
import { Pencil } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { ref, watch } from 'vue';

interface Task {
    id: number;
    title: string;
    description?: string;
    status: 'pending' | 'completed';
    order: number;
    due_date?: string;
    lists_id: number;
    user_id: number;
}

interface Props {
    task: Task;
}

const props = defineProps<Props>();

const isOpen = ref(false);

// Form starts with empty values
const form = useForm({
    title: '',
    description: '',
    due_date: '',
});

// Watch for changes in props.task and update the form
watch(
    () => props.task,
    (newTask) => {
        if (newTask) {
            form.title = newTask.title || '';
            form.description = newTask.description || '';
            form.due_date = newTask.due_date ? newTask.due_date.split('T')[0] : '';
        }
    },
    { deep: true, immediate: true }
);
// Save task edit
const saveTask = () => {
    if (!form.title.trim()) return;

    form.put(route('tasks.update', { task: props.task.id }), {
        onSuccess: () => {
            isOpen.value = false;
        },
        onError: (errors) => {
            console.error('Error updating task:', errors);
        }
    });
};

// Close modal
const closeModal = () => {
    isOpen.value = false;
    // Reset form to original task values
    form.title = props.task.title;
    form.description = props.task.description || '';
    form.due_date = props.task.due_date ? props.task.due_date.split('T')[0] : '';
    form.clearErrors();
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" size="sm">
                <Pencil class="w-3 h-3" />
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Edit Task</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="saveTask" class="space-y-4">
                <div>
                    <label for="edit-task-title" class="text-sm font-medium">Title</label>
                    <Input
                        id="edit-task-title"
                        v-model="form.title"
                        type="text"
                        placeholder="Task title"
                        class="w-full"
                        required
                    />
                    <div v-if="form.errors.title" class="text-sm text-red-600 mt-1">
                        {{ form.errors.title }}
                    </div>
                </div>
                <div>
                    <label for="edit-task-description" class="text-sm font-medium">Description</label>
                    <Textarea
                        id="edit-task-description"
                        v-model="form.description"
                        placeholder="Task description (optional)"
                        class="w-full"
                        rows="3"
                    />
                    <div v-if="form.errors.description" class="text-sm text-red-600 mt-1">
                        {{ form.errors.description }}
                    </div>
                </div>
                <div>
                    <label for="edit-task-due-date" class="text-sm font-medium">Due Date</label>
                    <Input
                        id="edit-task-due-date"
                        v-model="form.due_date"
                        type="date"
                        class="w-full"
                    />
                    <div v-if="form.errors.due_date" class="text-sm text-red-600 mt-1">
                        {{ form.errors.due_date }}
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="closeModal">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        Save Changes
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
