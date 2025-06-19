<script setup lang="ts">
import { Plus } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { ref } from 'vue';

interface Props {
    listId: number;
}

const props = defineProps<Props>();

const isOpen = ref(false);

// Form for creating new tasks
const form = useForm({
    title: '',
    description: '',
    lists_id: props.listId,
    due_date: '',
});

// Create new task
const createTask = () => {
    if (!form.title.trim()) return;

    form.post(route('tasks.store'), {
        onSuccess: () => {
            form.reset('title', 'description', 'due_date');
            isOpen.value = false;
        },
        onError: (errors) => {
            console.error('Error creating task:', errors);
        }
    });
};

// Close modal
const closeModal = () => {
    isOpen.value = false;
    form.reset('title', 'description', 'due_date');
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" size="sm" class="w-full">
                <Plus class="w-4 h-4 mr-2" />
                Add Task
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Create New Task</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="createTask" class="space-y-4">
                <div>
                    <label for="task-title" class="text-sm font-medium">Title</label>
                    <Input
                        id="task-title"
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
                    <label for="task-description" class="text-sm font-medium">Description</label>
                    <Textarea
                        id="task-description"
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
                    <label for="task-due-date" class="text-sm font-medium">Due Date</label>
                    <Input
                        id="task-due-date"
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
                        Create Task
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
