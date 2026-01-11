<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, WorkOrder } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

import Card from '@/components/ui/card/Card.vue';
import { workorders } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: workorders().url },
];

const { all_workorders } = defineProps<{
    all_workorders: WorkOrder[];
}>();

const groupedWorkOrders = {
    assigned: all_workorders.filter(
        (wo) => wo.status.toLowerCase() === 'assigned',
    ),
    open: all_workorders.filter((wo) => wo.status.toLowerCase() === 'open'),
    completed: all_workorders.filter(
        (wo) => wo.status.toLowerCase() === 'completed',
    ),
};


console.log(all_workorders);
</script>

<!-- Make this better. Cards and styling overall-->
<template>
    <Head title="Work Orders" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="">
            <div class="flex gap-4">
                <Card
                    class="h-96 flex-1 overflow-y-auto"
                    title="Assigned Work Orders"
                    :len="groupedWorkOrders.assigned.length"
                    color="green"
                >
                    <ul>
                        <li
                            v-for="wo in groupedWorkOrders.assigned"
                            :key="wo.id"
                        >
                            <Link :href="`workorders/${wo.id}`">{{
                                wo.title
                            }}</Link>
                        </li>
                    </ul>
                </Card>

                <Card
                    class="h-96 flex-1 overflow-y-auto"
                    title="Open Work Orders"
                    :len="groupedWorkOrders.open.length"
                    color="yellow"
                >
                    <ul>
                        <li v-for="wo in groupedWorkOrders.open" :key="wo.id">
                            <Link :href="`workorders/${wo.id}`">{{
                                wo.title
                            }}</Link>
                        </li>
                    </ul>
                </Card>

                <Card
                    class="h-96 flex-1 overflow-y-auto"
                    title="Completed Work Orders"
                    :len="groupedWorkOrders.completed.length"
                    color="red"
                >
                    <ul>
                        <li
                            v-for="wo in groupedWorkOrders.completed"
                            :key="wo.id"
                        >
                            <Link :href="`workorders/${wo.id}`">{{
                                wo.title
                            }}</Link>
                            <br />
                            <p class="mt-4">
                                {{ wo.description.substring(0, 75) }}...
                            </p>
                            <p class="mb-4">
                                WO Completed At:
                                {{ new Date(wo.updated_at).toLocaleString() }}
                            </p>
                            <div>DIVIDE</div>
                        </li>
                    </ul>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
