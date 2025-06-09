<script setup lang="ts">
import { Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head , usePage , useForm, router} from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import DialogFormCreate from '@/components/DialogFormCreate.vue';
import { computed, ref } from 'vue';



interface List {
    id: number;
    title: string;
}

const page = usePage<{ lists?: List[] }>();
const lists = computed(() => page.props.lists || []);

const form = useForm({
    title: '',
});

const addList = () => {

    form.post(route('lists.store'), {
        onSuccess: () => form.reset('title'),
    });
};

const editingId = ref<number | null>(null);
const editTitle = ref('');
const startEditing = (list: List) => {
    editingId.value = list.id;
    editTitle.value = list.title;
};

const saveTitle = (list: List) => {
    router.put(route('lists.update', { lists: list.id }), { title: editTitle.value }, {
        onSuccess: () => {
            editingId.value = null;
        },
    });
};

const deleteList = (id: number) => {
    if (confirm('are you sure about this?')) {
        router.delete(route('lists.destroy', { lists: id }));
    }
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
        <form @submit.prevent="addList">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
                <Card class="flex w-sm">
                    <CardHeader>
                        <CardTitle>To-do List</CardTitle>
                        <div class="flex w-full max-w-sm items-center gap-1.5">
                            <Input v-model="form.title" id="list-title" type="text" placeholder="New List" />
                            <Button type="submit" :disabled="form.processing">
                                + New List
                            </Button>
                        </div>
                    </CardHeader>
                </Card>

            </div>
        </form>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5 place-items-center">
                <Card v-for="list in lists" :key="list.id" class="w-xs">
                        <CardHeader class="pb-3 ">
                            <div class="flex items-center justify-between border-b pb-2">
                                <CardTitle class="text-base">
                                    <template v-if="editingId === list.id">
                                        <input
                                            v-model="editTitle"
                                            class="border rounded px-2 py-1 text-sm "
                                            @keyup.enter="saveTitle(list)"
                                            @blur="saveTitle(list)"
                                        />
                                    </template>
                                    <template v-else>
                                        {{ list.title }}
                                    </template>
                                </CardTitle>
                                <Button variant="outline" class="w-6 h-6 text-sm" @click="startEditing(list)"><Pencil /></Button>
                                <Button variant="outline" class="w-6 h-6 text-sm" @click="deleteList(list.id)"><Trash2 /></Button>
                                <DialogFormCreate />

                            </div>
                        </CardHeader>


                    <!-- Aqui vai ficar as tasks-->

                    <CardContent class="mt-4">

                        <!-- Placeholder para tasks -->
                        <div class="text-gray-500 text-sm">
                            <p>Nenhuma tarefa ainda</p>
                        </div>

                    </CardContent>

                </Card>
        </div>
    </AppLayout>
</template>
