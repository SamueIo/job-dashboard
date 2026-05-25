<script setup>

import { BarChart3 } from 'lucide-vue-next'
import { useUiStore } from '@/stores/useUiStore';

const ui = useUiStore()

</script>

<template>

    <button
        @click="ui.toggleStats()"
        :class= "ui.statsOpen ?  ' bg-white/3' :' bg-transparent' "
        class="
            group rounded-lg
            flex w-full items-center
            py-2  dark:text-white/80
            transition-all duration-200
            hover:bg-white/5
            gap-3
            px-3
        "
    >

        <!-- LEFT -->
        <div class="flex items-center gap-3 min-w-0">

            <BarChart3 class="h-4 w-4  shrink-0" />

            <span
                class="
                    text-sm
                    truncate
                    group-data-[collapsible=icon]:hidden
                    
                "
            >
                {{ ui.statsOpen ?  'Hide statistic' :'Show statistic' }}
            </span>

        </div>


    </button>

</template>