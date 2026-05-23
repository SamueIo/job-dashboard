import { router } from '@inertiajs/vue3'
import { watch } from 'vue'

let pollingInterval = null

export function useEmailSync() {

    const syncEmails = async () => {
        /*
        |--------------------------------------------------------------------------
        | SYNC EMAILS FROM GMAIL
        |--------------------------------------------------------------------------
        */

        const response = await fetch('/sync-emails', {

            method: 'POST',

            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content') || '',
            },
        })

        if (!response.ok) {
            // console.log('Email sync failed')
            // throw new Error('Email sync failed')
            const text = await response.text()

        }

        /*
        |--------------------------------------------------------------------------
        | CLEAR OLD POLLING
        |--------------------------------------------------------------------------
        */

        if (pollingInterval) {

            clearInterval(pollingInterval)

            pollingInterval = null
        }

        /*
        |--------------------------------------------------------------------------
        | INITIAL RELOAD
        |--------------------------------------------------------------------------
        */

        router.reload({

            only: [
                'emails',
                'stats',
                'processingCount',
            ],

            onSuccess: (page) => {

                /*
                |--------------------------------------------------------------------------
                | NOTHING TO PROCESS
                |--------------------------------------------------------------------------
                */

                if (page.props.processingCount === 0) {
                    return
                }

                /*
                |--------------------------------------------------------------------------
                | START POLLING
                |--------------------------------------------------------------------------
                */

                pollingInterval = setInterval(() => {

                    router.reload({

                        only: [
                            'emails',
                            'stats',
                            'processingCount',
                        ],

                        preserveScroll: true,
                        preserveState: true,

                        onSuccess: (page) => {

                            /*
                            |--------------------------------------------------------------------------
                            | STOP POLLING
                            |--------------------------------------------------------------------------
                            */

                            if (page.props.processingCount === 0) {

                                clearInterval(pollingInterval)

                                pollingInterval = null
                            }
                        },
                    })

                }, 3000)
            },
        })
    }

    const loadMoreMails = (nextPageUrl) => {

        router.get(
            nextPageUrl,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ['emails'],
                replace: true,

                onSuccess: () => {

                    window.history.replaceState(
                        {},
                        '',
                        '/emails'
                    )
                },
            }
        )
    }
    /*
    |--------------------------------------------------------------------------
    | EMAIL SEEN
    |--------------------------------------------------------------------------
    */

    const emailSeen = (email) => {

        watch(
            () => email.value,
            async (currentEmail) => {

                if (
                    !currentEmail ||
                    currentEmail.seen
                ) {
                    return
                }

                try {

                    await fetch(
                        `/emails/${currentEmail.id}/seen`,
                        {
                            method: 'PATCH',

                            headers: {
                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    ?.getAttribute('content') || '',
                            },
                        }
                    )

                    currentEmail.seen = true

                } catch (error) {

                    console.error(
                        'Failed to mark email as seen',
                        error,
                    )
                }
            },
            {
                immediate: true,
            }
        )
    }

    return {
        syncEmails,
        loadMoreMails,
        emailSeen
    }
}