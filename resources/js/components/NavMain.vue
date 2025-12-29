<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="">
        <SidebarGroupLabel>Outbiddr</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>

                <!-- Sub item if any -->
                <SidebarMenu v-if="item.subItems?.length" class="ml-2">
                    <SidebarMenuItem
                        v-for="sub in item.subItems"
                        :key="sub.title"
                    >
                        <SidebarMenuButton
                            as-child
                            :is-active="urlIsActive(sub.href, page.url)"
                            :tooltip="sub.title"
                        >
                            <Link :href="sub.href">
                                <component :is="sub.icon" />
                                <span>{{ sub.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
