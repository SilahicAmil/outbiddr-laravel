<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type WorkOrder } from '@/types';
import { route } from 'ziggy-js';
import { DollarSign, MapPin, FileText, Building2, Calendar, Eye, Send } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatDate } from '@/utils/date';

const { open_bids } = defineProps<{
    open_bids: { data: WorkOrder[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Open Bids', href: route('bids') },
];

// Track bid amounts for each work order
const bidAmounts = reactive<Record<number, string>>({});
const submittingBids = reactive<Record<number, boolean>>({});
const bidMessages = reactive<Record<number, { type: 'success' | 'error'; text: string }>>({});

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

async function submitBid(workOrderId: number) {
    const amount = bidAmounts[workOrderId];
    if (!amount || parseFloat(amount) <= 0) {
        bidMessages[workOrderId] = { type: 'error', text: 'Please enter a valid bid amount.' };
        return;
    }

    submittingBids[workOrderId] = true;
    bidMessages[workOrderId] = { type: 'success', text: '' };

    try {
        const response = await fetch(route('bids.store', { workOrder: workOrderId }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify({ amount: parseFloat(amount) }),
        });

        const data = await response.json();

        if (response.ok) {
            bidMessages[workOrderId] = { type: 'success', text: data.message };
            bidAmounts[workOrderId] = '';
        } else {
            bidMessages[workOrderId] = { type: 'error', text: data.message || 'Failed to place bid.' };
        }
    } catch (error) {
        bidMessages[workOrderId] = { type: 'error', text: 'An error occurred. Please try again.' };
    } finally {
        submittingBids[workOrderId] = false;
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <h1 class="text-3xl font-bold">Open Bids</h1>
                    <p class="text-lg text-gray-400 mt-1">
                        Browse and bid on available work orders
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!open_bids?.data?.length" class="border rounded-md p-12 text-center">
                <DollarSign class="h-12 w-12 mx-auto text-gray-400 mb-4" />
                <h2 class="text-xl font-semibold text-gray-300 mb-2">No Open Work Orders</h2>
                <p class="text-gray-400">There are currently no work orders available for bidding.</p>
            </div>

            <!-- Work Orders Grid -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div
                    v-for="workOrder in open_bids.data"
                    :key="workOrder.id"
                    class="border rounded-md overflow-hidden"
                >
                    <!-- Card Header -->
                    <div class="border-b p-4 bg-muted/30">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">{{ workOrder.title }}</h3>
                                <p class="text-sm text-gray-400">Work Order #{{ workOrder.id }}</p>
                            </div>
                            <Badge class="bg-yellow-400 text-black">
                                <span class="font-bold">OPEN</span>
                            </Badge>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-4">
                        <!-- Description -->
                        <div>
                            <div class="flex items-center gap-2 text-gray-500 mb-1">
                                <FileText class="h-4 w-4" />
                                <span class="text-sm font-medium">Description</span>
                            </div>
                            <p class="text-gray-300 text-sm line-clamp-2">
                                {{ workOrder.description || 'No description provided' }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div>
                            <div class="flex items-center gap-2 text-gray-500 mb-1">
                                <MapPin class="h-4 w-4" />
                                <span class="text-sm font-medium">Location</span>
                            </div>
                            <p class="text-gray-300 text-sm">
                                {{ workOrder.address || 'No address provided' }}
                            </p>
                        </div>

                        <!-- Owner & Date -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 mb-1">
                                    <Building2 class="h-4 w-4" />
                                    <span class="text-sm font-medium">Posted By</span>
                                </div>
                                <p class="text-gray-300 text-sm">{{ workOrder.owner_name }}</p>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 mb-1">
                                    <Calendar class="h-4 w-4" />
                                    <span class="text-sm font-medium">Posted</span>
                                </div>
                                <p class="text-gray-300 text-sm">{{ formatDate(workOrder.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bid Form -->
                    <div class="border-t p-4 bg-muted/20">
                        <div class="flex items-center gap-2 mb-3">
                            <DollarSign class="h-4 w-4 text-gray-500" />
                            <span class="text-sm font-medium">Place Your Bid</span>
                        </div>

                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                                <Input
                                    v-model="bidAmounts[workOrder.id]"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="Enter amount"
                                    class="pl-7"
                                    :disabled="submittingBids[workOrder.id]"
                                />
                            </div>
                            <Button
                                @click="submitBid(workOrder.id)"
                                :disabled="submittingBids[workOrder.id]"
                            >
                                <Send class="h-4 w-4 mr-2" />
                                {{ submittingBids[workOrder.id] ? 'Placing...' : 'Place Bid' }}
                            </Button>
                        </div>

                        <!-- Message -->
                        <p
                            v-if="bidMessages[workOrder.id]?.text"
                            :class="[
                                'text-sm mt-2',
                                bidMessages[workOrder.id].type === 'success' ? 'text-green-500' : 'text-red-500'
                            ]"
                        >
                            {{ bidMessages[workOrder.id].text }}
                        </p>

                        <!-- View Details Link -->
                        <div class="mt-3 pt-3 border-t border-dashed">
                            <Link
                                :href="route('workorders.show', { workOrder: workOrder.id })"
                                class="text-sm text-primary hover:underline flex items-center gap-1"
                            >
                                <Eye class="h-3 w-3" />
                                View Full Details
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
