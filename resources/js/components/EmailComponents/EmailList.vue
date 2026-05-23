<script setup>
import { getDate, getIcon, getStatusColor, getPriorityColor, getSenderName } from '@/utils/useEmailHelpers'
import { useEmailSync } from '@/composables/useEmailSync';
import AnalisingSpinner from '@/components/AnalisingSpinner.vue';

defineProps({
    emails: {
        type: Array,
        default: () => [],
    },

    nextPageUrl: String,

    compact: {
        type: Boolean,
        default: false,
    },
})
const emit= defineEmits(['select'])

const { loadMoreMails } = useEmailSync();

</script>

<template>
    <!-- if email is open, this mini emailList shower will apear -->
    <div v-if="compact" class="space-y-3 ">

        <!-- MINI SIDEBAR -->
        <div
            v-for="email in emails"
            :key="email.id"
            @click="$emit('select', email)"
            :class="[
                getPriorityColor(email.priority),
                `group flex cursor-pointer items-center
                justify-start gap-1.5 rounded-2xl
                p-2 transition-all duration-300
                hover:-translate-y-px
                hover:border-indigo-500/30
                hover:bg-white/4

                backdrop-blur-xl
                `
            ]"
        >
                    <!-- ICON -->
                <div
                    class="
                        flex h-14 w-14 shrink-0 items-center justify-center
                        rounded-2xl text-xl
                        transition-transform duration-300
                        group-hover:scale-105
                    "
                    :class="getStatusColor(email.status)"
                >
                    <component
                        :is="getIcon(email.status)"
                        class="h-5 w-5"
                    />
                </div>
                 <!-- COMPANY -->
                <div class="truncate text-lg font-semibold black:text-white">
                    {{ email.company || getSenderName(email.from) }}
                </div>
        </div>

    </div>
    <div v-else class=" rounded-2xl">

        <div
            v-for="email in emails" :key="email.id + email.status"
            @click="emit('select', email)"
            :class="[
                getPriorityColor(email.priority),
                `group grid grid-cols-[1fr_auto] items-center
                gap-6 rounded-2xl p-3 px-4
                transition-all duration-300 
                hover:-translate-y-px
                hover:border-indigo-500/30
                backdrop-blur-xl
                `
            ]"
        >

            <!-- LEFT -->
            <div class="flex min-w-0 items-center gap-3 overflow-hidden">

                <!-- ICON -->
                <div
                    class="
                        flex h-14 w-14 shrink-0 items-center justify-center
                        rounded-2xl text-xl
                        transition-transform duration-300
                        group-hover:scale-105
                    "
                    :class="getStatusColor(email.status)"
                >
                    <component
                        :is="getIcon(email.status)"
                        class="h-5 w-5"
                    />
                </div>

                <!-- CONTENT -->
                <div class="min-w-0 flex-1">

                    <!-- COMPANY -->
                    <div
                        class="
                            truncate
                            text-lg
                            font-semibold
                            tracking-tight
                            text-black/70 dark:text-white/90
                        "
                    >
                        {{ email.company || '(No company)' }}
                    </div>

                    <!-- SUBJECT -->
                    <div class="truncate text-sm font-medium text-gray-400 black:text-grey-300">
                        {{ email.role || email.subject }}
                    </div>

                    <!-- SUMMARY -->
                    <div class="mt-1 truncate text-sm text-gray-600 black:text-grey-500">
                        {{ email.summary || email.snippet }}
                    </div>

                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex min-w-[110px] flex-col items-end gap-2">

                <!-- STATUS -->
                <div
                    class="
                        rounded-full
                        px-3
                        py-1
                        text-xs
                        font-semibold
                        uppercase
                        tracking-wide
                    "
                    :class="getStatusColor(email.status)"
                >

                    <AnalisingSpinner
                        v-if="email.ai_status === 'processing'"
                        text="Analyzing"
                    />
                    <span v-else-if="email.ai_status === 'failed'"
                        class="text-red-400"
                    >
                        failed
                    </span>

                    <span v-else-if="email.status">
                        {{ email.status }}
                    </span>

                    <span v-else>
                        pending
                    </span>
                    


                </div>

                <!-- CONFIDENCE -->
                <div class="text-sm font-semibold text-emerald-400">
                    {{ Math.round((email.confidence || 0) * 100) }}%
                </div>

                <!-- DATE -->
                <div class="text-sm text-gray-500">
                    {{ getDate(email.gmail_date) }}
                </div>

            </div>

        </div>

        <!-- LOAD MORE -->
        <button
            v-if="nextPageUrl"
            @click="loadMoreMails(nextPageUrl)"
            class="
                flex w-full items-center justify-center gap-2
                rounded-2xl
                border border-white/5
                py-4
                text-sm font-medium text-gray-400
                transition-all duration-300
                hover:border-indigo-500/20
                hover:bg-white/3
                black:hover:text-white
                hover:text-black

            "
        >
            Load more
        </button>

    </div>
</template>