<script setup>
import { getDate, getTime, getStatusColor } from '@/utils/useEmailHelpers'
import { toRef } from 'vue'
import { useEmailSync } from '@/composables/useEmailSync'

const props = defineProps({
    email: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['closeEmail'])
const { emailSeen } = useEmailSync()
emailSeen(
    toRef(props, 'email')
)
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
                p-6

                dark:border-white/5
            "
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
                            To: {{ email.from }}
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
        <div class="grid grid-cols-2 gap-6 p-6">

            <!-- ORIGINAL -->
            <div
                class="
                    rounded-2xl

                    border border-black/5
                    bg-black/[0.02]

                    p-5

                    dark:border-white/5
                    dark:bg-white/[0.02]
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
                        View full email
                    </a>

                </div>

            </div>

            <!-- PREVIEW -->
            <div
                class="
                    rounded-2xl

                    border border-black/5
                    bg-black/[0.02]

                    p-5

                    dark:border-white/5
                    dark:bg-white/[0.02]
                "
            >

                <h3
                    class="
                        mb-5 font-semibold

                        text-gray-900
                        dark:text-white
                    "
                >
                    Email Preview
                </h3>

                <div
                    class="
                        line-clamp-12
                        whitespace-pre-line
                        text-sm leading-7

                        text-gray-700
                        dark:text-gray-300
                    "
                >
                    {{ email.body || email.snippet }}
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

</template>