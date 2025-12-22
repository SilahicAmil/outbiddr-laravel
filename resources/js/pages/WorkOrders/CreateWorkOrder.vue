<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { workorders } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { reactive } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: workorders().url },
    { title: 'Create Work Order', href: '/workorders/create' },
];

const formData = reactive({
    title: '',
    description: '',
    address: '',
    status: '',
});

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

async function submitForm() {
    console.log('form data', formData);
    const req = await fetch('/workorders/create', {
        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            Accept: 'application/json',
        },
        body: JSON.stringify(formData),
    });

    const res = await req.json();
    console.log(res);
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Create Work Order</h1>
            <form @submit.prevent="submitForm">
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Title</label>
                    <input
                        v-model="formData.title"
                        type="text"
                        class="w-full rounded border px-2 py-1"
                    />
                </div>
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Description</label>
                    <textarea
                        v-model="formData.description"
                        class="w-full rounded border px-2 py-1"
                    ></textarea>
                </div>
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Address</label>
                    <textarea
                        v-model="formData.address"
                        class="w-full rounded border px-2 py-1"
                    ></textarea>
                </div>
                <div class="mb-4">
                    <label class="mb-1 block font-semibold">Status</label>
                    <select v-model="formData.status">
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
