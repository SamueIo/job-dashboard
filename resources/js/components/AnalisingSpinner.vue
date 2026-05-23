<script setup>
defineProps({
    text: {
        type: String,
        default: '',
    },
})
</script>

<template>
    <div
        class="
            flex items-center gap-1

            text-sm text-gray-400
        "
    >

        <span v-if="text">
            {{ text }}
        </span>

        <span class="flex">

            <span
                class="
                    animate-bounce
                    [animation-delay:0ms]
                "
            >
                .
            </span>

            <span
                class="
                    animate-bounce
                    [animation-delay:150ms]
                "
            >
                .
            </span>

            <span
                class="
                    animate-bounce
                    [animation-delay:300ms]
                "
            >
                .
            </span>

        </span>

    </div>
</template>