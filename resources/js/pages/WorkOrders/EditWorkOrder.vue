<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, WorkOrder } from '@/types';
import { route } from 'ziggy-js';
import { reactive } from 'vue';

const { workOrder } = defineProps<{
    workOrder: WorkOrder[];
}>();

const wo_id = workOrder?.data.id;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: route('workorders') },
    { title: 'View Work Order', href: route('workorders.show', { workOrder: wo_id }) },
    { title: 'Edit Work Order', href: route('workorders.edit', { workOrder: wo_id }) },
]
console.log(workOrder);
const formData = reactive({
    title: workOrder?.data.title ?? '',
    description: workOrder?.data.description ?? '',
    address: workOrder?.data.address ?? '',
    status: workOrder?.data.status.toUpperCase() ?? '',
});
console.log(formData);
const cancel = () => {
    return window.location.href = route('workorders.show', { workOrder: wo_id });
}

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

async function submitForm() {
    console.log('form data', formData);
    const req = await fetch(route('workorders.update', { workOrder: wo_id }), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            Accept: 'application/json',
        },
        body: JSON.stringify(formData),
    });

    const res = await req.json();
    // TODO: Handle Form Validation Errors and display them to the user using the AlertError component
    if (res) {
        return window.location.href = '/workorders/' + wo_id;
    }
}


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Make an actual form. Probably a reusable component or use existing-->
        <form @submit.prevent="submitForm">
            <div>
                <label for="title">Title</label>
                <input type="text" id="title" v-model="formData.title">
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" v-model="formData.description"></textarea>
            </div>
            <div>
                <label for="address">Address</label>
                <textarea id="address" v-model="formData.address"></textarea>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" v-model="formData.status">
                    <option value="open">Open</option>
                    <option value="assigned">Assigned</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <button type="submit">Save</button>
            <button type="button" @click="cancel">Cancel</button>
        </form>
        
    </AppLayout>
</template>

<style scoped></style>
