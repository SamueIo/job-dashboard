<script setup >
import { Head } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { ref, onMounted, onUnmounted } from 'vue';
import UpcomingInterviews from '@/components/DashboardsComponents/UpcomingInterviews.vue';
import RecentActivity from '@/components/DashboardsComponents/RecentActivity.vue';
import EmailShow from '@/components/DashboardsComponents/EmailShow.vue';
import StatsGrid from '@/components/DashboardsComponents/StatsGrid.vue';
import { useEmailSync } from '@/composables/useEmailSync'
import { useDashboardStore } from '@/stores/dashboard';
import ChartStats from '@/components/DashboardsComponents/ChartStats.vue';


defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps({
    upcomingInterviews: Array,
    recentActivity: Array,
    stats: Object,
    auth: Object,
    errors: Object,
    name: String,
    sidebarOpen: Boolean
})
const dashboardStore = useDashboardStore()
dashboardStore.setStats(props.stats)

const selectedEmail = ref(null)
const { syncEmails } = useEmailSync()

// Reload button from emailist component 
const loading = ref(false)

let interval = null

onMounted(async () => {

    await syncEmails()

})

onUnmounted(() => {
    clearInterval(interval)
})
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">

        <!-- STATS -->
        <StatsGrid v-if="!selectedEmail"/>

        <!-- TOP GRID -->
        <div v-if="recentActivity && !selectedEmail"
            class="grid grid-cols-5 gap-6">

            <!-- UPCOMING INTERVIEWS -->
            <div class="col-span-3 border rounded-2xl ">
                <UpcomingInterviews
                    :upcomingInterviews="upcomingInterviews"
                    @select="selectedEmail = $event"
                />
            </div>
            <div class="col-span-2 border rounded-2xl  max-h-80 overflow-hidden overflow-y-auto no-scrollbar">
                <RecentActivity
                    :recentActivity="recentActivity"
                    @select="selectedEmail = $event"
                />
            </div>


        </div>

        <!-- BOTTOM GRID -->
        <div v-if="upcomingInterviews && !selectedEmail" class="grid grid-cols-5 gap-6">

            <!-- APPLICATION STATUS -->
            <ChartStats/>

            <!-- AI INSIGHTS -->
            <div
                class="col-span-2 rounded-3xl border border-white/10 bg-[#0B1120] p-6"
            >
                <!-- HEADER -->
                <h2 class="mb-6 text-lg font-semibold text-white">
                    AI Insights
                </h2>

                <!-- INSIGHT CARD -->
                <div
                    class="rounded-2xl bg-white/3 p-4"
                >
                    <div class="text-sm text-gray-400">
                        Highest response rate
                    </div>

                    <div class="mt-2 text-lg font-semibold text-white">
                        Aerospace companies
                    </div>

                    <div class="mt-1 text-sm text-green-400">
                        34% response rate
                    </div>
                </div>

                <!-- INSIGHT CARD -->
                <div
                    class="mt-4 rounded-2xl bg-white/3 p-4"
                >
                    <div class="text-sm text-gray-400">
                        Average interview time
                    </div>

                    <div class="mt-2 text-lg font-semibold text-white">
                        5.2 days
                    </div>

                    <div class="mt-1 text-sm text-green-400">
                        -1.3 days vs last month
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="mt-6 border-t border-white/5 pt-4">
                    <button
                        class="flex items-center gap-2 text-sm font-medium text-indigo-400 hover:text-indigo-300"
                    >
                        View all insights
                        <span>→</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- EMAIL MODAL -->
        <EmailShow
            v-if="selectedEmail"
            :email="selectedEmail"
            @closeEmail="selectedEmail = null"
        />

    </div>
</template>
