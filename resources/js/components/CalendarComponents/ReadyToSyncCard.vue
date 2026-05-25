<script setup>
import { router } from '@inertiajs/vue3'
const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
})


const syncEvent = (id) => {

    router.post(
        `/calendar/events/${id}/sync`
    )
}

const syncAll = () => {

    router.post(
        '/calendar/events/sync-all'
    )
}

const formatDate = (date) => {

    return new Date(date).toLocaleString('en-US', {

        month: 'short',
        day: 'numeric',

        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>

<template>

    <div>

        <!-- HEADER -->
        <div
            class="
                mb-4
                flex items-center justify-between
            "
        >

            <div>

                <p
                    class="
                        text-sm
                        font-medium
                        text-white
                    "
                >
                    Ready to sync with google
                </p>
                    
                <p
                    class="
                        mt-1
                        text-xs
                        text-white/40
                    "
                >
                    Events not synced
                </p>

            </div>

            <div
                class="
                    rounded-full
                    border border-white/10
                    bg-white/3

                    px-2.5 py-1

                    text-xs
                    text-white/60
                "
            >
                {{ events.length }}
            </div>

        </div>

        <!-- EMPTY -->
        <div
            v-if="!events.length"
            class="
                rounded-2xl
                border border-white/5
                bg-white/2

                px-4 py-6

                text-center
                text-sm
                text-white/40
            "
        >
            Everything is synced.
        </div>

        <!-- LIST -->
        <div
            v-else
            class="space-y-3"
        >

            <div
                v-for="event in events"
                :key="event.id"
                class="
                    rounded-2xl

                    border border-white/5

                    bg-white/2

                    p-4
                "
            >

                <div
                    class="
                        flex items-start
                        justify-between
                        gap-4
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm
                                font-medium
                                text-white
                            "
                        >
                            {{ event.company || event.title }}
                        </p>

                        <p
                            class="
                                mt-1
                                text-xs
                                text-white/40
                            "
                        >
                            {{ formatDate(event.start_at) }}
                        </p>

                    </div>

                    <button
                        @click="syncEvent(event.id)"
                        class="
                            rounded-xl

                            border border-white/10

                            bg-white/3

                            px-3 py-2

                            text-xs
                            font-medium
                            text-white

                            transition

                            hover:bg-white/6
                        "
                    >
                        Sync
                    </button>

                </div>

            </div>

        </div>

    </div>

</template>