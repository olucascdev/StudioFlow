<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head ,usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import DialogFormCreate from '@/components/DialogFormCreate.vue';



interface List {
    id: number;
    title: string;
}

const { props } = usePage<{ lists?: List[] }>();
const lists = props.lists || [];

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
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <Card class="flex w-sm">
                <CardHeader>
                    <CardTitle>To-do List</CardTitle>
                    <div class="flex w-full max-w-sm items-center gap-1.5">
                        <Input id="#" type="#" placeholder="New List" />
                        <Button type="submit">
                            + New List
                        </Button>
                    </div>
                </CardHeader>
            </Card>

        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5 place-items-center">
                <Card v-for="list in lists" :key="list.id" class="w-xs">
                        <CardHeader class="pb-3 ">
                            <div class="flex items-center justify-between border-b pb-2">
                                <CardTitle class="text-base">{{ list.title }}</CardTitle>
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
