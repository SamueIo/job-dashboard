<script setup lang="ts">
import { computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { Calendar } from 'lucide-vue-next'
import AnalisingSpinner from '../AnalisingSpinner.vue'

const page = usePage()

const isEnabled = computed(() => page.props.googleCalendarSyncEnabled)

const syncing = ref(false)

const toggleGoogleSync = () => {
    syncing.value = true

    router.post('/calendar/google-sync', {}, {
        preserveScroll: true,
        onFinish: () => syncing.value = false
    })
}
</script>

<template>
    <button
        @click="toggleGoogleSync"
        class="group flex w-full items-center gap-3 py-2 "
    >
        <!-- LEFT -->
        <div class="flex items-center gap-3 min-w-0">
            <Calendar class="h-4 w-4 shrink-0" />
        
            <span class="truncate group-data-[collapsible=icon]:hidden">
                Google Calendar Sync
            </span>
        </div>
    
        <!-- RIGHT -->
        <div class="ml-auto flex items-center">
            <AnalisingSpinner v-if="syncing" />
        
            <span
                v-else
                class="h-2.5 w-2.5 rounded-full group-data-[collapsible=icon]:hidden"
                :class="isEnabled ? 'bg-green-500' : 'bg-white/20'"
            />
        </div>
    </button>
</template>