<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent,  CardFooter, CardHeader, CardTitle } from '@/components/ui/card';


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
                    <CardTitle>Today</CardTitle>
                    <div class="flex w-full max-w-sm items-center gap-1.5">
                        <Input id="#" type="#" placeholder="New Task" />
                        <Button type="submit">
                            + New Task
                        </Button>
                    </div>
                </CardHeader>
            </Card>

            <Card class="flex w-sm">
                <CardHeader>
                    <CardTitle>Task</CardTitle>
                    <div class="flex w-full max-w-sm items-center gap-1.5">
                    </div>
                </CardHeader>
            </Card>

        </div>
    </AppLayout>
</template>
