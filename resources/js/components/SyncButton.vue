<script setup>
const emit = defineEmits([ 'sync'])

defineProps({
    loading: Boolean,
    label: String,
})

const handleClick = () => {
    emit('sync')
}

</script>
<template>
    <button
        @click="handleClick"
        :disabled="loading"
        class=" inline-flex items-center gap-3 rounded-xl border border-black/10 dark:border-white/10 dark:bg-[#0B1120]
         px-2 py-1 text-sm font-medium dark:text-white transition hover:bg-black/5 dark:hover:bg-white/5 disabled:opacity-50"
    >

        <!-- TEXT -->
        <span>
            {{ label }}
        </span>

        <!-- ICON WRAPPER -->
        <div
            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5"
        >
            <svg
                class="h-5 w-5 text-gray-300"
                :class="{ 'animate-spin': loading }"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582M20 20v-5h-.581M5.7 9A7 7 0 0112 5c3.042 0 5.6 1.942 6.7 4.7M18.3 15A7 7 0 0112 19c-3.042 0-5.6-1.942-6.7-4.7"
                />
            </svg>
        </div>

    </button>
</template>