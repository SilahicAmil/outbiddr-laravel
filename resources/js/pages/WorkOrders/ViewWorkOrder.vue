<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    workOrder: Object,
});

const wo_id = props.workOrder?.data.id ?? 'NULL';

// TODO: Breadcrumbs
</script>

<template>
    <AppLayout>
        <div>Work Order # 123</div>
        <div>
            <Link :href="`/workorders/edit/${wo_id}`">Edit</Link>
        </div>

        <ul v-for="wo in workOrder" :key="wo.id">
            <li>{{ wo.address }}</li>
        </ul>
    </AppLayout>
</template>
