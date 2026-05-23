<script setup>
defineProps({
    upcomingInterviews: {
        type: Array,
        default: () => []
    },
})

const emit = defineEmits(['select'])

const formatDate = (date) => {
    if (!date) return 'No date'

    return new Date(date).toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

const formatTime = (date) => {
    if (!date) return ''

    return new Date(date).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
    })
}

const getCompanyInitial = (company) => {
    if (!company) return '?'

    return company.charAt(0).toUpperCase()
}
</script>

<template>
    <div
        class="rounded-3xl  p-6 shadow-[0_0_40px_rgba(0,0,0,0.25)]"
    >
        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-400"
                >
                    📅
                </div>

                <h2 class="text-lg font-semibold text-white">
                    Upcoming Interviews
                </h2>
            </div>

            <button
                class="text-sm font-medium text-indigo-400 transition hover:text-indigo-300"
            >
                View all
            </button>
        </div>

        <!-- LIST -->
        <div class="space-y-2">
            <div
                v-for="interview in upcomingInterviews"
                :key="interview.id"
                @click="emit('select', interview)"
                class="group flex cursor-pointer items-center justify-between rounded-2xl border border-transparent px-3 py-4 transition hover:border-white/10 hover:bg-white/3"
            >
                <!-- LEFT -->
                <div class="flex items-center gap-4">
                    <!-- COMPANY ICON -->
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-white/5 text-sm font-semibold text-white"
                    >
                        {{ getCompanyInitial(interview.company) }}
                    </div>

                    <!-- INFO -->
                    <div>
                        <div class="text-sm font-semibold text-white">
                            {{ interview.company || 'Unknown company' }}
                        </div>

                        <div class="text-sm text-gray-400">
                            {{ interview.role || 'Unknown role' }}
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="flex items-center gap-8">
                    <!-- DATE -->
                    <div class="hidden text-sm text-gray-400 md:block">
                        <div class="flex items-center gap-2">
                            <span>🗓</span>

                            <span>
                                {{ formatDate(interview.interview_at) }}
                            </span>
                        </div>

                        <div class="mt-1 text-xs text-gray-500">
                            {{ formatTime(interview.interview_at) }}
                        </div>
                    </div>

                    <!-- MEETING TYPE -->
                    <div
                        class="hidden min-w-30 text-sm text-gray-400 lg:block"
                    >
                        {{
                            interview.meeting_link
                                ? 'Google Meet'
                                : 'In-person'
                        }}
                    </div>

                    <!-- STATUS -->
                    <div
                        class="rounded-full bg-green-500 px-3 py-1 text-xs font-medium text-white/80"
                    >
                        Interview
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>