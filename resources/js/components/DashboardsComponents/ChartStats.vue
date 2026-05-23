<script setup>
import { computed } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'

const dashboardStore = useDashboardStore()

const data = computed(() => ({
    applications: dashboardStore.stats.applications ?? 0,
    interviews: dashboardStore.stats.interviews ?? 0,
    offers: dashboardStore.stats.offers ?? 0,
    rejections: dashboardStore.stats.rejections ?? 0,
    other: dashboardStore.stats.other ?? 0,
}))

const total = computed(() =>
    data.value.applications +
    data.value.interviews +
    data.value.offers +
    data.value.rejections +
    data.value.other
)

const applicationsDeg = computed(() =>
    total.value
        ? (data.value.applications / total.value) * 360
        : 0
)

const interviewsDeg = computed(() =>
    total.value
        ? (data.value.interviews / total.value) * 360
        : 0
)

const offersDeg = computed(() =>
    total.value
        ? (data.value.offers / total.value) * 360
        : 0
)

const rejectionsDeg = computed(() =>
    total.value
        ? (data.value.rejections / total.value) * 360
        : 0
)

const chartStyle = computed(() => ({
    background: `
        conic-gradient(
            #6366f1 0deg ${applicationsDeg.value}deg,
            #eab308 ${applicationsDeg.value}deg ${applicationsDeg.value + interviewsDeg.value}deg,
            #22c55e ${applicationsDeg.value + interviewsDeg.value}deg ${applicationsDeg.value + interviewsDeg.value + offersDeg.value}deg,
            #ef4444 ${applicationsDeg.value + interviewsDeg.value + offersDeg.value}deg ${applicationsDeg.value + interviewsDeg.value + offersDeg.value + rejectionsDeg.value}deg,
            #6b7280 ${applicationsDeg.value + interviewsDeg.value + offersDeg.value + rejectionsDeg.value}deg 360deg
        )
    `,
}))
</script>

<template>
    <div class="col-span-3 rounded-3xl border border-white/10  p-6">
        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">
                Application Status Overview
            </h2>

            <button
                class="rounded-xl bg-white/5 px-3 py-2 text-sm text-gray-300"
            >
                This Month
            </button>
        </div>

        <!-- CONTENT -->
        <div class="flex items-center justify-between">

            <!-- DONUT CHART -->
            <div
                class="relative flex h-52 w-52 items-center justify-center rounded-full"
                :style="chartStyle"
            >
                <!-- INNER CIRCLE -->
                <div
                    class="flex h-36 w-36 flex-col items-center justify-center rounded-full bg-[#0B1120]"
                >
                    <div class="text-4xl font-bold text-white">
                        {{ total }}
                    </div>

                    <div class="text-sm text-gray-400">
                        Total
                    </div>
                </div>
            </div>

            <!-- LEGEND -->
            <div class="space-y-4">

                <!-- Applications -->
                <div class="flex items-center justify-between gap-12">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-indigo-500" />

                        <span class="text-sm text-gray-300">
                            Applications
                        </span>
                    </div>

                    <span class="text-sm font-semibold text-white">
                        {{ data.applications }}
                    </span>
                </div>

                <!-- Interviews -->
                <div class="flex items-center justify-between gap-12">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-yellow-500" />

                        <span class="text-sm text-gray-300">
                            Interviews
                        </span>
                    </div>

                    <span class="text-sm font-semibold text-white">
                        {{ data.interviews }}
                    </span>
                </div>

                <!-- Offers -->
                <div class="flex items-center justify-between gap-12">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-green-500" />

                        <span class="text-sm text-gray-300">
                            Offers
                        </span>
                    </div>

                    <span class="text-sm font-semibold text-white">
                        {{ data.offers }}
                    </span>
                </div>

                <!-- Rejections -->
                <div class="flex items-center justify-between gap-12">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-red-500" />

                        <span class="text-sm text-gray-300">
                            Rejections
                        </span>
                    </div>

                    <span class="text-sm font-semibold text-white">
                        {{ data.rejections }}
                    </span>
                </div>

                <!-- Other -->
                <div class="flex items-center justify-between gap-12">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-gray-500" />

                        <span class="text-sm text-gray-300">
                            Other
                        </span>
                    </div>

                    <span class="text-sm font-semibold text-white">
                        {{ data.other }}
                    </span>
                </div>

            </div>

        </div>
    </div>
</template>