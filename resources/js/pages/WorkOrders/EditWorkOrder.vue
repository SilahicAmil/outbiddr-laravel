<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';


// TODO: Make below into a type
defineProps<{
    workOrder: Array<{ id: string; title: string; status: string }>,
}>()

// TODO: Breadcrumbs
</script>

<template>
    <AppLayout>
        <div>
            <h1>Edit Work Orders Page</h1>
            <ul v-for="wo in workOrder" :key="wo.id">
                <li>{{wo.address}}</li>
                <li>{{wo.status}}</li>
                <li>{{wo.description}}</li>
                <li>{{wo.owner_name}}</li>
            </ul>
        </div>
    </AppLayout>
</template>

<style scoped></style>
