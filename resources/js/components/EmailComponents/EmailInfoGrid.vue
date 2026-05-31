<script setup lang="ts">
import { getDate } from '@/utils/useEmailHelpers';
defineProps<{
    email: any
}>()
</script>

<template>
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
</template>