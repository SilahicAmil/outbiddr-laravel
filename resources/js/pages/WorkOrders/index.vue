<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

import Card from '@/components/ui/card/Card.vue';
import { workorders } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: workorders().url },
];

const props = defineProps({
    all_workorders: Array<{ id: string; title: string; status: string }>,
});
console.log(props.all_workorders);
</script>

<template>
    <Head title="Work Orders" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="ml-2">
            <ul class="flex flex-wrap">
                <li v-for="wo in all_workorders" :key="wo.id">
                    <Card class="m-6 h-64 w-64">
                        <Link :href="`workorders/${wo.id}`"> {{ wo.title }} - {{ wo.status }} </Link>
                    </Card>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
