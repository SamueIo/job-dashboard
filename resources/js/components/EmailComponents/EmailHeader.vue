<script setup lang="ts">
import { getDate, getTime, getStatusColor } from '@/utils/useEmailHelpers'

defineProps<{
    email: any
}>()

const emit = defineEmits<{
    schedule: []
    closeEmail: []
}>()

const getSenderName = (from: string) => {
    const match = from.match(/"?([^"<]+)"?\s*</)

    return match
        ? match[1].trim()
        : from
}

</script>

<template>
            <div
            class="
                border-b border-black/5
                p-6 dark:border-white/5 "
        >

            <div class="flex items-start justify-between">

                <!-- LEFT -->
                <div class="flex items-start gap-4">

                    <!-- ICON -->
                    <div
                        class="
                            flex h-16 w-16 items-center justify-center
                            rounded-2xl

                            bg-indigo-500

                            text-2xl font-bold text-white

                            shadow-[0_0_30px_rgba(99,102,241,0.25)]
                        "
                    >
                        {{ email.company?.charAt(0) || '?' }}
                    </div>

                    <!-- INFO -->
                    <div>

                        <h1
                            class="
                                text-3xl font-bold

                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ email.company || '(No company)' }}
                        </h1>

                        <div
                            class="
                                mt-1

                                text-gray-500
                                dark:text-gray-400
                            "
                        >
                            {{ email.subject }}
                        </div>

                        <!-- STATUS -->
                        <div class="mt-4 flex items-center gap-3">
                            
                            <div
                                class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide"
                                :class="getStatusColor(email.status)"
                            >
                                {{ email.status }}
                            </div>

                            <div
                                class="
                                    text-sm font-semibold

                                    text-emerald-500
                                    dark:text-emerald-400
                                "
                            >
                                {{ Math.round((email.confidence || 0) * 100) }}% confidence
                            </div>

                            <button
                                @click="emit('schedule')"
                                    :class="[
                                    'inline-flex  items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium shadow-sm transition-all duration-200',
                                        email.event
                                            ? 'dark:hover:bg-slate-800 hover:bg-slate-800'
                                            : 'bg-white text-slate-700 border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300',

                                        'dark:border-slate-700 dark:text-slate-200',
                                        !email.event ?  'dark:bg-slate-800 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300' : ''
                                    ]"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10m-13 9h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v11a2 2 0 002 2z"
                                        />
                                    </svg>

                                    {{ email.event ? 'Already scheduled' : 'Schedule' }}
                            </button>
                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="flex items-start gap-6">

                    <div class="text-right">

                        <div
                            class="
                                text-sm

                                text-gray-500
                                dark:text-gray-400
                            "
                        >
                            {{ getDate(email.interview_at || email.updated_at) }}
                            {{ getTime(email.interview_at || email.updated_at) }}
                        </div>

                        <div
                            class="
                                mt-3 text-sm

                                text-gray-400
                                dark:text-gray-500
                            "
                        >
                            From: {{ getSenderName(email.from) }}
                        </div>

                    </div>

                    <!-- CLOSE -->
                    <button
                        @click="emit('closeEmail')"
                        class="
                            text-gray-400
                            transition

                            hover:text-gray-900

                            dark:text-gray-500
                            dark:hover:text-white
                        "
                    >
                        ✕
                    </button>

                </div>

            </div>

        </div>
</template>