<script setup>
const props = defineProps({
    cards: {
        type: Array,
        default: () => [],
    },
})

const styles = {
    indigo: {
        glow: 'bg-indigo-500/20 dark:bg-indigo-500/10',
        icon: 'from-indigo-500/20 to-violet-500/20 dark:from-indigo-500/30 dark:to-violet-500/10',
        iconColor: 'text-indigo-600 dark:text-indigo-300',
        stroke: '#8B5CF6',
    },

    blue: {
        glow: 'bg-blue-500/20 dark:bg-blue-500/10',
        icon: 'from-blue-500/20 to-cyan-500/20 dark:from-blue-500/30 dark:to-cyan-500/10',
        iconColor: 'text-blue-600 dark:text-blue-300',
        stroke: '#3B82F6',
    },

    emerald: {
        glow: 'bg-emerald-500/20 dark:bg-emerald-500/10',
        icon: 'from-emerald-500/20 to-green-500/20 dark:from-emerald-500/30 dark:to-green-500/10',
        iconColor: 'text-emerald-600 dark:text-emerald-300',
        stroke: '#22C55E',
    },

    fuchsia: {
        glow: 'bg-fuchsia-500/20 dark:bg-fuchsia-500/10',
        icon: 'from-fuchsia-500/20 to-pink-500/20 dark:from-fuchsia-500/30 dark:to-pink-500/10',
        iconColor: 'text-fuchsia-600 dark:text-fuchsia-300',
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

                border border-gray-200
                bg-white

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

            <div class="relative z-10 flex h-full flex-col">

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

                                text-gray-600
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

                        <div
                            v-if="card.subtitle"
                            class="
                                mt-1
                                text-sm

                                text-gray-500
                                dark:text-gray-400
                            "
                        >
                            {{ card.subtitle }}
                        </div>
                    </div>

                </div>

                <!-- BOTTOM -->
                <div class="mt-auto flex items-end justify-between">

                    <div
                        class="
                            text-sm

                            text-emerald-600
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