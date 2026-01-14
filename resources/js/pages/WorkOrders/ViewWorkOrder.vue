<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, WorkOrder } from '@/types';
import { route } from 'ziggy-js';
import { Edit, MapPin, Calendar, User, Building2, FileText } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { edit } from '@/routes/workorders';

const { workOrder } = defineProps<{
    workOrder: WorkOrder[];
}>();

const wo_id = workOrder?.data.id ?? 'NULL';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: route('workorders') },
    {
        title: 'View Work Order',
        href: route('workorders.show', { workOrder: wo_id }),
    },
];

const getStatusColor = (status: string) => {
    const statusLower = status.toLowerCase();
    console.log(status);
    if (statusLower === 'assigned') return 'bg-green-400 text-black';
    if (statusLower === 'open') return 'bg-yellow-400 text-black';
    if (statusLower === 'completed') return 'bg-red-400 text-black';
    return 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString();
};

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8 p-6">
            <!-- Header with Edit Button -->
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <h1 v-if="(workOrder as any).data.title" class="text-3xl font-bold">
                    {{ (workOrder as any).data.title }}
                    </h1>
                    <p class=" text-lg text-gray-400 mt-1">
                        Work Order #&nbsp;{{ wo_id as number }}
                    </p>
                </div>
                    <!-- Status Section -->
                    <div class="flex items-center justify-end">
                        <Badge :class="getStatusColor(workOrder.data.status)">
                            <span class="font-bold">{{ workOrder.data.status.toUpperCase() }}</span>
                        </Badge>
                    </div>
                <Link :href="edit({ workOrder: wo_id }).url" v-if="workOrder.data.status != 'completed'">
                    <Button variant="outline" class="cursor-pointer">
                        <Edit class="h-4 w-4 mr-2" />
                        Edit Work Order
                    </Button>
                </Link>
            </div>

            <!-- Main Content: Info and Map -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- General Information Section -->
                <div class="lg:col-span-2 space-y-6">


                    <!-- Description Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <FileText class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Description</h2>
                        </div>
                        <p class="text-gray-300 whitespace-pre-wrap">
                            {{ workOrder.data.description || 'No description provided' }}
                        </p>
                    </div>

                    <!-- Address Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <MapPin class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Address</h2>
                        </div>
                        <p class="text-gray-700">
                            {{ workOrder.data.address || 'No address provided' }}
                        </p>
                    </div>

                    <!-- Additional Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Owner Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <Building2 class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Owner</h2>
                            </div>
                            <p class="text-gray-700">
                                {{ workOrder.data.owner_name.toUpperCase() }}
                            </p>
                        </div>

                        <!-- Assigned User Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <User class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Assigned To</h2>
                            </div>
                            <p class="text-gray-700">
                                {{ workOrder.data.assigned_user.toUpperCase() || 'Unassigned' }}
                            </p>
                        </div>

                        <!-- Created Date Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <Calendar class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Created</h2>
                            </div>
                            <p class="text-gray-700">
                                {{ formatDate(workOrder.data.created_at) }}
                            </p>
                        </div>

                        <!-- Updated Date Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <Calendar class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Last Updated</h2>
                            </div>
                            <p class="text-gray-700">
                                {{ formatDate(workOrder.data.updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="lg:col-span-1">
                    <div class="border rounded-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Location</h2>
                        <div
                            class="w-full h-96 bg-gray-100 rounded-md flex items-center justify-center border-2 border-dashed border-gray-300"
                        >
                            <div class="text-center p-6">
                                <MapPin
                                    class="h-12 w-12 mx-auto text-gray-400 mb-4"
                                />
                                <p class="text-gray-500 font-medium">
                                    Map Placeholder
                                </p>
                                <p class="text-sm text-gray-400 mt-2">
                                    {{ workOrder.address || 'No address available' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="border rounded-md p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Work Order Notes</h2>
                    <Button variant="outline">
                        Add Note
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
