<script setup lang="ts">
import { reactive, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';
import { FileText, MapPin, Calendar, CheckCircle, X, Save, Type } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: route('workorders') },
    { title: 'Create Work Order', href: route('workorders.create') },
];

// Get today's date in YYYY-MM-DD format for default values
const today = new Date().toISOString().split('T')[0];
const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

const formData = reactive({
    title: '',
    description: '',
    address: '',
    status: 'open',
    bidding_opens_at: today,
    bidding_ends_at: nextWeek,
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

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

const cancel = () => {
    window.location.href = route('workorders');
};

async function submitForm() {
    isSubmitting.value = true;
    errors.value = {};

    try {
        const req = await fetch(route('workorders.store'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
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
            window.location.href = route('workorders');
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
                    <h1 class="text-3xl font-bold">Create Work Order</h1>
                    <p class="text-lg text-gray-400 mt-1">
                        Fill in the details to create a new work order
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <Button type="button" variant="outline" @click="cancel" :disabled="isSubmitting">
                        <X class="h-4 w-4 mr-2" />
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <Save class="h-4 w-4 mr-2" />
                        {{ isSubmitting ? 'Creating...' : 'Create Work Order' }}
                    </Button>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Fields -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <Type class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Title</h2>
                        </div>
                        <Input
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
                            v-model="formData.description"
                            rows="5"
                            placeholder="Describe the work that needs to be done..."
                            class="flex min-h-[120px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            required
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
                            v-model="formData.address"
                            rows="3"
                            placeholder="Enter the work location address..."
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                        ></textarea>
                        <p v-if="errors.address" class="text-sm text-red-500 mt-2">{{ errors.address }}</p>
                    </div>
                </div>

                <!-- Right Column - Settings -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Bidding Period Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <Calendar class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Bidding Period</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-400 mb-1 block">Opens On</label>
                                <Input
                                    v-model="formData.bidding_opens_at"
                                    type="date"
                                    class="w-full"
                                    required
                                />
                                <p v-if="errors.bidding_opens_at" class="text-sm text-red-500 mt-1">{{ errors.bidding_opens_at }}</p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-400 mb-1 block">Closes On</label>
                                <Input
                                    v-model="formData.bidding_ends_at"
                                    type="date"
                                    class="w-full"
                                    required
                                />
                                <p v-if="errors.bidding_ends_at" class="text-sm text-red-500 mt-1">{{ errors.bidding_ends_at }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <CheckCircle class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Status</h2>
                        </div>
                        <div class="space-y-2">
                            <label
                                v-for="status in ['open']"
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

                    <!-- Info Card -->
                    <div class="border rounded-md p-6 border-dashed bg-muted/20">
                        <h3 class="text-sm font-semibold text-gray-400 mb-3">Info</h3>
                        <ul class="text-sm text-gray-500 space-y-2">
                            <li>- You will be set as the owner</li>
                            <li>- Contractors can bid during the bidding period</li>
                            <li>- You can accept a bid to assign a contractor</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
