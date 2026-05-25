<script setup>
import { computed, ref } from 'vue'

import { router, usePage } from '@inertiajs/vue3'
import { Calendar } from 'lucide-vue-next'
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
        :class= "googleCalendarSyncEnabled ?  ' bg-white/3' :' bg-transparent' "
        class="
            group rounded-lg
            flex w-full items-center
            py-2 dark:text-white
            transition-all duration-200 
            hover:bg-white/5
            gap-3
            px-3
        "
  >

    <!-- LEFT SIDE -->
    <div class="flex items-center gap-3 min-w-0">

      <Calendar class="h-4 w-4 shrink-0" />

      <!-- TEXT hides when sidebar is collapsed -->
      <span
        class="text-sm truncate group-data-[collapsible=icon]:hidden  "
      >
        Google Calendar Sync
      </span>

    </div>

    <!-- RIGHT SIDE -->
    <div class="flex items-center ml-auto">

      <AnalisingSpinner v-if="syncing" />

      <div
        v-else
        :class="[
          'h-2.5 w-2.5 rounded-full group-data-[collapsible=icon]:hidden',
          googleCalendarSyncEnabled
            ? 'bg-green-500'
            : 'bg-black/20 dark:bg-white/20'
        ]"
      />

    </div>

  </button>
</template>