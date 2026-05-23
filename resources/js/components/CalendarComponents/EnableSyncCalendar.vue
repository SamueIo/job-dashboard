<script setup>
import { computed, ref } from 'vue'

import { router, usePage } from '@inertiajs/vue3'
import { Mail } from 'lucide-vue-next'
import AnalisingSpinner from '../AnalisingSpinner.vue'

const page = usePage()

const googleCalendarSyncEnabled = computed(
    () => page.props.googleCalendarSyncEnabled
)


const syncing = ref(false)

const toggleGoogleSync = () => {

    syncing.value = true

    router.post(
        '/calendar/google-sync',
        {},
        {
            preserveScroll: true,

            onFinish: () => {
                syncing.value = false
            }
        }
    )
}

</script>

<template>
    <button
    @click="toggleGoogleSync()"
        class="
            flex w-full items-center justify-between
    
            rounded-2xl
    
            border border-white/10
    
            bg-white/3
    
            px-3 py-2
    
            text-white
    
            transition-all duration-200
    
            hover:bg-white/5
        "
    >

    <div class="flex items-center gap-3">

        <Mail class="h-4 w-4" />

        <span class="text-sm">
            Google Calendar Sync
        </span>

    </div>
    <div v-if="syncing">
        <AnalisingSpinner/>
    </div>
    <div v-else
        :class="[
            'h-2.5 w-2.5 rounded-full',

            googleCalendarSyncEnabled
                ? 'bg-green-500'
                : 'bg-white/20'
        ]"
    />

</button>
</template>