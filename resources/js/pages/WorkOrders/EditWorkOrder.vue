<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, WorkOrder } from '@/types';
import { route } from 'ziggy-js';
import { reactive, ref } from 'vue';
import { FileText, MapPin, CheckCircle, X, Save, Calendar, Building2, User, Type } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const { workOrder } = defineProps<{
    workOrder: WorkOrder[];
}>();

const wo_id = workOrder?.data.id;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: route('workorders') },
    { title: 'View Work Order', href: route('workorders.show', { workOrder: wo_id }) },
    { title: 'Edit Work Order', href: route('workorders.edit', { workOrder: wo_id }) },
];

const formData = reactive({
    title: workOrder?.data.title ?? '',
    description: workOrder?.data.description ?? '',
    address: workOrder?.data.address ?? '',
    status: workOrder?.data.status?.toLowerCase()
});

const isSubmitting = ref(false);
const errors = ref<Record<string, string>>({});

const getStatusColor = (status: string) => {
    const statusLower = status.toLowerCase();
    if (statusLower === 'assigned') return 'bg-green-400 text-black';
    if (statusLower === 'open') return 'bg-yellow-400 text-black';
    if (statusLower === 'completed') return 'bg-red-400 text-black';
    return 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString();
};

const cancel = () => {
    window.location.href = route('workorders.show', { workOrder: wo_id });
};

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

async function submitForm() {
    isSubmitting.value = true;
    errors.value = {};

    try {
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

        if (!req.ok && res.errors) {
            errors.value = res.errors;
            return;
        }

        if (res) {
            window.location.href = '/workorders/' + wo_id;
        }
    } catch (error) {
        console.error('Error submitting form:', error);
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submitForm" class="space-y-8 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <h1 class="text-3xl font-bold">Edit Work Order</h1>
                    <p class="text-lg text-gray-400 mt-1">
                        Work Order #{{ wo_id }}
                    </p>
                </div>

                <!-- Current Status Badge -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">Current Status:</span>
                    <Badge :class="getStatusColor(formData.status)">
                        <span class="font-bold">{{ formData.status.toUpperCase() }}</span>
                    </Badge>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <Button type="button" variant="outline" @click="cancel" :disabled="isSubmitting">
                        <X class="h-4 w-4 mr-2" />
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <Save class="h-4 w-4 mr-2" />
                        {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Editable Fields Section -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Title Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <Type class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Title</h2>
                        </div>
                        <Input
                            id="title"
                            v-model="formData.title"
                            type="text"
                            placeholder="Enter work order title"
                            class="w-full"
                            required
                        />
                        <p v-if="errors.title" class="text-sm text-red-500 mt-2">{{ errors.title }}</p>
                    </div>

                    <!-- Description Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <FileText class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Description</h2>
                        </div>
                        <textarea
                            id="description"
                            v-model="formData.description"
                            rows="5"
                            placeholder="Enter work order description"
                            class="flex min-h-[120px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                        ></textarea>
                        <p v-if="errors.description" class="text-sm text-red-500 mt-2">{{ errors.description }}</p>
                    </div>

                    <!-- Address Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <MapPin class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Address</h2>
                        </div>
                        <textarea
                            id="address"
                            v-model="formData.address"
                            rows="3"
                            placeholder="Enter work order address"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                        ></textarea>
                        <p v-if="errors.address" class="text-sm text-red-500 mt-2">{{ errors.address }}</p>
                    </div>

                    <!-- Status Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <CheckCircle class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Status</h2>
                        </div>
                        <div class="flex gap-3">
                            <label
                                v-for="status in ['open', 'assigned', 'completed']"
                                :key="status"
                                class="flex items-center gap-2 px-4 py-3 border rounded-md cursor-pointer transition-all"
                                :class="formData.status === status
                                    ? 'border-primary bg-primary/10 ring-2 ring-primary/50'
                                    : 'border-input hover:border-gray-400'"
                            >
                                <input
                                    type="radio"
                                    :value="status"
                                    v-model="formData.status"
                                    class="sr-only"
                                />
                                <Badge :class="getStatusColor(status)">
                                    <span class="font-bold">{{ status.toUpperCase() }}</span>
                                </Badge>
                            </label>
                        </div>
                        <p v-if="errors.status" class="text-sm text-red-500 mt-2">{{ errors.status }}</p>
                    </div>
                </div>

                <!-- Sidebar: Read-only Information -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Work Order Info Card -->
                    <div class="border rounded-md p-6 bg-muted/30">
                        <h2 class="text-lg font-semibold mb-4">Work Order Info</h2>

                        <div class="space-y-4">
                            <!-- Owner -->
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 mb-1">
                                    <Building2 class="h-4 w-4" />
                                    <span class="text-sm">Owner</span>
                                </div>
                                <p class="font-medium">
                                    {{ workOrder.data.owner_name?.toUpperCase() || 'Unknown' }}
                                </p>
                            </div>

                            <!-- Assigned To -->
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 mb-1">
                                    <User class="h-4 w-4" />
                                    <span class="text-sm">Assigned To</span>
                                </div>
                                <p class="font-medium">
                                    {{ workOrder.data.assigned_user?.toUpperCase() || 'Unassigned' }}
                                </p>
                            </div>

                            <!-- Created -->
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 mb-1">
                                    <Calendar class="h-4 w-4" />
                                    <span class="text-sm">Created</span>
                                </div>
                                <p class="font-medium">
                                    {{ formatDate(workOrder.data.created_at) }}
                                </p>
                            </div>

                            <!-- Last Updated -->
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 mb-1">
                                    <Calendar class="h-4 w-4" />
                                    <span class="text-sm">Last Updated</span>
                                </div>
                                <p class="font-medium">
                                    {{ formatDate(workOrder.data.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="border rounded-md p-6 border-dashed">
                        <h3 class="text-sm font-semibold text-gray-500 mb-3">Quick Tips</h3>
                        <ul class="text-sm text-gray-400 space-y-2">
                            <li>- Changes are saved when you click "Save Changes"</li>
                            <li>- Changing status to "Completed" will close this work order</li>
                            <li>- Use a detailed description for better tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
