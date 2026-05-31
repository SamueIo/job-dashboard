<script setup lang="ts">
defineProps<{
    email: any
}>()
</script>

<template>
            <div
                class="

                    border border-black/5
                    bg-black/2
                    px-5
                    pb-5

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
</template>