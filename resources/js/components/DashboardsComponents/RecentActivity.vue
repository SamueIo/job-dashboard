<script setup>
defineProps({
    recentActivity: {
        type: Array,
        default: () => []
    },
})

const emit = defineEmits(['select'])

const getStatusIcon = (status) => {
    return {
        interview: '📅',
        rejected: '❌',
        offer: '🛡️',
        applied: '🛫',
    }[status] || '✉️'
}

const getStatusBg = (status) => {
    return {
        interview: 'bg-indigo-500/10 text-indigo-400',
        rejected: 'bg-red-500/10 text-red-400',
        offer: 'bg-green-500/10 text-green-400',
        applied: 'bg-yellow-500/10 text-yellow-400',
    }[status] || 'bg-white/5 text-gray-400'
}


</script>

<template>
    <div
        class="  p-6 shadow-[0_0_40px_rgba(0,0,0,0.25)]"
    >
        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">
                Recent Activity
            </h2>

            <button
                class="flex items-center gap-1 text-sm font-medium text-indigo-400 transition hover:text-indigo-300"
            >
                View all
                <span>›</span>
            </button>
        </div>

        <!-- LIST -->
        <div class="space-y-5">
            <div
                v-for="activity in recentActivity"
                :key="activity.id"
                @click="emit('select', activity)"
                class="group flex p-2 rounded-2xl cursor-pointer items-start justify-between hover:border-white/10 hover:bg-white/3"
            >
                <!-- LEFT -->
                <div class="flex items-start gap-4">
                    <!-- ICON -->
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-lg"
                        :class="getStatusBg(activity.status)"
                    >
                        {{ getStatusIcon(activity.status) }}
                    </div>

                    <!-- CONTENT -->
                    <div>
                        <!-- TITLE -->
                        <div
                            class="max-w-65 text-sm font-semibold text-white"
                        >
                            {{ activity.summary }}
                        </div>

                        <!-- SUBTITLE -->
                        <div
                            class="mt-1 text-xs uppercase tracking-wide text-gray-500"
                        >
                            {{ activity.role || activity.company }}
                        </div>
                    </div>
                </div>

                <!-- TIME -->
                <div
                    class="shrink-0 text-xs text-gray-500"
                >
                    {{ new Date(activity.gmail_date).toLocaleString('en-US', {
                        day: 'numeric',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit',
                    }) }}
                </div>
            </div>
        </div>
    </div>
</template>