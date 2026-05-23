<script setup>
import { computed, ref, onMounted } from 'vue'

import FullCalendar from '@fullcalendar/vue3'

import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

import '../../../css/calendar.css'

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
})

/*
|--------------------------------------------------------------------------
| EMITS
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
    'event-click',
    'date-click',
])

/*
|--------------------------------------------------------------------------
| MOUNTED
|--------------------------------------------------------------------------
*/

const mounted = ref(false)

onMounted(() => {
    mounted.value = true
})


/*
|--------------------------------------------------------------------------
| EVENTS
|--------------------------------------------------------------------------
*/

const mappedEvents = computed(() =>
    props.events.map((event) => ({
        id: event.id,

        title: event.company || event.title,

        start: event.start_at,

        end: event.end_at,

        classNames: [
            'interview-event',
            event.source === 'local'
                ? 'event-local'
                : 'event-google',
        ],

        extendedProps: {
            source: event.source,
            company: event.company,
            description: event.description,
            status: event.status,
            type: event.type,
            candidate: event.candidate,
            google_event_id: event.google_event_id,
        },
    }))
)

/*
|--------------------------------------------------------------------------
| CUSTOM EVENT RENDER
|--------------------------------------------------------------------------
*/

const renderEventContent = (arg) => {

    const title =
        arg.event.title.length > 12
            ? arg.event.title.slice(0, 12) + '...'
            : arg.event.title

    return {
        html: `
            <div class="custom-event">



                <div class="custom-event-title">
                    ${title}
                </div>

            </div>
        `
    }
}

/*
|--------------------------------------------------------------------------
| EVENT CLICK
|--------------------------------------------------------------------------
*/

const handleEventClick = (info) => {

    emit('event-click', {
        id: info.event.id,

        title: info.event.title,

        start: info.event.start,

        end: info.event.end,

        ...info.event.extendedProps,
    })
}
const handleDateClick = (info) => {

    emit('date-click', {

        start_at: info.dateStr + 'T09:00',
    })
}

/*
|--------------------------------------------------------------------------
| CALENDAR OPTIONS
|--------------------------------------------------------------------------
*/

const calendarOptions = computed(() => ({

    plugins: [
        dayGridPlugin,
        timeGridPlugin,
        listPlugin,
        interactionPlugin,
    ],

    initialView: 'dayGridMonth',

    headerToolbar: {
        left: 'today prev,next',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek',
    },

    buttonText: {
        today: 'Today',
        dayGridMonth: 'Month',
        timeGridWeek: 'Week',
        listWeek: 'List',
    },

    height: 650,

    fixedWeekCount: true,

    dayMaxEvents: true,

    events: mappedEvents.value,

    /*
    |--------------------------------------------------------------------------
    | CUSTOM RENDERERS
    |--------------------------------------------------------------------------
    */

    eventContent: renderEventContent,

    /*
    |--------------------------------------------------------------------------
    | INTERACTIONS
    |--------------------------------------------------------------------------
    */

    eventClick: handleEventClick,
    dateClick: handleDateClick,

}))
</script>

<template>
    <div
        class="
            rounded-3xl
            border border-white/5
            bg-[#131314]
            p-4
        "
    >

        <FullCalendar
            v-if="mounted"
            :options="calendarOptions"
        />

    </div>
</template>