<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

import SyncButton from './SyncButton.vue';
import { ref } from 'vue';
import { useEmailSync } from '@/composables/useEmailSync'
import { useCalendarSync } from '@/composables/useCalendarSync'


withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { syncEmails } = useEmailSync()
const { syncCalendar } = useCalendarSync()

const loading = ref(false)
const handleSyncGmail = async () => {
    loading.value = true

    try {
        await syncEmails()
        
    } finally {
        loading.value = false
    }
}
const handleSyncCalendar = async () => {
    loading.value = true

    try {
        await syncCalendar()
        
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
            <!-- RIGHT SIDE -->
    <div class="ml-auto flex items-center gap-2">
        <SyncButton label="Sync gmail"
            v-if="$page.component === 'Dashboard' || $page.component === 'Emails'"
            :loading="loading"
            @sync="handleSyncGmail"
        />
        <SyncButton label="Sync calendar"
            v-if="$page.component === 'Calendar'"
            :loading="loading"
            @sync="handleSyncCalendar"
        />
    </div>
    </header>
</template>
