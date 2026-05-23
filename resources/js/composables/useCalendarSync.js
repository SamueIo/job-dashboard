import { ref } from 'vue'

import { router } from '@inertiajs/vue3'

export function useCalendarSync()
{
    const loading = ref(false)

    const syncCalendar = () => {

        loading.value = true

        router.post(
            '/calendar/sync',
            {},
            {
                preserveScroll: true,

                onFinish: () => {

                    loading.value = false
                },
            }
        )
    }

    return {

        loading,

        syncCalendar,
    }
}