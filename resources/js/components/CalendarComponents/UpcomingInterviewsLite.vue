<script setup>
import {
    Calendar,
} from 'lucide-vue-next'

defineProps({
    upcomingInterviews: {
        type: Object,
        default: () => [],
    },
})

const colors = {
    Google: {
        bg: 'bg-purple-500/10',
        icon: 'text-purple-400',
        badge: 'bg-purple-500/15 text-purple-300',
    },

    Amazon: {
        bg: 'bg-orange-500/10',
        icon: 'text-orange-400',
        badge: 'bg-orange-500/15 text-orange-300',
    },

    Spotify: {
        bg: 'bg-green-500/10',
        icon: 'text-green-400',
        badge: 'bg-green-500/15 text-green-300',
    },

    Meta: {
        bg: 'bg-red-500/10',
        icon: 'text-red-400',
        badge: 'bg-red-500/15 text-red-300',
    },

    Airbnb: {
        bg: 'bg-indigo-500/10',
        icon: 'text-indigo-400',
        badge: 'bg-indigo-500/15 text-indigo-300',
    },
}
</script>

<template>

    <div>

        <!-- HEADER -->
        <div class="mb-5 flex items-center justify-between">

            <h2
                class="
                    text-lg
                    font-semibold
                    text-white
                "
            >
                Upcoming Interviews
            </h2>

            <button
                class="
                    text-sm
                    text-blue-400

                    hover:text-blue-300
                "
            >
                View all
            </button>

        </div>

        <!-- LIST -->
        <div class="space-y-3">
                <div v-if="!upcomingInterviews?.length">
                    <p>No upcoming interviews</p>
                </div >
            <div v-else
                v-for="interview in upcomingInterviews"
                :key="interview.id"
                class="
                    flex items-center gap-3

                    rounded-2xl

                    border border-white/5

                    bg-white/[0.02]

                    px-3 py-3

                    transition-all duration-200

                    hover:bg-white/[0.04]
                "
            >

                <!-- ICON -->
                <div
                    :class="[
                        'flex h-11 w-11 items-center justify-center rounded-xl',
                        colors[interview.company]?.bg,
                    ]"
                >

                    <Calendar
                        :class="[
                            'h-5 w-5',
                            colors[interview.company]?.icon,
                        ]"
                    />

                </div>

                <!-- CONTENT -->
                <div class="min-w-0 flex-1">

                    <div
                        class="
                            mb-1

                            text-xs

                            text-white/50
                        "
                    >
                        {{ new Date(interview.start_at).toLocaleString() }}
                    </div>

                    <div
                        class="
                            truncate

                            text-sm
                            font-semibold

                            text-white
                        "
                    >
                        {{ interview.company || interview.title }}
                    </div>

                    <div
                        class="
                            truncate

                            text-xs

                            text-white/55
                        "
                    >
                        {{ interview.position }}
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="flex items-center gap-3">

                    <!-- BADGE -->
                    <div
                        :class="[
                            'rounded-full px-3 py-1 text-[11px] font-semibold tracking-wide',
                            colors[interview.company]?.badge,
                        ]"
                    >
                        INTERVIEW
                    </div>

                    <!-- DOT -->
                    <div
                        class="
                            h-2 w-2

                            rounded-full

                            bg-blue-500
                        "
                    />

                </div>

            </div>

        </div>

    </div>
</template>