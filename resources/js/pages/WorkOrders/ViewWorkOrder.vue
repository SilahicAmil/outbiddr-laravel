<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, WorkOrder } from '@/types';
import { route } from 'ziggy-js';

const { workOrder } = defineProps<{
    workOrder: WorkOrder[];
}>();

const wo_id = workOrder?.data.id ?? 'NULL';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: route('workorders') },
    { title: 'View Work Order', href: route('workorders.show', { workOrder: wo_id }) },
]

</script>
<!--TODO: Make this an actual page that users can view stuff nicely-->

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div>Work Order # 123</div>
        <div>
            <Link :href="`/workorders/edit/${wo_id}`">Edit</Link>
        </div>

        <ul v-for="wo in workOrder" :key="wo.id">
            <li>{{ wo.address }}</li>
        </ul>
    </AppLayout>
</template>
