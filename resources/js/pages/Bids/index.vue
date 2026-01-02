<script setup lang="ts">
// TODO : Implement Bids Part of WOs

// WO Will own the bidding window/bids
// One to many relationship

// So bids table will be like a tracking table
// id, wo_id, event, metadata, created_at

// Work orders table needs to be updated to have
// bid_window_start, bid_window_end? or just simplify it to a time frame like 1wk, 1 day, 1hr and etc.
// bidding_opens_at, bidding_closes_at probably

// Bids page will only show where assigned_user_id == NULL and staus == 'OPEN'

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
