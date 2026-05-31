<script setup lang="ts">
import { computed } from 'vue'

import {
    SidebarMenu,
    SidebarMenuItem,
    SidebarMenuButton,
} from '@/components/ui/sidebar'

import type { SidebarActionItem } from '@/types/sidebar'

const props = defineProps<{
    items: SidebarActionItem[]
}>()

const isVisible = (item: SidebarActionItem): boolean => {
    if (!item.show) return true

    if (typeof item.show === 'function') {
        return item.show()
    }

    // computed ref
    if (typeof item.show === 'object' && 'value' in item.show) {
        return item.show.value
    }

    return Boolean(item.show)
}

const visibleItems = computed(() =>
    props.items.filter(isVisible)
)
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem
            v-for="item in visibleItems"
            :key="item.title"
        >
            <SidebarMenuButton :tooltip="item.title">
                <component :is="item.component" />
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>