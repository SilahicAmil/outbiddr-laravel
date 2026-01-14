<script setup lang="ts">
import { type WorkOrder } from '@/types';
import { Link } from '@inertiajs/vue3';
import workordersRoutes from '@/routes/workorders';

interface Props {
    workOrders: WorkOrder[];
    dateColumnLabel?: string;
    useUpdatedAt?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    dateColumnLabel: 'Created',
    useUpdatedAt: false,
});

const getDateValue = (wo: WorkOrder) => {
    return props.useUpdatedAt ? wo.updated_at : wo.created_at;
};

const getWorkOrderUrl = (workOrderId: number) => {
    return workordersRoutes.show({ workOrder: workOrderId }).url;
};

</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left border-b">
                <tr>
                    <th class="pb-2 pr-4">Title</th>
                    <th class="pb-2 pr-4">Address</th>
                    <th class="pb-2 pr-4">Owner</th>
                    <th class="pb-2 pr-4">Assigned To</th>
                    <th class="pb-2">{{ dateColumnLabel }}</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="wo in workOrders"
                    :key="wo.id"
                    class="border-b hover:bg-gray-50"
                >
                    <td class="py-2 pr-4">
                    
                        <Link
                            :href="getWorkOrderUrl(wo.id)"
                            class="text-blue-600 hover:text-blue-800 hover:underline font-medium"
                        >
                            {{ wo.title || `WO #${wo.id}` }}
                        </Link>
                    </td>
                    <td class="py-2 pr-4 text-gray-600">
                        {{ wo.address || 'N/A' }}
                    </td>
                    <td class="py-2 pr-4">
                        {{ wo.owner_name.toUpperCase() }}
                    </td>
                    <td class="py-2 pr-4">
                        {{ wo.assigned_user.toUpperCase() || 'Unassigned' }}
                    </td>
                    <td class="py-2 text-gray-500">
                        {{ new Date(getDateValue(wo)).toLocaleDateString() }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
