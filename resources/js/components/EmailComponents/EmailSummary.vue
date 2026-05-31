<script setup lang="ts">
defineProps<{
    summary?: string
}>()
</script>

<template>
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
                    {{ summary || 'No summary available.' }}
                </p>
            </div>

        </div>
</template>