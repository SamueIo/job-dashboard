<script setup>
import { computed } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'

import {
    Folder,
    Calendar,
    Mail,
    TrendingUp,
} from 'lucide-vue-next'

const dashboardStore = useDashboardStore()

const cards = computed(() => [
    {
        title: 'Applications',
        value: dashboardStore.stats.applications,
        trend: dashboardStore.stats.applications_trend,
        icon: Folder,
        color: 'indigo',
    },

    {
        title: 'Interviews',
        value: dashboardStore.stats.interviews,
        trend: dashboardStore.stats.interviews_trend,
        icon: Calendar,
        color: 'blue',
    },

    {
        title: 'Responses',
        value: dashboardStore.stats.responses,
        trend: dashboardStore.stats.responses_trend,
        icon: Mail,
        color: 'emerald',
    },

    {
        title: 'Success Rate',
        value: `${dashboardStore.stats.social_rate}%`,
        trend: dashboardStore.stats.social_rate_trend,
        icon: TrendingUp,
        color: 'fuchsia',
    },
])

const styles = {
    indigo: {
        glow: 'bg-indigo-500/10',
        icon: 'from-indigo-500/30 to-violet-500/10',
        iconColor: 'text-indigo-300',
        stroke: '#8B5CF6',
    },

    blue: {
        glow: 'bg-blue-500/10',
        icon: 'from-blue-500/30 to-cyan-500/10',
        iconColor: 'text-blue-300',
        stroke: '#3B82F6',
    },

    emerald: {
        glow: 'bg-emerald-500/10',
        icon: 'from-emerald-500/30 to-green-500/10',
        iconColor: 'text-emerald-300',
        stroke: '#22C55E',
    },

    fuchsia: {
        glow: 'bg-fuchsia-500/10',
        icon: 'from-fuchsia-500/30 to-pink-500/10',
        iconColor: 'text-fuchsia-300',
        stroke: '#D946EF',
    },
}
</script>

<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

        <div
            v-for="card in cards"
            :key="card.title"
            class="
                relative overflow-hidden

                rounded-[18px]

                border border-black/5
                bg-white/60

                p-3

                shadow-sm

                dark:border-[#1B2440]
                dark:bg-[#081120]
            "
        >
            <!-- GLOW -->
            <div
                :class="[
                    'absolute -right-10 -top-10 h-32 w-32 rounded-full blur-3xl',
                    styles[card.color].glow
                ]"
            />

            <div class="relative flex h-full flex-col z-10">

                <!-- TOP -->
                <div class="flex items-start gap-4">

                    <!-- ICON -->
                    <div
                        :class="[
                            `
                            flex h-14 w-14 items-center justify-center
                            rounded-2xl
                            bg-linear-to-br
                            `,
                            styles[card.color].icon
                        ]"
                    >
                        <!-- ICON -->
                        <component
                            :is="card.icon"
                            :class="[
                                'h-6 w-6',
                                styles[card.color].iconColor
                            ]"
                        />
                    </div>

                    <!-- TEXT -->
                    <div>
                        <div
                            class="
                                text-sm

                                text-gray-500
                                dark:text-gray-400
                            "
                        >
                            {{ card.title }}
                        </div>

                        <div
                            class="
                                mt-1

                                text-2xl
                                font-semibold

                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ card.value }}
                        </div>
                    </div>

                </div>

                <!-- BOTTOM -->
                <div class=" flex items-end justify-between">

                    <div
                        class="
                            text-sm

                            text-emerald-500
                            dark:text-emerald-400
                        "
                    >
                        ↑ {{ card.trend }}% from last 30 days
                    </div>

                    <!-- WAVE -->
                    <svg
                        width="74"
                        height="30"
                        viewBox="0 0 74 30"
                        fill="none"
                        class="opacity-90"
                    >
                        <path
                            d="M2 24C10 24 14 8 24 8C34 8 36 20 46 20C56 20 60 4 72 4"
                            :stroke="styles[card.color].stroke"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />
                    </svg>

                </div>

            </div>
        </div>

    </div>
</template>