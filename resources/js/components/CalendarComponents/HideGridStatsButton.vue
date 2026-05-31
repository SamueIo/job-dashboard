<script setup lang="ts">
import { BarChart3 } from 'lucide-vue-next'
import { useUiStore } from '@/stores/useUiStore'

const ui = useUiStore()
</script>

<template>
    <button
        @click="ui.toggleStats()"
        class="flex w-full items-center gap-2"
    >
        <BarChart3 class="h-4 w-4 shrink-0" />

        <span class="truncate group-data-[collapsible=icon]:hidden">
            {{ ui.statsOpen ? 'Hide statistic' : 'Show statistic' }}
        </span>
    </button>
</template>