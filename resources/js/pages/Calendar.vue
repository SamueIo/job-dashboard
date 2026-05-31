<script setup>
import { ref } from 'vue'

import StatsCard from '@/components/CalendarComponents/StatsCard.vue'
import CalendarView from '@/components/CalendarComponents/CalendarView.vue'
import UpcomingInterviewsLite from '@/components/CalendarComponents/UpcomingInterviewsLite.vue'
import EventDrawer from '@/components/CalendarComponents/EventDrawer.vue'
import Actions from '@/components/CalendarComponents/Actions.vue'
import ReadyToSyncCard from '@/components/CalendarComponents/ReadyToSyncCard.vue'
import CreateEventModal from '@/components/CalendarComponents/CreateEventModal.vue'
import { useUiStore } from '@/stores/useUiStore'

import { useCalendarStats } from '@/composables/useCalendarStats'


const uiStore = useUiStore()
/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },

    stats: {
        type: Object,
        default: () => ({}),
    },

    upcomingInterviews: {
        type: Array,
        default: () => [],
    },

    googleCalendarSyncEnabled: {
        type: Boolean,
        default: false,
    },
    readyToSync: Array,
})

/*
|--------------------------------------------------------------------------
| STATS
|--------------------------------------------------------------------------
*/

const cards = useCalendarStats(props.stats)


/*
|--------------------------------------------------------------------------
| DRAWER
|--------------------------------------------------------------------------
*/

const showEventDrawer = ref(false)

const selectedEvent = ref(null)
const closeDrawer = () => {
    showEventDrawer.value = false
    
    editingEvent.value = null
}

/*
|--------------------------------------------------------------------------
| HANDLERS
|--------------------------------------------------------------------------
*/

const handleEventClick = (event) => {

    selectedEvent.value = event
    
    showEventDrawer.value = true
}

const showCreateModal = ref(false)
const selectedDate = ref(null)
// editing event for createEventModal
const editingEvent = ref(null)

const openCreateModal = (payload) => {

    editingEvent.value = null
    selectedDate.value = payload.start_at

    showCreateModal.value = true
}


const handleEditEvent = (event) => {

    editingEvent.value = event

    showEventDrawer.value = false
    showCreateModal.value = true
}

const closeModal = () => {

    showCreateModal.value = false

    editingEvent.value = null
}

</script>

<template>
    <div
        class="
            min-h-screen
            bg-gray-50
            dark:bg-[#0c0c0c]
            p-4
        "
    >

        <!-- STATS -->
        
        <StatsCard v-if="uiStore.statsOpen" :cards="cards" />

        <!-- GRID -->
        <div
            :class="[
                uiStore.statsOpen ? 'mt-6' : 'mt-0',
                'grid grid-cols-1 gap-8 xl:grid-cols-[1fr_320px]'
            ]"
        >

            <!-- CALENDAR -->
            <CalendarView
                :events="events"
                @event-click="handleEventClick"
                @date-click="openCreateModal"
            />

            <!-- SIDEBAR -->
            <div class="space-y-6">

                <!-- UPCOMING -->
                <div
                    class="
                        rounded-2xl
                        border border-gray-200
                        bg-white                 
                        dark:border-white/5
                        dark:bg-[#111111]
                        p-5
                    "
                >
                    <UpcomingInterviewsLite
                        :upcomingInterviews="upcomingInterviews"
                    />

                </div>
                              <div
                    class="
                        rounded-2xl
                        dark:border-white/5
                        dark:bg-[#111111]
                        p-5
                    "
                >
                    <ReadyToSyncCard
                        :events="readyToSync"
                    />
                </div>

                <!-- ACTIONS -->
                <Actions @create-event="showCreateModal= true"/>
                <!-- Create modal -->
                <CreateEventModal 
                    :selected-date="selectedDate"
                    :show="showCreateModal"
                    @close="closeModal"
                    :event="editingEvent"
                />

            </div>

        </div>

        <!-- DRAWER -->
        <EventDrawer
            :show="showEventDrawer"
            :event="selectedEvent"
            @close="closeDrawer"
            @edit-event="handleEditEvent"
        />

    </div>
</template>