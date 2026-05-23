<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

import StatsGrid from '@/components/DashboardsComponents/StatsGrid.vue'
import EmailList from '@/components/EmailComponents/EmailList.vue'
import EmailShow from '@/components/DashboardsComponents/EmailShow.vue'
import EmailFilters from '@/components/EmailComponents/EmailFilters.vue'

const selectedEmail = ref(null)

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Emails',
                href: '/emails',
            },
        ],
    },
})

const props = defineProps({
    emails: Object,
    statuses: Array,
    companies: Array,
    filters: Object,
    statusCounts: Object
})

const localEmails = ref([])

watch(
    () => props.emails,

    (newEmails) => {

        if (!newEmails) return

        if (newEmails.current_page === 1) {

            localEmails.value = newEmails.data

            return
        }

        localEmails.value.push(
            ...newEmails.data
        )
    },

    {
        immediate: true,
    }
)
</script>

<template>
    <Head title="Emails" />
    <div class="pt-4 px-6 gap-2 ">
        <StatsGrid />
    </div>
    <div class="flex flex-col h-screen overflow-hidden gap-2 p-4 pt-0 ">


        <EmailFilters
            :statuses="statuses"
            :companies="companies"
            :statusCounts="statusCounts"
            :filters="filters"
        />

        <!-- CONTENT -->
        <div class="flex flex-1 min-h-0 gap-4 overflow-hidden">

            <!-- EMAIL LIST -->
            <div
                :class="selectedEmail
                    ? 'w-80'
                    : 'w-full'"
                class="overflow-y-auto min-h-0 shrink-0 no-scrollbar"
            >
                <EmailList
                    :compact="!!selectedEmail"
                    :emails="localEmails"
                    :next-page-url="props.emails.next_page_url"
                    @select="selectedEmail = $event"
                />
            </div>

            <!-- EMAIL SHOW -->
            <div
                v-if="selectedEmail"
                class="flex-1 overflow-y-auto min-h-0 no-scrollbar             
                rounded-3xl border border-black/5 bg-white
                shadow-sm
                dark:border-white/5
                dark:bg-[#0b1120]"
                >
                <EmailShow
                    :email="selectedEmail"
                    @closeEmail="selectedEmail = null"
                />
            </div>

        </div>

    </div>
</template>