<script setup>
import { router } from '@inertiajs/vue3'
const props = defineProps({
    show: Boolean,
    event: Object,
})


const emit = defineEmits([
    'close',
    'editEvent',
    'deleted',
])

const hideEvent = () => {
    router.patch(`/calendar/events/${props.event.id}/hide`, {}, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            emit('close')
            
        }
    })
}

const forceDeleteEvent = () => {
    if (!confirm('Delete this event from Google Calendar?')) {
        return
    }

    router.delete(
        `/calendar/events/${props.event.id}/force`,
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                emit('close')
                emit('deleted', props.event.id)
            }
        }
    )
}
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
    >
        <div
            v-if="show"
            class="
                fixed inset-0 z-50
                bg-black/60
                backdrop-blur-sm
            "
        >

            <div
                class="
                    absolute right-0 top-0

                    h-full
                    w-full
                    max-w-md

                    border-l border-white/10

                    bg-[#0b0b0b]

                    p-6
                "
            >

                <!-- HEADER -->
                <div
                    class="
                        mb-8
                        flex items-start justify-between
                    "
                >

                    <div>
                        <p class="text-[11px] uppercase tracking-[0.18em] text-white/40
                            "
                        >
                            Info
                        </p>

                        <h2
                            class="
                                mt-2
                                text-xl
                                font-semibold
                                tracking-tight
                                text-white
                            "
                        >
                            {{ props.event?.title }}
                        </h2>

                    </div>

                    <button
                        @click="emit('close')"
                        class="
                            rounded-xl

                            border border-white/10

                            bg-white/3

                            px-3 py-2

                            text-sm
                            text-white/70
                        "
                    >
                        Close
                    </button>

                </div>

                <!-- CONTENT -->
                <div class="space-y-5">

                    <div>
                        <p class="drawer-label  text-white/40">
                            Company
                        </p>

                        <div class="drawer-card">
                            {{ props.event?.company || 'Unknown' }}
                        </div>
                    </div>

                    <div>
                        <p class="drawer-label  text-white/40">
                            Notes
                        </p>

                        <div
                            class="
                                min-h-35

                                rounded-2xl

                                border border-white/10

                                bg-white/3

                                px-4 py-3

                                text-sm
                                leading-relaxed

                                text-white/70
                            "
                        >
                            {{ props.event?.description || 'No notes yet.' }}
                        </div>
                    </div>

                </div>

                <!-- buttons -->
                <div class="flex gap-3 mt-3">
                    <button
                        @click="emit('editEvent', props.event)"
                        class="
                            flex-1
                            rounded-2xl
                            border border-indigo-500/20
                            bg-indigo-500/10
                            px-4 py-3
                            text-sm
                            font-medium
                            text-indigo-200
                            transition-all duration-200
                            hover:bg-indigo-500/15
                        "
                    >
                        Edit Event
                    </button>

                    <button
                        @click="hideEvent"
                        class="
                            flex-1
                            rounded-2xl
                            border border-neutral-700
                            bg-neutral-800
                            px-4 py-3
                            text-sm
                            font-medium
                            text-neutral-300
                            transition-all duration-200
                            hover:bg-neutral-700
                        "
                    >
                        Hide
                    </button>
                    <button
                        v-if="props.event.source != 'local' || props.event.google_event_id"
                        @click="forceDeleteEvent"
                        class="
                            flex-1
                            rounded-2xl
                            border border-red-500/15
                            bg-red-500/10
                            px-4 py-3
                            text-sm
                            font-medium
                            text-red-200
                            transition-all duration-200
                            hover:bg-red-500/15
                        "
                    >
                        Delete Everywhere
                    </button>
                </div>

            </div>

        </div>
    </Transition>
</template>