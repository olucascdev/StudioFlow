<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { h } from 'vue'
import * as z from 'zod'

import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'


const formSchema = toTypedSchema(z.object({
    username: z.string().min(2).max(50),
}))

function onSubmit(values: any) {
    toast({
        title: 'You submitted the following values:',
        description: h('pre', { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify(values, null, 2))),
    })
}
</script>

<template>
    <Form v-slot="{ handleSubmit }" as="" keep-values :validation-schema="formSchema">
        <Dialog>
            <DialogTrigger as-child>
                <Button variant="outline" class="w-8 h-8 text-sm">+</Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Create New Task</DialogTitle>
                </DialogHeader>

                <form id="dialogForm" @submit="handleSubmit($event, onSubmit)" class="space-y-6 p-6">
                    <!-- Title Field -->
                    <FormField v-slot="{ componentField }" name="title">
                        <FormItem class="space-y-2">
                            <FormLabel class="text-sm font-medium">Title</FormLabel>
                            <FormControl>
                                <Input
                                    type="text"
                                    placeholder="Enter task title"
                                    v-bind="componentField"
                                    class="h-10"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <!-- Description Field (nullable) -->
                    <FormField v-slot="{ componentField }" name="description">
                        <FormItem class="space-y-2">
                            <FormLabel class="text-sm font-medium">Description</FormLabel>
                            <FormControl>
                                <Input
                                    type="text"
                                    placeholder="Enter task description (optional)"
                                    v-bind="componentField"
                                    class="h-10"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <!-- Status and Due Date Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Status Field -->
                        <FormField v-slot="{ componentField }" name="status">
                            <FormItem class="space-y-2">
                                <FormLabel class="text-sm font-medium">Status</FormLabel>
                                <Select v-bind="componentField">
                                    <FormControl>
                                        <SelectTrigger class="h-10">
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectItem value="pending">Pending</SelectItem>
                                        <SelectItem value="completed">Completed</SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Due Date Field -->
                        <FormField v-slot="{ componentField }" name="due_date">
                            <FormItem class="space-y-2">
                                <FormLabel class="text-sm font-medium">Due Date</FormLabel>
                                <FormControl>
                                    <Input
                                        type="date"
                                        v-bind="componentField"
                                        class="h-10"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <DialogFooter class="pt-4">
                        <div class="flex justify-end gap-3 w-full">
                            <Button type="button" variant="outline" @click="closeDialog" class="px-6">
                                Cancel
                            </Button>
                            <Button type="submit" class="px-6">
                                Create Task
                            </Button>
                        </div>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </Form>
</template>
