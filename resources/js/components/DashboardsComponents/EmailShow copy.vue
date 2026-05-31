<script setup>
import { getDate, getTime, getStatusColor } from '@/utils/useEmailHelpers'
import { toRef, ref } from 'vue'
import { useEmailSync } from '@/composables/useEmailSync'
import CreateEventModal from '../CalendarComponents/CreateEventModal.vue'

const props = defineProps({
    email: {
        type: Object,
        default: null,
    },
})



const emit = defineEmits(['closeEmail'])
const { emailSeen } = useEmailSync()

const showCreateEventModal = ref(false)
const eventPrefill = ref(null)
const scheduleFromEmail = () => {

    eventPrefill.value = {

        email_id: props.email.id,

        title: props.email.subject,

        description: props.email.summary,

        start_at: props.email.detected_date,

        end_at: props.email.detected_end_date,

        type: props.status || 'meeting',
    }

    showCreateEventModal.value = true
}

emailSeen(
    toRef(props, 'email')
)

const getSenderName = (from) => {

    const match = from.match(/"?([^"<]+)"?\s*</)

    return match
        ? match[1].trim()
        : from
}
</script>

<template>

    <div
        v-if="email"
        class="
            overflow-hidden "
    >

        <!-- HEADER -->
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
                                @click="scheduleFromEmail"
                                    :class="[
                                    'inline-flex  items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium shadow-sm transition-all duration-200',
                                        props.email.event
                                            ? 'dark:hover:bg-slate-800 hover:bg-slate-800'
                                            : 'bg-white text-slate-700 border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300',

                                        'dark:border-slate-700 dark:text-slate-200',
                                        !props.email.event ?  'dark:bg-slate-800 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300' : ''
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

                                    {{ props.email.event ? 'Already scheduled' : 'Schedule' }}
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

        <!-- AI SUMMARY -->
        <div
            class="
                border-b border-black/5
                p-6

                dark:border-white/5
            "
        >

            <div class="mb-4 flex items-center gap-2">

                <div
                    class="
                        flex h-8 w-8 items-center justify-center
                        rounded-lg

                        bg-indigo-500/10
                        text-indigo-500

                        dark:text-indigo-400
                    "
                >
                    ✨
                </div>

                <h2
                    class="
                        font-semibold

                        text-gray-900
                        dark:text-white
                    "
                >
                    AI Summary
                </h2>

            </div>

            <div
                class="
                    space-y-2

                    text-gray-700
                    dark:text-gray-300
                "
            >
                <p>
                    {{ email.summary || 'No summary available.' }}
                </p>
            </div>

        </div>

        <!-- INFO GRID -->
        <div
            class="
                grid grid-cols-2

                border-b border-black/5

                dark:border-white/5
            "
        >

            <!-- LEFT -->
            <div
                class="
                    border-r border-black/5
                    p-6

                    dark:border-white/5
                "
            >

                <h3
                    class="
                        mb-5 font-semibold

                        text-gray-900
                        dark:text-white
                    "
                >
                    Key Information
                </h3>

                <div class="space-y-4 text-sm">

                    <div class="flex justify-between">
                        <span
                            class="
                                text-gray-500
                            "
                        >
                            Type
                        </span>

                        <span
                            class="
                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ email.status }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Date
                        </span>

                        <span
                            class="
                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ getDate(email.interview_at) }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Company
                        </span>

                        <span
                            class="
                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ email.company || 'Unknown' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Confidence
                        </span>
                        <span
                            class="
                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ Math.round((email.confidence || 0) * 100) }}%
                        </span>
                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="p-6">

                <h3
                    class="
                        mb-5 font-semibold

                        text-gray-900
                        dark:text-white
                    "
                >
                    Next Steps
                </h3>

                <div
                    class="
                        space-y-3 text-sm

                        text-gray-700
                        dark:text-gray-300
                    "
                >

                    <div class="flex items-center gap-3">
                        <span class="text-green-500 dark:text-green-400">✓</span>
                        Review the email carefully
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-green-500 dark:text-green-400">✓</span>
                        Prepare response if needed
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-green-500 dark:text-green-400">✓</span>
                        Research the company
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-green-500 dark:text-green-400">✓</span>
                        Save important information
                    </div>

                </div>

            </div>

        </div>

        <!-- EMAIL CONTENT -->
        <div class="flex flex-col">



            <!-- PREVIEW -->
            <div
                class="

                    border border-black/5
                    bg-black/2
                    px-5

                    dark:border-white/5
                    dark:bg-white/2
                "
            >
                <div class="flex py-2 justify-between">
                    <h3
                        class="
                            font-semibold
                            py-2
                            text-gray-900
                            dark:text-white
                        "
                    >
                        Email Preview
                    </h3>
                        <a
                            :href="`https://mail.google.com/mail/u/0/#inbox/${email.gmail_id}`"
                            target="_blank"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200
                                         bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition-all
                                         duration-200 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600
                                         dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200
                                         dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300"
                        >
                            Open in gmail
                        </a>

                </div>


            <div
                class="
                    email-preview
                    prose prose-sm max-w-none
                    text-gray-700
                    dark:prose-invert
                    dark:text-gray-300
                "
                v-html="email.body || email.snippet"
            />

            </div>
                        <!-- ORIGINAL -->
            <div
                class="
                    border border-black/5
                    bg-black/2

                    p-5

                    dark:border-white/5
                    dark:bg-white/2
                "
            >

                <h3
                    class="
                        mb-5 font-semibold

                        text-gray-900
                        dark:text-white
                    "
                >
                    Original Email
                </h3>

                <div class="space-y-4 text-sm">

                    <div>
                        <div class="text-gray-500">
                            From
                        </div>

                        <div
                            class="
                                mt-1

                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ email.from }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">
                            Subject
                        </div>

                        <div
                            class="
                                mt-1

                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ email.subject }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">
                            Date
                        </div>

                        <div
                            class="
                                mt-1

                                text-gray-900
                                dark:text-white
                            "
                        >
                            {{ getDate(email.updated_at) }}
                        </div>
                    </div>

                    <a
                        :href="`https://mail.google.com/mail/u/0/#inbox/${email.gmail_id}`"
                        target="_blank"
                        class="
                            mt-4 inline-flex rounded-xl

                            bg-black/[0.04]
                            px-4 py-3

                            text-gray-700

                            transition
                            hover:bg-black/[0.06]

                            dark:bg-white/10
                            dark:text-white
                            dark:hover:bg-white/20
                        "
                    >
                        Open in gmail
                    </a>

                </div>

            </div>
        </div>

    </div>

    <!-- EMPTY -->
    <div
        v-else
        class="
            flex h-full items-center justify-center
            rounded-3xl

            border border-black/5

            text-gray-500

            dark:border-white/5
        "
    >
        Select an email
    </div>

    <CreateEventModal
        :show="showCreateEventModal"
        :prefill="eventPrefill"
        :event="props.email.event ? props.email.event : ''"
        @close="showCreateEventModal = false"
    />

</template>