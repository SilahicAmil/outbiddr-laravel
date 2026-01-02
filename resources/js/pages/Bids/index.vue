<script setup lang="ts">
// TODO: IMplement this
// Bids page will only show where assigned_user_id == NULL and staus == 'OPEN'
// Update BidPolicy
// Update WO page for bidding dates
// Create a WO seeder/factory
// Create a Bid seeder/factory

import { WorkOrder } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';

const { open_bids } = defineProps<{
    open_bids: WorkOrder[];
}>();
console.log(open_bids);
</script>

<template>
 <AppLayout>
    <div>
        <h1>Open Bids</h1>
    </div>
 </AppLayout>
</template>

<style scoped>

</style>
