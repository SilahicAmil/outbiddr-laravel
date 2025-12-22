<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { workorders } from '@/routes';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: workorders().url },
    { title: 'Create Work Order', href: '/workorders/create' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Create Work Order</h1>
            <form>
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Title</label>
                    <input
                        type="text"
                        class="w-full rounded border px-2 py-1"
                    />
                </div>
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Description</label>
                    <textarea
                        class="w-full rounded border px-2 py-1"
                    ></textarea>
                </div>
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Address</label>
                    <textarea
                        class="w-full rounded border px-2 py-1"
                    ></textarea>
                </div>
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Status</label>
                    <select>
                        <option>Open</option>
                    </select>
                </div>
                <button
                    type="submit"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    Save Work Order
                </button>
            </form>
        </div>
    </AppLayout>
</template>
