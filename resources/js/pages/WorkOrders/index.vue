<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, WorkOrder } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronDown } from 'lucide-vue-next';

import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import WorkOrdersTable from '@/components/WorkOrdersTable.vue';
import { workorders } from '@/routes';
import { cn } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: workorders().url },
];

const { all_workorders } = defineProps<{
    all_workorders: WorkOrder[];
}>();

const groupedWorkOrders = {
    assigned: all_workorders.data.filter(
        (wo) => wo.status.toLowerCase() === 'assigned',
    ),
    open: all_workorders.data.filter((wo) => wo.status.toLowerCase() === 'open'),
    completed: all_workorders.data.filter(
        (wo) => wo.status.toLowerCase() === 'completed',
    ),
};

const assignedOpen = ref(false);
const openOpen = ref(false);
const completedOpen = ref(false);

</script>

<template>
    <Head title="Work Orders" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 max-w-7xl ">
            <!-- Assigned Work Orders Accordion -->
            <Collapsible
                v-model:open="assignedOpen"
                class="border rounded-lg"
            >
                <CollapsibleTrigger
                    class="flex w-full items-center justify-between p-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-3 h-3 rounded-full bg-green-500"
                        ></div>
                        <h3 class="text-lg font-semibold">
                            Assigned Work Orders
                        </h3>
                        <span
                            class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full"
                        >
                            {{ groupedWorkOrders.assigned.length }}
                        </span>
                    </div>
                    <ChevronDown
                        :class="
                            cn(
                                'h-4 w-4 transition-transform duration-200',
                                assignedOpen && 'rotate-180',
                            )
                        "
                    />
                </CollapsibleTrigger>
                <CollapsibleContent class="border-t">
                    <div class="p-4">
                        <WorkOrdersTable
                            :work-orders="groupedWorkOrders.assigned"
                            date-column-label="Created"
                        />
                    </div>
                </CollapsibleContent>
            </Collapsible>

            <!-- Open Work Orders Accordion -->
            <Collapsible
                v-model:open="openOpen"
                class="border rounded-lg"
            >
                <CollapsibleTrigger
                    class="flex w-full items-center justify-between p-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-3 h-3 rounded-full bg-yellow-500"
                        ></div>
                        <h3 class="text-lg font-semibold">
                            Open Work Orders
                        </h3>
                        <span
                            class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full"
                        >
                            {{ groupedWorkOrders.open.length }}
                        </span>
                    </div>
                    <ChevronDown
                        :class="
                            cn(
                                'h-4 w-4 transition-transform duration-200',
                                openOpen && 'rotate-180',
                            )
                        "
                    />
                </CollapsibleTrigger>
                <CollapsibleContent class="border-t">
                    <div class="p-4">
                        <WorkOrdersTable
                            :work-orders="groupedWorkOrders.open"
                            date-column-label="Created"
                        />
                    </div>
                </CollapsibleContent>
            </Collapsible>

            <!-- Completed Work Orders Accordion -->
            <Collapsible
                v-model:open="completedOpen"
                class="border rounded-lg"
            >
                <CollapsibleTrigger
                    class="flex w-full items-center justify-between p-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-3 h-3 rounded-full bg-red-500"
                        ></div>
                        <h3 class="text-lg font-semibold">
                            Completed Work Orders
                        </h3>
                        <span
                            class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full"
                        >
                            {{ groupedWorkOrders.completed.length }}
                        </span>
                    </div>
                    <ChevronDown
                        :class="
                            cn(
                                'h-4 w-4 transition-transform duration-200',
                                completedOpen && 'rotate-180',
                            )
                        "
                    />
                </CollapsibleTrigger>
                <CollapsibleContent class="border-t">
                    <div class="p-4">
                        <WorkOrdersTable
                            :work-orders="groupedWorkOrders.completed"
                            date-column-label="Completed"
                            :use-updated-at="true"
                        />
                    </div>
                </CollapsibleContent>
            </Collapsible>
        </div>
    </AppLayout>
</template>
