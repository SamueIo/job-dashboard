<script setup>
defineProps({
    label: String,
    default: 'Continute with google'
})
</script>

<template>
    <div class="text-center">
        <a
            href="/auth/google"
            class="mt-4 w-full border-2 p-2 rounded-2xl flex items-center justify-center gap-2 hover:bg-gray-900"
        >
            <!-- Google icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20">
            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.4 29.3 35 24 35c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 5.1 29.2 3 24 3 12.4 3 3 12.4 3 24s9.4 21 21 21 20.5-8.4 20.5-21c0-1.1-.1-2.2-.3-3.5z"/>
            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 16 18.9 13 24 13c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 5.1 29.2 3 24 3c-7.7 0-14.3 4.4-17.7 10.7z"/>
            <path fill="#4CAF50" d="M24 45c5.2 0 9.9-2 13.6-5.3l-6.3-5.2C29.2 35.9 26.7 37 24 37c-5.3 0-9.7-3.6-11.3-8.5l-6.5 5C9.6 40.6 16.3 45 24 45z"/>
            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1 3-3.4 5.4-6.3 6.5l6.3 5.2C39.9 36.5 44 30.7 44 24c0-1.1-.1-2.2-.4-3.5z"/>
            </svg>

            <span>{{ label }}</span>
        </a>

    </div>
</template>