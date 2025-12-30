<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, WorkOrder } from '@/types';
import { route } from 'ziggy-js';

const { workOrder } = defineProps<{
    workOrder: WorkOrder[];
}>();

const wo_id = workOrder?.data.id;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: route('workorders') },
    { title: 'View Work Order', href: route('workorders.show', { workOrder: wo_id }) },
    { title: 'Edit Work Order', href: route('workorders.edit', { workOrder: wo_id }) },
]
</script>

<!-- TODO: Make this an actual form. Probably a reusable component or use existing-->

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <h1>Edit Work Orders Page</h1>
            <ul v-for="wo in workOrder" :key="wo.id">
                <li>{{ wo.address }}</li>
                <li>{{ wo.status }}</li>
                <li>{{ wo.description }}</li>
                <li>{{ wo.owner_name }}</li>
            </ul>
        </div>
    </AppLayout>
</template>

<style scoped></style>
